<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $patients = Patient::query()
            ->when($request->q, function ($q, $s) {
                $q->where(fn ($w) => $w->where('name', 'like', "%$s%")
                    ->orWhere('medical_record_no', 'like', "%$s%")
                    ->orWhere('phone', 'like', "%$s%")
                    ->orWhere('nik', 'like', "%$s%"));
            })
            ->withCount('medicalRecords')
            ->latest()->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.form', ['patient' => new Patient]);
    }

    public function store(Request $request)
    {
        $patient = Patient::create($this->validated($request));

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'medicalRecords' => fn ($q) => $q->latest('visit_date'),
            'appointments' => fn ($q) => $q->with('service')->latest('appointment_date'),
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.form', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $patient->update($this->validated($request, $patient));

        return redirect()->route('admin.patients.show', $patient)->with('success', 'Data pasien diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('admin.patients.index')->with('success', 'Pasien dihapus.');
    }

    private function validated(Request $request, ?Patient $patient = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:100',
            'nik' => ['nullable', 'digits:16', Rule::unique('patients', 'nik')->ignore($patient?->id)],
            'gender' => 'required|in:L,P',
            'birth_date' => 'nullable|date|before:today',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string|max:500',
        ]);
    }
}

