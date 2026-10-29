@extends('layouts.admin')
@section('title', auth()->user()->role === 'pharmacist' ? 'Resep & Keterangan Obat dari Dokter' : 'Rekam Medis')

@section('content')
@php($isPharmacist = auth()->user()->role === 'pharmacist')
<div class="card">
    <div class="card-header">
        <form class="row g-2 align-items-center" method="GET">
            <div class="col-md-4"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="No. Invoice / Nama pasien / No. RM / diagnosis"></div>
            <div class="col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control" title="Dari"></div>
            <div class="col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control" title="Sampai"></div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-light"><i class="bi bi-funnel"></i> Filter</button>
                @if(auth()->user()->hasRole('doctor', 'medical_record'))
                    <a href="{{ route('admin.records.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg"></i> Rekam Medis Baru</a>
                @endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>Invoice / Tanggal</th>
                <th>Pasien</th>
                <th>Resep Obat</th>
                <th>Keterangan Dokter ke Apotek</th>
                <th>Status Obat</th>
                <th>Total Tagihan</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $r)
                <tr class="{{ $r->pharmacy_status === 'pending' && $isPharmacist ? 'table-warning' : '' }}">
                    <td>
                        <div class="font-monospace small fw-semibold">{{ $r->invoice_no }}</div>
                        <small class="text-secondary">{{ $r->visit_date->translatedFormat('d M Y') }}</small>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $r->patient->name }}</div>
                        <small class="text-secondary font-monospace">{{ $r->patient->medical_record_no }}</small>
                    </td>
                    <td class="small">{!! nl2br(e($r->prescription ?? '-')) !!}</td>
                    <td class="small">
                        @if($r->pharmacy_note)
                            <div class="p-2 rounded bg-warning-subtle text-dark border border-warning-subtle">
                                <i class="bi bi-info-circle-fill text-warning me-1"></i> {{ $r->pharmacy_note }}
                            </div>
                        @else
                            <span class="text-secondary">-</span>
                        @endif
                    </td>
                    <td>
                        @if($r->pharmacy_status === 'dispensed')
                            <span class="badge bg-success"><i class="bi bi-check-lg"></i> Diserahkan</span>
                        @else
                            <span class="badge bg-warning text-dark">Menunggu Obat</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-semibold">Rp {{ number_format($r->total_fee, 0, ',', '.') }}</div>
                        <small class="text-secondary">{{ $r->payment_label }}</small>
                    </td>
                    <td class="text-end text-nowrap">
                        @if($r->pharmacy_status === 'pending' && auth()->user()->hasRole('pharmacist'))
                            <form method="POST" action="{{ route('admin.records.dispense', $r) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success" title="Tandai Obat Sudah Diserahkan"><i class="bi bi-check2-circle"></i> Serahkan</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.records.prescription', $r) }}" class="btn btn-sm btn-outline-secondary" title="Cetak Lembar Resep (R/)"><i class="bi bi-prescription2"></i> R/</a>
                        <a href="{{ route('admin.records.labels', $r) }}" class="btn btn-sm btn-outline-dark" title="Cetak Etiket Plastik Obat"><i class="bi bi-tag"></i> Etiket</a>
                        <a href="{{ route('admin.records.receipt', $r) }}" class="btn btn-sm btn-outline-primary" title="Cetak Kuitansi"><i class="bi bi-receipt"></i></a>
                        <a href="{{ route('admin.records.show', $r) }}" class="btn btn-sm btn-light" title="Detail"><i class="bi bi-eye"></i></a>
                        @if(auth()->user()->hasRole('doctor', 'medical_record'))
                            <a href="{{ route('admin.records.edit', $r) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data rekam medis / resep.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($records->hasPages())<div class="card-body">{{ $records->links() }}</div>@endif
</div>
@endsection
