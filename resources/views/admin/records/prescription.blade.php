@extends('layouts.admin')
@section('title', 'Lembar Resep Dokter — '.$record->patient->name)

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex gap-2 mb-3 d-print-none mx-auto" style="max-width: 580px">
    <a href="{{ route('admin.records.show', $record) }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali ke Rekam Medis</a>
    <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="bi bi-printer me-1"></i> Cetak Lembar Resep (R/)</button>
</div>

<div class="card mx-auto" style="max-width: 580px">
    <div class="card-body p-4 p-md-5">
        {{-- Kop Dokter --}}
        <div class="text-center border-bottom border-2 border-dark pb-3 mb-3">
            <h5 class="fw-bold mb-0">{{ $s['doctor_name'] ?? 'dr. Eko Andrianto' }}</h5>
            <div class="small fw-semibold">{{ $s['doctor_sip'] ?? '' }}</div>
            <div class="small text-secondary">{{ $s['address'] ?? '' }}</div>
            <div class="small text-secondary">Telp: {{ $s['phone'] ?? '' }}</div>
        </div>

        <div class="d-flex justify-content-between small mb-3">
            <span>No. Resep: <strong class="font-monospace">{{ $record->invoice_no }}</strong></span>
            <span>Semarang, <strong>{{ $record->visit_date->translatedFormat('d F Y') }}</strong></span>
        </div>

        {{-- Isi Resep R/ --}}
        <div class="my-4" style="min-height: 280px">
            @forelse($record->medicineItems as $item)
                <div class="mb-3 pb-2 border-bottom" style="border-bottom-style: dashed !important">
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-4 font-monospace text-primary">R/</span>
                        <div class="flex-grow-1">
                            <div class="fw-bold fs-6">{{ $item->medicine->name }} <span class="ms-2">No. {{ $item->qty }} ({{ $item->medicine->unit }})</span></div>
                            <div class="fst-italic text-secondary">S. {{ $item->dosage ?: 'Sesuai petunjuk dokter' }}</div>
                        </div>
                    </div>
                </div>
            @empty
                @if($record->prescription)
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fw-bold fs-4 font-monospace text-primary">R/</span>
                        <div class="font-monospace">{!! nl2br(e($record->prescription)) !!}</div>
                    </div>
                @else
                    <p class="text-secondary">Tidak ada obat yang diresepkan.</p>
                @endif
            @endforelse
        </div>

        @if($record->patient->allergies)
            <div class="alert alert-danger py-2 small">
                <strong>Riwayat Alergi Obat:</strong> {{ $record->patient->allergies }}
            </div>
        @endif

        {{-- Identitas Pasien & Tanda Tangan --}}
        <div class="row align-items-end border-top border-dark pt-3 mt-4 small">
            <div class="col-7">
                <div><strong>Pro (Pasien):</strong> {{ $record->patient->name }}</div>
                <div><strong>No. RM:</strong> <span class="font-monospace">{{ $record->patient->medical_record_no }}</span></div>
                <div><strong>Umur / BB:</strong> {{ $record->patient->age ?? '-' }} / {{ $record->weight ? $record->weight.' kg' : '-' }}</div>
                <div><strong>Alamat:</strong> {{ $record->patient->address ?? '-' }}</div>
            </div>
            <div class="col-5 text-center">
                <div class="text-secondary mb-5">Paraf Dokter,</div>
                <div class="fw-bold text-decoration-underline">{{ $s['doctor_name'] ?? 'dr. Eko Andrianto' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

