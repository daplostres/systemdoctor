@extends('layouts.admin')
@section('title', 'Cetak Etiket Obat — '.$record->patient->name)

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex flex-wrap gap-2 mb-3 d-print-none">
    <a href="{{ route('admin.records.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Daftar Resep</a>
    <a href="{{ route('admin.records.receipt', $record) }}" class="btn btn-outline-primary ms-auto"><i class="bi bi-receipt me-1"></i> Cetak Kuitansi</a>
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i> Cetak Etiket Obat</button>
</div>

@if($record->pharmacy_note)
    <div class="alert alert-warning d-print-none">
        <i class="bi bi-info-circle-fill me-1"></i> <strong>Instruksi Dokter ke Apotek:</strong> {{ $record->pharmacy_note }}
    </div>
@endif

<style>
    .etiket-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(310px, 1fr));
        gap: 16px;
    }
    .etiket-box {
        background: #fff;
        border: 2px solid #0f2b3d;
        border-radius: 10px;
        padding: 12px 14px;
        page-break-inside: avoid;
    }
    .etiket-header {
        border-bottom: 1.5px solid #0f2b3d;
        padding-bottom: 6px;
        margin-bottom: 8px;
        text-align: center;
    }
    .etiket-dosage {
        border: 1.5px dashed #0e9f8f;
        background: #f2fbf9;
        border-radius: 8px;
        padding: 8px;
        text-align: center;
        font-weight: 800;
        font-size: 1.05rem;
        margin: 8px 0;
    }
</style>

<div class="etiket-grid">
    @forelse($record->medicineItems as $item)
        <div class="etiket-box">
            <div class="etiket-header">
                <div class="fw-bold text-uppercase" style="font-size:.9rem">{{ $s['clinic_name'] ?? 'Praktek dr. Eko Andrianto' }}</div>
                <div style="font-size:.7rem" class="text-secondary">{{ $s['address'] ?? '' }} · Telp: {{ $s['phone'] ?? '' }}</div>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span>No. RM: <strong class="font-monospace">{{ $record->patient->medical_record_no }}</strong></span>
                <span>Tgl: <strong>{{ $record->visit_date->format('d/m/Y') }}</strong></span>
            </div>
            <div class="small mb-2">
                Nama Pasien: <strong class="fs-6">{{ $record->patient->name }}</strong>
            </div>
            <div class="bg-light rounded p-2 small d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $item->medicine->name }}</span>
                <span class="badge bg-dark">{{ $item->qty }} {{ $item->medicine->unit }}</span>
            </div>
            <div class="etiket-dosage">
                {{ $item->dosage ?: 'Sesuai petunjuk dokter' }}
            </div>
            <div class="d-flex justify-content-between align-items-center" style="font-size:.72rem">
                <span>Exp: {{ $item->medicine->expired_date?->format('m/Y') ?? '-' }}</span>
                <span class="fst-italic">Semoga Lekas Sembuh</span>
            </div>
        </div>
    @empty
        <div class="card p-4 text-center text-secondary">
            Tidak ada rincian item obat pada rekam medis ini.
        </div>
    @endforelse
</div>
@endsection

