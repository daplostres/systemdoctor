@extends('layouts.admin')
@section('title', 'Kartu Antrean #'.str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT))

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex gap-2 mb-3 d-print-none mx-auto" style="max-width: 420px">
    <a href="{{ route('admin.appointments.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Daftar Antrean</a>
    <a href="{{ route('admin.appointments.create') }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg"></i> Antrean Baru</a>
    <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="bi bi-printer me-1"></i> Cetak Kartu</button>
</div>

<div class="card mx-auto text-center ticket" style="max-width: 420px">
    <div class="card-body p-4">
        <div class="fw-bold text-primary fs-5">{{ $s['clinic_name'] ?? 'Praktek dr. Eko Andrianto' }}</div>
        <div class="small text-secondary mb-3">{{ $s['address'] ?? '' }}</div>
        <hr class="my-2">
        <div class="text-uppercase small text-secondary fw-semibold mt-2">Nomor Antrean Anda</div>
        <div class="queue-number my-2" style="font-size: 5rem">{{ str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT) }}</div>
        <div class="font-monospace small text-secondary mb-3">{{ $appointment->code }} · {{ $appointment->appointment_date->translatedFormat('l, d M Y') }}</div>

        <div class="bg-light rounded-3 p-3 text-start small mb-3">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary">Nama Pasien:</span>
                <span class="fw-bold">{{ $appointment->patient->name }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary">No. RM:</span>
                <span class="font-monospace">{{ $appointment->patient->medical_record_no }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-secondary">Layanan:</span>
                <span>{{ $appointment->service->name ?? 'Pemeriksaan Umum' }}</span>
            </div>
        </div>
        <div class="small text-secondary">Mohon menunggu panggilan nomor antrean di ruang tunggu. Terima kasih.</div>
    </div>
</div>
@endsection

