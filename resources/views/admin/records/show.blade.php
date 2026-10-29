@extends('layouts.admin')
@section('title', 'Detail Rekam Medis')

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex flex-wrap gap-2 mb-3 d-print-none">
    @if(auth()->user()->hasRole('doctor', 'medical_record'))
        <a href="{{ route('admin.patients.show', $record->patient) }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Ke Pasien</a>
        <a href="{{ route('admin.records.edit', $record) }}" class="btn btn-light ms-auto"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('admin.letters.create', ['record_id' => $record->id, 'type' => 'sick']) }}" class="btn btn-outline-warning"><i class="bi bi-bandaid"></i> Surat Sakit</a>
        <a href="{{ route('admin.letters.create', ['record_id' => $record->id, 'type' => 'healthy']) }}" class="btn btn-outline-success"><i class="bi bi-patch-check"></i> Surat Sehat</a>
        <a href="{{ route('admin.letters.create', ['record_id' => $record->id, 'type' => 'referral']) }}" class="btn btn-outline-info"><i class="bi bi-hospital"></i> Surat Rujukan</a>
    @else
        <a href="{{ route('admin.records.index') }}" class="btn btn-light me-auto"><i class="bi bi-arrow-left"></i> Daftar Resep</a>
    @endif
    <a href="{{ route('admin.records.prescription', $record) }}" class="btn btn-outline-secondary"><i class="bi bi-file-earmark-medical"></i> Cetak Resep (R/)</a>
    <a href="{{ route('admin.records.labels', $record) }}" class="btn btn-outline-dark"><i class="bi bi-tag"></i> Cetak Etiket Obat</a>
    <a href="{{ route('admin.records.receipt', $record) }}" class="btn btn-outline-primary"><i class="bi bi-receipt"></i> Cetak Struk / Kuitansi</a>
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak Rekam Medis</button>
    @if(auth()->user()->hasRole('doctor', 'medical_record'))
        <form method="POST" action="{{ route('admin.records.destroy', $record) }}" data-confirm="Hapus rekam medis ini?">
            @csrf @method('DELETE')
            <button class="btn btn-light text-danger"><i class="bi bi-trash"></i></button>
        </form>
    @endif
</div>

<div class="card" style="max-width: 900px">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex align-items-center border-bottom pb-3 mb-4">
            <span class="brand-icon me-3"><i class="bi bi-heart-pulse-fill"></i></span>
            <div>
                <h5 class="fw-bold mb-0">{{ $s['clinic_name'] ?? '' }}</h5>
                <small class="text-secondary">{{ $s['address'] ?? '' }} · {{ $s['phone'] ?? '' }}</small>
            </div>
            <div class="ms-auto text-end">
                <div class="font-monospace small text-secondary">{{ $record->invoice_no }}</div>
                <div class="fw-bold">{{ $record->visit_date->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="label-sm">Nama Pasien</div><div class="fw-semibold">{{ $record->patient->name }}</div></div>
            <div class="col-md-3"><div class="label-sm">No. RM</div><div class="font-monospace">{{ $record->patient->medical_record_no }}</div></div>
            <div class="col-md-3"><div class="label-sm">JK / Umur</div>{{ $record->patient->gender_label }} · {{ $record->patient->age ?? '-' }}</div>
            <div class="col-md-2"><div class="label-sm">Gol. Darah</div>{{ $record->patient->blood_type ?? '-' }}</div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4"><div class="stat-pill"><small class="text-secondary">Tekanan Darah</small><strong>{{ $record->blood_pressure ?? '-' }}</strong></div></div>
            <div class="col-4"><div class="stat-pill"><small class="text-secondary">Berat Badan</small><strong>{{ $record->weight ? $record->weight.' kg' : '-' }}</strong></div></div>
            <div class="col-4"><div class="stat-pill"><small class="text-secondary">Suhu</small><strong>{{ $record->temperature ? $record->temperature.' °C' : '-' }}</strong></div></div>
        </div>

        @foreach(['complaint' => 'Keluhan', 'diagnosis' => 'Diagnosis', 'treatment' => 'Tindakan', 'prescription' => 'Resep Obat', 'notes' => 'Catatan / Saran'] as $field => $label)
            @if($record->$field)
                <div class="mb-3">
                    <div class="label-sm">{{ $label }}</div>
                    <div class="{{ $field === 'prescription' ? 'font-monospace bg-light rounded p-3' : '' }} {{ $field === 'diagnosis' ? 'fw-semibold' : '' }}">{!! nl2br(e($record->$field)) !!}</div>
                </div>
            @endif
        @endforeach

        @if($record->pharmacy_note)
            <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
                <i class="bi bi-capsule-pill fs-5"></i>
                <div>
                    <div class="fw-bold small text-uppercase">Keterangan Dokter untuk Pengelola Obat (Apotek):</div>
                    <div>{!! nl2br(e($record->pharmacy_note)) !!}</div>
                </div>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between bg-light rounded-3 p-3 mb-3 d-print-none">
            <div>
                <span class="small text-secondary me-2">Status Penyerahan Obat:</span>
                @if($record->pharmacy_status === 'dispensed')
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Obat Sudah Diserahkan ke Pasien</span>
                @else
                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu Disiapkan Apotek</span>
                @endif
            </div>
            @if($record->pharmacy_status === 'pending' && auth()->user()->hasRole('pharmacist'))
                <form method="POST" action="{{ route('admin.records.dispense', $record) }}">
                    @csrf @method('PATCH')
                    <button class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i> Tandai Obat Sudah Diserahkan</button>
                </form>
            @endif
        </div>

        <div class="d-flex justify-content-between align-items-end border-top pt-4 mt-4">
            <div>
                <div class="small text-secondary">Jasa: Rp {{ number_format($record->fee, 0, ',', '.') }} · Obat: Rp {{ number_format($record->medicine_fee, 0, ',', '.') }} ({{ $record->payment_label }})</div>
                <div class="label-sm mt-1">Total Tagihan</div>
                <div class="fs-4 fw-bold text-primary">Rp {{ number_format($record->total_fee, 0, ',', '.') }}</div>
            </div>
            <div class="text-center">
                <div class="small text-secondary mb-5">Dokter Pemeriksa,</div>
                <div class="fw-bold">{{ $s['doctor_name'] ?? '' }}</div>
                <small class="text-secondary">{{ $s['doctor_sip'] ?? '' }}</small>
            </div>
        </div>
    </div>
</div>
@endsection
