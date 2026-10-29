@extends('layouts.admin')
@section('title', 'Antrean On-Site (Di Tempat)')

@section('content')
@php($isDoctor = auth()->user()->hasRole('doctor'))
<div class="card">
    <div class="card-header">
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-md-3"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama / No. RM / HP / kode"></div>
            <div class="col-md-2"><input type="date" name="date" value="{{ $selectedDate === 'all' ? '' : $selectedDate }}" class="form-control"></div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach(\App\Models\Appointment::STATUSES as $key => $st)
                        <option value="{{ $key }}" @selected(request('status') === $key)>{{ $st['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex flex-wrap gap-2">
                <button class="btn btn-light"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.appointments.index', ['date' => 'all']) }}" class="btn btn-light">Semua Tanggal</a>
                <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg"></i> Ambil Antrean</a>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>No. Antrean</th><th>Kode</th><th>Tanggal</th><th>Pasien</th><th>Layanan / Keluhan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($appointments as $a)
                <tr class="{{ $a->status === 'confirmed' ? 'table-info' : '' }}">
                    <td><span class="queue-badge">{{ str_pad($a->queue_number, 2, '0', STR_PAD_LEFT) }}</span></td>
                    <td class="font-monospace small">{{ $a->code }}</td>
                    <td>{{ $a->appointment_date->translatedFormat('D, d M Y') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $a->patient->name }}</div>
                        <small class="text-secondary">{{ $a->patient->medical_record_no }} · {{ $a->patient->phone }}</small>
                    </td>
                    <td class="small">
                        <div class="fw-semibold">{{ $a->service->name ?? 'Pemeriksaan Umum' }}</div>
                        <span class="text-secondary">{{ \Illuminate\Support\Str::limit($a->complaint, 40) }}</span>
                    </td>
                    <td><span class="badge bg-{{ $a->status_color }}">{{ $a->status_label }}</span></td>
                    <td class="text-end text-nowrap">
                        @if($a->status !== 'completed' && $a->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.appointments.call', $a) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-sm btn-warning" title="Panggil Pasien"><i class="bi bi-volume-up-fill"></i> Panggil</button>
                            </form>
                            @if($isDoctor)
                                <a href="{{ route('admin.records.create', ['appointment_id' => $a->id]) }}" class="btn btn-sm btn-primary" title="Periksa"><i class="bi bi-clipboard2-pulse"></i></a>
                            @endif
                        @endif
                        <a href="{{ route('admin.appointments.ticket', $a) }}" class="btn btn-sm btn-light" title="Cetak Kartu Antrean"><i class="bi bi-printer"></i></a>
                        <a href="{{ route('admin.appointments.show', $a) }}" class="btn btn-sm btn-light" title="Detail"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Tidak ada data antrean pada tanggal ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($appointments->hasPages())<div class="card-body">{{ $appointments->links() }}</div>@endif
</div>
@endsection
