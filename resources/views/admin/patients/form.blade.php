@extends('layouts.admin')
@section('title', $patient->exists ? 'Edit Pasien' : 'Pasien Baru')

@section('content')
<div class="card" style="max-width: 900px">
    <div class="card-body p-4">
        <form method="POST" action="{{ $patient->exists ? route('admin.patients.update', $patient) : route('admin.patients.store') }}">
            @csrf
            @if($patient->exists) @method('PUT') @endif
            <div class="row g-3">
                @if($patient->exists)
                    <div class="col-md-4">
                        <label class="form-label">No. Rekam Medis</label>
                        <input class="form-control font-monospace" value="{{ $patient->medical_record_no }}" disabled>
                    </div>
                @endif
                <div class="col-md-{{ $patient->exists ? 8 : 12 }}">
                    <label class="form-label">Nama Lengkap *</label>
                    <input name="name" class="form-control" value="{{ old('name', $patient->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIK</label>
                    <input name="nik" class="form-control" maxlength="16" value="{{ old('nik', $patient->nik) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP *</label>
                    <input name="phone" class="form-control" value="{{ old('phone', $patient->phone) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Kelamin *</label>
                    <select name="gender" class="form-select" required>
                        <option value="L" @selected(old('gender', $patient->gender) === 'L')>Laki-laki</option>
                        <option value="P" @selected(old('gender', $patient->gender) === 'P')>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $patient->birth_date?->toDateString()) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Golongan Darah</label>
                    <select name="blood_type" class="form-select">
                        <option value="">-</option>
                        @foreach(['A', 'B', 'AB', 'O'] as $bt)
                            <option @selected(old('blood_type', $patient->blood_type) === $bt)>{{ $bt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $patient->address) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Riwayat Alergi</label>
                    <textarea name="allergies" rows="2" class="form-control" placeholder="Contoh: Amoxicillin, seafood">{{ old('allergies', $patient->allergies) }}</textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ $patient->exists ? route('admin.patients.show', $patient) : route('admin.patients.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

