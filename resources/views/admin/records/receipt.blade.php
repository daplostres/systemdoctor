@extends('layouts.admin')
@section('title', 'Kuitansi '.$record->invoice_no)

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex gap-2 mb-3 d-print-none" style="max-width: 720px; margin: 0 auto">
    <a href="{{ route('admin.records.show', $record) }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali ke Rekam Medis</a>
    <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="bi bi-printer me-1"></i> Cetak Struk / Kuitansi</button>
</div>

<div class="card mx-auto" style="max-width: 720px">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-4">
            <div>
                <h4 class="fw-bold text-primary mb-1"><i class="bi bi-heart-pulse-fill me-1"></i> {{ $s['clinic_name'] ?? 'Praktek Dokter' }}</h4>
                <div class="small text-secondary">{{ $s['doctor_name'] ?? '' }} · {{ $s['doctor_sip'] ?? '' }}</div>
                <div class="small text-secondary">{{ $s['address'] ?? '' }}</div>
                <div class="small text-secondary">Telp/WA: {{ $s['phone'] ?? '-' }}</div>
            </div>
            <div class="text-end">
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 mb-2">LUNAS · {{ strtoupper($record->payment_label) }}</span>
                <div class="label-sm">No. Kuitansi</div>
                <div class="fw-bold font-monospace">{{ $record->invoice_no }}</div>
                <div class="small text-secondary">{{ $record->visit_date->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <div class="row g-2 mb-4 small bg-light rounded-3 p-3">
            <div class="col-sm-6">
                <div class="text-secondary">Telah diterima dari Pasien:</div>
                <div class="fw-bold fs-6">{{ $record->patient->name }}</div>
                <div class="font-monospace text-secondary">{{ $record->patient->medical_record_no }}</div>
            </div>
            <div class="col-sm-6 text-sm-end">
                <div class="text-secondary">Diagnosis / Layanan:</div>
                <div class="fw-semibold">{{ $record->appointment?->service?->name ?? 'Pemeriksaan & Konsultasi' }}</div>
                <div class="text-secondary">{{ $record->diagnosis }}</div>
            </div>
        </div>

        <table class="table align-middle mb-4">
            <thead>
            <tr>
                <th>Rincian Biaya</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Harga Satuan</th>
                <th class="text-end">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <div class="fw-semibold">Jasa Pemeriksaan & Konsultasi Dokter</div>
                    @if($record->treatment)<small class="text-secondary">Tindakan: {{ $record->treatment }}</small>@endif
                </td>
                <td class="text-center">1</td>
                <td class="text-end">Rp {{ number_format($record->fee, 0, ',', '.') }}</td>
                <td class="text-end fw-semibold">Rp {{ number_format($record->fee, 0, ',', '.') }}</td>
            </tr>
            @foreach($record->medicineItems as $item)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $item->medicine->name }}</div>
                        @if($item->dosage)<small class="text-secondary">Aturan pakai: {{ $item->dosage }}</small>@endif
                    </td>
                    <td class="text-center">{{ $item->qty }} {{ $item->medicine->unit }}</td>
                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="table-light">
                <th colspan="3" class="text-end fs-5">TOTAL PEMBAYARAN</th>
                <th class="text-end fs-5 text-primary">Rp {{ number_format($record->total_fee, 0, ',', '.') }}</th>
            </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between align-items-end pt-3">
            <div class="small text-secondary">
                <em>Terima kasih atas kepercayaan Anda.<br>Semoga lekas sembuh.</em>
            </div>
            <div class="text-center">
                <div class="small text-secondary mb-5">Pemeriksa / Kasir,</div>
                <div class="fw-bold">{{ $s['doctor_name'] ?? '' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

