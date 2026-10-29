<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $records = MedicalRecord::with('patient')
            ->when($request->q, function ($q, $s) {
                $q->where(fn ($w) => $w->where('diagnosis', 'like', "%$s%")
                    ->orWhere('invoice_no', 'like', "%$s%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%$s%")->orWhere('medical_record_no', 'like', "%$s%")));
            })
            ->when($request->from, fn ($q, $d) => $q->whereDate('visit_date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('visit_date', '<=', $d))
            ->latest('visit_date')->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $appointment = $request->appointment_id
            ? Appointment::with(['patient', 'service'])->findOrFail($request->appointment_id)
            : null;

        $record = new MedicalRecord([
            'visit_date' => now()->toDateString(),
            'patient_id' => $appointment?->patient_id ?? $request->patient_id,
            'appointment_id' => $appointment?->id,
            'complaint' => $appointment?->complaint,
            'fee' => $appointment?->service?->price ?? 75000,
            'payment_method' => 'cash',
        ]);

        return view('admin.records.form', [
            'record' => $record,
            'appointment' => $appointment,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'medical_record_no']),
            'medicines' => Medicine::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $record = DB::transaction(function () use ($data, $request) {
            $record = MedicalRecord::create($data);
            $this->syncMedicines($record, $request->input('items', []));

            if ($record->appointment_id) {
                Appointment::whereKey($record->appointment_id)->update(['status' => 'completed']);
            }

            return $record;
        });

        return redirect()->route('admin.records.show', $record)->with('success', 'Rekam medis & tagihan tersimpan.');
    }

    public function show(MedicalRecord $record)
    {
        $record->load(['patient', 'appointment.service', 'medicineItems.medicine']);

        return view('admin.records.show', compact('record'));
    }

    public function receipt(MedicalRecord $record)
    {
        $record->load(['patient', 'appointment.service', 'medicineItems.medicine']);

        return view('admin.records.receipt', compact('record'));
    }

    /** Halaman Cetak Etiket / Label Plastik Obat */
    public function labels(MedicalRecord $record)
    {
        $record->load(['patient', 'medicineItems.medicine']);

        return view('admin.records.labels', compact('record'));
    }

    /** Halaman Cetak Lembar Resep Dokter (R/) untuk Apotek Luar */
    public function prescriptionPrint(MedicalRecord $record)
    {
        $record->load(['patient', 'medicineItems.medicine']);

        return view('admin.records.prescription', compact('record'));
    }

    /** Tandai obat sudah disiapkan & diserahkan ke pasien oleh pengelola obat */
    public function dispense(MedicalRecord $record)
    {
        $record->update(['pharmacy_status' => 'dispensed']);

        return back()->with('success', 'Obat untuk pasien '.$record->patient->name.' ('.$record->invoice_no.') telah ditandai selesai diserahkan.');
    }

    public function edit(MedicalRecord $record)
    {
        $record->load('medicineItems.medicine');

        return view('admin.records.form', [
            'record' => $record,
            'appointment' => $record->appointment,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'medical_record_no']),
            'medicines' => Medicine::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, MedicalRecord $record)
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($record, $data, $request) {
            $record->update($data);
            $this->syncMedicines($record, $request->input('items', []));
        });

        return redirect()->route('admin.records.show', $record)->with('success', 'Rekam medis diperbarui.');
    }

    public function destroy(MedicalRecord $record)
    {
        $patient = $record->patient_id;

        DB::transaction(function () use ($record) {
            foreach ($record->medicineItems as $item) {
                Medicine::whereKey($item->medicine_id)->increment('stock', $item->qty);
            }
            $record->delete();
        });

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Rekam medis dihapus & stok obat dikembalikan.');
    }

    private function syncMedicines(MedicalRecord $record, array $items): void
    {
        // Gabungkan jika obat yang sama dipilih lebih dari sekali & validasi ketersediaan stok
        $grouped = [];
        foreach ($items as $row) {
            $medId = $row['medicine_id'] ?? null;
            if (! $medId) {
                continue;
            }
            $qty = max(1, (int) ($row['qty'] ?? 1));
            if (isset($grouped[$medId])) {
                $grouped[$medId]['qty'] += $qty;
            } else {
                $grouped[$medId] = [
                    'medicine_id' => $medId,
                    'qty' => $qty,
                    'dosage' => trim($row['dosage'] ?? ''),
                ];
            }
        }

        // Kembalikan stok lama jika edit
        foreach ($record->medicineItems as $old) {
            Medicine::whereKey($old->medicine_id)->increment('stock', $old->qty);
        }
        $record->medicineItems()->delete();

        $medicineFee = 0;
        $prescriptionLines = [];

        foreach ($grouped as $row) {
            $medicine = Medicine::find($row['medicine_id']);
            if (! $medicine) {
                continue;
            }
            $qty = $row['qty'];
            if ($medicine->stock < $qty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'items' => "Stok obat {$medicine->name} tidak mencukupi (tersedia: {$medicine->stock} {$medicine->unit}).",
                ]);
            }

            $price = $medicine->price;
            $dosage = $row['dosage'];

            $record->medicineItems()->create([
                'medicine_id' => $medicine->id,
                'qty' => $qty,
                'price' => $price,
                'dosage' => $dosage ?: null,
            ]);

            $medicine->decrement('stock', $qty);
            $medicine->refresh();

            \App\Models\MedicineStockLog::create([
                'medicine_id' => $medicine->id,
                'user_id' => auth()->id(),
                'type' => 'out',
                'qty' => $qty,
                'stock_after' => $medicine->stock,
                'description' => 'Resep pasien '.$record->patient->name.' ('.$record->invoice_no.')',
            ]);

            $medicineFee += $price * $qty;
            $prescriptionLines[] = $medicine->name.' ('.$qty.' '.$medicine->unit.')'.($dosage ? ' — '.$dosage : '');
        }

        // Jika catatan resep manual kosong tetapi memilih obat, isi ringkasannya otomatis
        $updates = ['medicine_fee' => $medicineFee];
        if (empty($record->prescription) && count($prescriptionLines) > 0) {
            $updates['prescription'] = implode("\n", $prescriptionLines);
        }
        $record->update($updates);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'blood_pressure' => 'nullable|string|max:15',
            'weight' => 'nullable|numeric|min:0|max:500',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
            'pharmacy_note' => 'nullable|string|max:1000',
            'notes' => 'nullable|string',
            'fee' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,qris,transfer',
        ]);
    }
}
