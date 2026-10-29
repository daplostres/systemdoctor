@extends('layouts.admin')
@section('title', 'Riwayat Mutasi Stok Obat')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <a href="{{ route('admin.medicines.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Kembali ke Stok Obat</a>
        <form class="d-flex flex-wrap gap-2 ms-auto" method="GET">
            <select name="medicine_id" class="form-select" style="width:auto">
                <option value="">Semua Obat</option>
                @foreach($medicines as $m)
                    <option value="{{ $m->id }}" @selected(request('medicine_id') == $m->id)>{{ $m->name }} ({{ $m->code }})</option>
                @endforeach
            </select>
            <select name="type" class="form-select" style="width:auto">
                <option value="">Semua Jenis</option>
                <option value="in" @selected(request('type') === 'in')>Stok Masuk (+)</option>
                <option value="out" @selected(request('type') === 'out')>Stok Keluar (-)</option>
            </select>
            <button class="btn btn-primary"><i class="bi bi-funnel"></i> Filter</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>Waktu</th>
                <th>Nama Obat</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Sisa Stok Akhir</th>
                <th>Keterangan</th>
                <th>Petugas</th>
            </tr>
            </thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="small text-secondary">{{ $log->created_at->translatedFormat('d M Y H:i') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $log->medicine->name }}</div>
                        <small class="text-secondary font-monospace">{{ $log->medicine->code }}</small>
                    </td>
                    <td>
                        @if($log->type === 'in')
                            <span class="badge bg-success-subtle text-success"><i class="bi bi-arrow-down-left"></i> Masuk</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger"><i class="bi bi-arrow-up-right"></i> Keluar</span>
                        @endif
                    </td>
                    <td class="fw-bold {{ $log->type === 'in' ? 'text-success' : 'text-danger' }}">
                        {{ $log->type === 'in' ? '+' : '-' }}{{ $log->qty }} {{ $log->medicine->unit }}
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $log->stock_after }} {{ $log->medicine->unit }}</span></td>
                    <td class="small">{{ $log->description ?? '-' }}</td>
                    <td class="small text-secondary">{{ $log->user->name ?? 'Sistem' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada riwayat mutasi stok obat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())<div class="card-body">{{ $logs->links() }}</div>@endif
</div>
@endsection

