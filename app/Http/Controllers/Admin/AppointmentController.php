<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        $appointments = Appointment::with(['patient', 'service'])
            ->when($date !== 'all', fn ($q) => $q->whereDate('appointment_date', $date))
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, function ($q, $s) {
                $q->where(fn ($w) => $w->where('code', 'like', "%$s%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%$s%")->orWhere('phone', 'like', "%$s%")->orWhere('medical_record_no', 'like', "%$s%")));
            })
            ->orderByDesc('appointment_date')->orderBy('queue_number')
            ->paginate(20)->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'selectedDate' => $date,
            'services' => Service::active()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.appointments.create', [
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'medical_record_no', 'phone']),
            'services' => Service::active()->orderBy('name')->get(),
            'selectedPatient' => $request->patient_id,
        ]);
    }

    /** Simpan antrean di tempat (bisa pilih pasien lama ATAU langsung daftar pasien baru sekaligus) */
    public function store(Request $request)
    {
        $mode = $request->input('patient_mode', 'existing');

        if ($mode === 'new') {
            $data = $request->validate([
                'name' => 'required|string|max:100',
                'phone' => 'required|string|max:20',
                'gender' => 'required|in:L,P',
                'birth_date' => 'nullable|date|before:today',
                'address' => 'nullable|string|max:500',
                'allergies' => 'nullable|string|max:500',
                'service_id' => 'nullable|exists:services,id',
                'appointment_date' => 'required|date',
                'complaint' => 'nullable|string|max:1000',
            ]);

            $appointment = DB::transaction(function () use ($data) {
                $patient = Patient::create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'birth_date' => $data['birth_date'] ?? null,
                    'address' => $data['address'] ?? null,
                    'allergies' => $data['allergies'] ?? null,
                ]);

                return Appointment::create([
                    'patient_id' => $patient->id,
                    'service_id' => $data['service_id'] ?? null,
                    'appointment_date' => $data['appointment_date'],
                    'complaint' => $data['complaint'] ?? 'Pemeriksaan umum',
                    'status' => 'pending',
                ]);
            });
        } else {
            $data = $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'service_id' => 'nullable|exists:services,id',
                'appointment_date' => 'required|date',
                'complaint' => 'nullable|string|max:1000',
            ]);
            $data['status'] = 'pending';
            $appointment = Appointment::create($data);
        }

        return redirect()->route('admin.appointments.ticket', $appointment)
            ->with('success', 'Nomor antrean #'.str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT).' berhasil dibuat.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'service', 'medicalRecord']);

        return view('admin.appointments.show', compact('appointment'));
    }

    /** Halaman Cetak Kartu Nomor Antrean On-Site */
    public function ticket(Appointment $appointment)
    {
        $appointment->load(['patient', 'service']);

        return view('admin.appointments.ticket', compact('appointment'));
    }

    /** Panggil pasien ke ruang periksa (trigger layar TV + suara TTS) */
    public function call(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'confirmed',
            'called_at' => now(),
        ]);

        return back()->with('called_patient', [
            'number' => $appointment->queue_number,
            'name' => $appointment->patient->name,
        ])->with('success', 'Memanggil Antrean #'.str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT).' — '.$appointment->patient->name);
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Appointment::STATUSES)),
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($data['status'] === 'confirmed') {
            $data['called_at'] = now();
        }

        $appointment->update($data);

        return back()->with('success', 'Status diubah menjadi "'.$appointment->status_label.'".');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Antrean dihapus.');
    }
}
