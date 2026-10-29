@extends('layouts.admin')
@section('title', 'Detail Antrean')

@section('content')
@php($wa = preg_replace('/^0/', '62', preg_replace('/\D/', '', $appointment->patient->phone)))
@php($isDoctor = auth()->user()->hasRole('doctor'))
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="font-monospace">{{ $appointment->code }}</span>
                <span class="badge bg-{{ $appointment->status_color }} ms-auto">{{ $appointment->status_label }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="queue-badge" style="width:64px;height:64px;font-size:1.6rem">{{ str_pad($appointment->queue_number, 2, '0', STR_PAD_LEFT) }}</span>
                    <div>
                        <div class="label-sm">Nomor Antrean</div>
                        <div class="fw-bold fs-5">{{ $appointment->appointment_date->translatedFormat('l, d F Y') }}</div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6"><div class="label-sm">Pasien</div><a href="{{ route('admin.patients.show', $appointment->patient) }}" class="fw-semibold">{{ $appointment->patient->name }}</a><div class="small text-secondary">{{ $appointment->patient->medical_record_no }}</div></div>
                    <div class="col-md-6"><div class="label-sm">No. HP</div>{{ $appointment->patient->phone }}</div>
                    <div class="col-md-6"><div class="label-sm">Layanan</div>{{ $appointment->service->name ?? 'Pemeriksaan Umum' }}</div>
                    <div class="col-md-6"><div class="label-sm">Terdaftar Pukul</div>{{ $appointment->created_at->translatedFormat('d M Y H:i') }}</div>
                    <div class="col-12"><div class="label-sm">Keluhan</div>{{ $appointment->complaint ?? '-' }}</div>
                    @if($appointment->patient->allergies)
                        <div class="col-12"><div class="alert alert-danger py-2 mb-0 small"><i class="bi bi-exclamation-triangle"></i> Alergi: {{ $appointment->patient->allergies }}</div></div>
                    @endif
                </div>
            </div>
            <div class="card-body border-top d-flex flex-wrap gap-2">
                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                    <form method="POST" action="{{ route('admin.appointments.call', $appointment) }}">
                        @csrf
                        <button class="btn btn-warning"><i class="bi bi-volume-up-fill me-1"></i> Panggil Pasien</button>
                    </form>
                @endif
                @if($appointment->medicalRecord)
                    @if($isDoctor)
                        <a href="{{ route('admin.records.show', $appointment->medicalRecord) }}" class="btn btn-success"><i class="bi bi-journal-check me-1"></i> Lihat Rekam Medis</a>
                    @endif
                    <a href="{{ route('admin.records.receipt', $appointment->medicalRecord) }}" class="btn btn-outline-primary"><i class="bi bi-receipt me-1"></i> Cetak Kuitansi</a>
                @elseif($appointment->status !== 'cancelled' && $isDoctor)
                    <a href="{{ route('admin.records.create', ['appointment_id' => $appointment->id]) }}" class="btn btn-primary"><i class="bi bi-clipboard2-pulse me-1"></i> Periksa & Isi Rekam Medis</a>
                @endif
                <a href="{{ route('admin.appointments.ticket', $appointment) }}" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Cetak Kartu Antrean</a>
                <form method="POST" action="{{ route('admin.appointments.destroy', $appointment) }}" class="ms-auto" data-confirm="Hapus antrean ini?">
                    @csrf @method('DELETE')
                    <button class="btn btn-light text-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">Ubah Status Antrean</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.appointments.status', $appointment) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        @foreach(\App\Models\Appointment::STATUSES as $key => $st)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="radio" name="status" id="st-{{ $key }}" value="{{ $key }}" @checked($appointment->status === $key)>
                                <label class="form-check-label" for="st-{{ $key }}"><span class="badge bg-{{ $st['color'] }}">{{ $st['label'] }}</span></label>
                            </div>
                        @endforeach
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Petugas</label>
                        <textarea name="admin_note" rows="2" class="form-control">{{ $appointment->admin_note }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">Simpan Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
