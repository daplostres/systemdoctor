<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalLetter;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalLetterController extends Controller
{
    public function index(Request $request)
    {
        $letters = MedicalLetter::with('patient')
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->q, function ($q, $s) {
                $q->where(fn ($w) => $w->where('letter_no', 'like', "%$s%")
                    ->orWhere('referral_to', 'like', "%$s%")
                    ->orWhereHas('patient', fn ($p) => $p->where('name', 'like', "%$s%")->orWhere('medical_record_no', 'like', "%$s%")));
            })
            ->latest('issue_date')->latest('id')
            ->paginate(15)->withQueryString();

        return view('admin.letters.index', compact('letters'));
    }

    public function create(Request $request)
    {
        $record = $request->record_id ? MedicalRecord::with('patient')->findOrFail($request->record_id) : null;
        $type = $request->input('type', 'sick');

        $letter = new MedicalLetter([
            'type' => $type,
            'patient_id' => $record?->patient_id ?? $request->patient_id,
            'medical_record_id' => $record?->id,
            'issue_date' => now()->toDateString(),
            'rest_days' => 2,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'weight' => $record?->weight,
            'blood_pressure' => $record?->blood_pressure ?? '120/80',
            'temperature' => $record?->temperature ?? 36.5,
            'color_blind' => 'Tidak Buta Warna',
            'referral_to' => 'RSUD Adhyatma, MPH (Tugurejo) Semarang',
            'referral_specialist' => 'Poli Penyakit Dalam',
            'anamnesis' => $record ? trim($record->complaint.($record->blood_pressure ? ' (TD: '.$record->blood_pressure.' mmHg, Suhu: '.$record->temperature.' °C)' : '')) : null,
            'provisional_diagnosis' => $record?->diagnosis,
            'given_therapy' => $record ? trim(($record->treatment ? $record->treatment."\n" : '').($record->prescription ?? '')) : null,
        ]);

        return view('admin.letters.form', [
            'letter' => $letter,
            'patients' => Patient::orderBy('name')->get(['id', 'name', 'medical_record_no']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:sick,healthy,referral',
            'patient_id' => 'required|exists:patients,id',
            'medical_record_id' => 'nullable|exists:medical_records,id',
            'issue_date' => 'required|date',
            'occupation' => 'nullable|string|max:100',
            'rest_days' => 'nullable|integer|min:1|max:90',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'height' => 'nullable|integer|min:30|max:250',
            'weight' => 'nullable|numeric|min:1|max:300',
            'blood_pressure' => 'nullable|string|max:15',
            'temperature' => 'nullable|numeric|min:30|max:45',
            'color_blind' => 'nullable|string|max:30',
            'purpose' => 'nullable|string|max:255',
            'referral_to' => 'nullable|string|max:150',
            'referral_specialist' => 'nullable|string|max:120',
            'anamnesis' => 'nullable|string|max:1000',
            'provisional_diagnosis' => 'nullable|string|max:255',
            'given_therapy' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        $letter = MedicalLetter::create($data);

        return redirect()->route('admin.letters.show', $letter)->with('success', $letter->type_label.' berhasil dibuat.');
    }

    public function show(MedicalLetter $letter)
    {
        $letter->load('patient');

        return view('admin.letters.show', compact('letter'));
    }

    public function destroy(MedicalLetter $letter)
    {
        $letter->delete();

        return redirect()->route('admin.letters.index')->with('success', 'Surat keterangan dihapus.');
    }
}
