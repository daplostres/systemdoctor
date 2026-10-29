@extends('layouts.admin')
@section('title', 'Buat Surat Sakit / Sehat / Rujukan')

@section('content')
<div class="card" style="max-width: 860px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.letters.store') }}">
            @csrf
            <input type="hidden" name="medical_record_id" value="{{ old('medical_record_id', $letter->medical_record_id) }}">

            <div class="mb-4">
                <label class="form-label fw-bold">Jenis Surat *</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="type" id="type-sick" value="sick" @checked(old('type', $letter->type) === 'sick')>
                    <label class="btn btn-outline-primary" for="type-sick"><i class="bi bi-bandaid me-1"></i> Surat Keterangan Sakit</label>

                    <input type="radio" class="btn-check" name="type" id="type-healthy" value="healthy" @checked(old('type', $letter->type) === 'healthy')>
                    <label class="btn btn-outline-primary" for="type-healthy"><i class="bi bi-patch-check me-1"></i> Surat Keterangan Sehat</label>

                    <input type="radio" class="btn-check" name="type" id="type-referral" value="referral" @checked(old('type', $letter->type) === 'referral')>
                    <label class="btn btn-outline-primary" for="type-referral"><i class="bi bi-hospital me-1"></i> Surat Rujukan RS / Lab</label>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Pasien *</label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">- Pilih pasien -</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" @selected(old('patient_id', $letter->patient_id) == $p->id)>{{ $p->name }} ({{ $p->medical_record_no }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pekerjaan</label>
                    <input name="occupation" class="form-control" value="{{ old('occupation', $letter->occupation) }}" placeholder="Karyawan / Pelajar">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Surat *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date', $letter->issue_date?->toDateString()) }}" required>
                </div>
            </div>

            {{-- Form khusus Surat Sakit --}}
            <div id="section-sick" class="border rounded-4 p-3 mb-3 bg-light">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-range me-1"></i> Rincian Istirahat Sakit</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Lama Istirahat (Hari) *</label>
                        <input type="number" min="1" max="90" name="rest_days" id="rest-days" class="form-control" value="{{ old('rest_days', $letter->rest_days ?? 2) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mulai Tanggal *</label>
                        <input type="date" name="start_date" id="start-date" class="form-control" value="{{ old('start_date', $letter->start_date?->toDateString()) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sampai Tanggal *</label>
                        <input type="date" name="end_date" id="end-date" class="form-control" value="{{ old('end_date', $letter->end_date?->toDateString()) }}">
                    </div>
                </div>
            </div>

            {{-- Form khusus Surat Sehat --}}
            <div id="section-healthy" class="border rounded-4 p-3 mb-3 bg-light" style="display:none">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-heart-pulse me-1"></i> Hasil Pemeriksaan Fisik (Surat Sehat)</h6>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tinggi Badan (cm)</label>
                        <input type="number" name="height" class="form-control" value="{{ old('height', $letter->height ?? 165) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" value="{{ old('weight', $letter->weight ?? 60) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tekanan Darah</label>
                        <input name="blood_pressure" class="form-control" value="{{ old('blood_pressure', $letter->blood_pressure ?? '120/80') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Suhu (°C)</label>
                        <input type="number" step="0.1" name="temperature" class="form-control" value="{{ old('temperature', $letter->temperature ?? 36.5) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tes Buta Warna</label>
                        <select name="color_blind" class="form-select">
                            @foreach(['Tidak Buta Warna', 'Buta Warna Parsial', 'Buta Warna Total'] as $cb)
                                <option value="{{ $cb }}" @selected(old('color_blind', $letter->color_blind) === $cb)>{{ $cb }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Keperluan Surat Sehat</label>
                        <input name="purpose" class="form-control" value="{{ old('purpose', $letter->purpose) }}" placeholder="Contoh: Persyaratan Melamar Pekerjaan / Pendaftaran Kuliah">
                    </div>
                </div>
            </div>

            {{-- Form khusus Surat Rujukan --}}
            <div id="section-referral" class="border rounded-4 p-3 mb-3 bg-light" style="display:none">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-hospital me-1"></i> Rincian Rujukan Rumah Sakit / Spesialis / Laboratorium</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Rumah Sakit / Laboratorium Tujuan *</label>
                        <input name="referral_to" class="form-control" value="{{ old('referral_to', $letter->referral_to) }}" placeholder="Contoh: RSUD Tugurejo Semarang / RS Permata Medika">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Poli Spesialis / Bagian Tujuan *</label>
                        <input name="referral_specialist" class="form-control" value="{{ old('referral_specialist', $letter->referral_specialist) }}" placeholder="Contoh: Poli Penyakit Dalam / Cek Darah Lengkap">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ringkasan Anamnesis & Pemeriksaan Fisik</label>
                        <textarea name="anamnesis" rows="2" class="form-control" placeholder="Keluhan utama dan tanda vital pasien">{{ old('anamnesis', $letter->anamnesis) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Diagnosis Sementara (Working Diagnosis)</label>
                        <input name="provisional_diagnosis" class="form-control" value="{{ old('provisional_diagnosis', $letter->provisional_diagnosis) }}" placeholder="Contoh: Suspek Demam Dengue / Hipertensi Grade II">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Terapi / Obat yang Telah Diberikan</label>
                        <input name="given_therapy" class="form-control" value="{{ old('given_therapy', $letter->given_therapy) }}" placeholder="Contoh: Paracetamol 500mg, edukasi cairan">
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Catatan Tambahan</label>
                <input name="notes" class="form-control" value="{{ old('notes', $letter->notes) }}">
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-lg"><i class="bi bi-printer me-1"></i> Simpan & Cetak Surat</button>
                <a href="{{ route('admin.letters.index') }}" class="btn btn-light btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const tSick = document.getElementById('type-sick');
    const tHealthy = document.getElementById('type-healthy');
    const tReferral = document.getElementById('type-referral');
    const sSick = document.getElementById('section-sick');
    const sHealthy = document.getElementById('section-healthy');
    const sReferral = document.getElementById('section-referral');

    function updateType() {
        sSick.style.display = tSick.checked ? 'block' : 'none';
        sHealthy.style.display = tHealthy.checked ? 'block' : 'none';
        sReferral.style.display = tReferral.checked ? 'block' : 'none';
    }
    [tSick, tHealthy, tReferral].forEach(r => r.addEventListener('change', updateType));
    updateType();

    const restInput = document.getElementById('rest-days');
    const startInput = document.getElementById('start-date');
    const endInput = document.getElementById('end-date');
    function calcEnd() {
        if (!startInput.value || !restInput.value) return;
        const d = new Date(startInput.value + 'T00:00:00');
        d.setDate(d.getDate() + Math.max(1, parseInt(restInput.value, 10)) - 1);
        endInput.value = d.toISOString().slice(0, 10);
    }
    restInput.addEventListener('input', calcEnd);
    startInput.addEventListener('change', calcEnd);
</script>
@endpush
