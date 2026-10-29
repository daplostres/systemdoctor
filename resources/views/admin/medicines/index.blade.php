@extends('layouts.admin')
@section('title', 'Stok Obat')

@section('content')
<div class="row g-2 mb-3">
    @if($lowStockCount > 0)
        <div class="col-md-6">
            <div class="alert alert-warning d-flex align-items-center justify-content-between mb-0">
                <div><i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>{{ $lowStockCount }}</strong> obat dengan stok menipis / habis.</div>
                <a href="{{ route('admin.medicines.index', ['filter' => 'low']) }}" class="btn btn-sm btn-warning">Filter Stok Tipis</a>
            </div>
        </div>
    @endif
    @if($expiringCount > 0)
        <div class="col-md-6">
            <div class="alert alert-danger d-flex align-items-center justify-content-between mb-0">
                <div><i class="bi bi-calendar-x-fill me-2"></i> <strong>{{ $expiringCount }}</strong> obat sudah / mendekati kedaluwarsa (≤ 60 hari).</div>
                <a href="{{ route('admin.medicines.index', ['filter' => 'expiring']) }}" class="btn btn-sm btn-danger">Filter Kedaluwarsa</a>
            </div>
        </div>
    @endif
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <input name="q" value="{{ request('q') }}" class="form-control" style="width:220px" placeholder="Cari nama, kode, kategori...">
            <select name="filter" class="form-select" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua Obat</option>
                <option value="low" @selected(request('filter') === 'low')>Stok Menipis</option>
                <option value="expiring" @selected(request('filter') === 'expiring')>Hampir / Sudah Expired</option>
            </select>
            <button class="btn btn-light"><i class="bi bi-search"></i></button>
        </form>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('admin.medicines.logs') }}" class="btn btn-outline-primary"><i class="bi bi-clock-history me-1"></i> Riwayat Mutasi Stok</a>
            <a href="{{ route('admin.medicines.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Obat</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Obat</th>
                <th>Harga / Satuan</th>
                <th>Stok Saat Ini</th>
                <th>Tgl. Kedaluwarsa (ED)</th>
                <th>Tambah / Kurangi Stok</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($medicines as $m)
                <tr>
                    <td class="font-monospace small">{{ $m->code }}</td>
                    <td>
                        <div class="fw-semibold">{{ $m->name }}</div>
                        <small class="text-secondary">{{ $m->category ?? 'Umum' }}</small>
                    </td>
                    <td>{{ $m->price_formatted }} <small class="text-secondary">/ {{ $m->unit }}</small></td>
                    <td>
                        @if($m->is_low_stock)
                            <span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ $m->stock }} {{ $m->unit }}</span>
                            <div class="small text-danger">Min: {{ $m->min_stock }}</div>
                        @else
                            <span class="badge bg-success-subtle text-success fs-6">{{ $m->stock }} {{ $m->unit }}</span>
                        @endif
                    </td>
                    <td>
                        @if(!$m->expired_date)
                            <span class="text-secondary small">-</span>
                        @elseif($m->is_expired)
                            <span class="badge bg-danger"><i class="bi bi-x-octagon me-1"></i>EXPIRED ({{ $m->expired_date->translatedFormat('d M Y') }})</span>
                        @elseif($m->is_near_expiry)
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>{{ $m->expired_date->translatedFormat('d M Y') }}</span>
                            <div class="small text-danger">Segera kedaluwarsa</div>
                        @else
                            <span class="small">{{ $m->expired_date->translatedFormat('d M Y') }}</span>
                        @endif
                    </td>
                    <td style="max-width:230px">
                        <form method="POST" action="{{ route('admin.medicines.stock', $m) }}" class="d-flex gap-1">
                            @csrf @method('PATCH')
                            <select name="type" class="form-select form-select-sm" style="width:95px">
                                <option value="in">+ Masuk</option>
                                <option value="out">- Keluar</option>
                            </select>
                            <input type="number" name="amount" min="1" value="10" class="form-control form-control-sm" style="width:70px" required>
                            <button class="btn btn-sm btn-outline-primary" title="Update Stok"><i class="bi bi-check2"></i></button>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.medicines.logs', ['medicine_id' => $m->id]) }}" class="btn btn-sm btn-light" title="Riwayat Mutasi"><i class="bi bi-clock-history"></i></a>
                        <a href="{{ route('admin.medicines.edit', $m) }}" class="btn btn-sm btn-light" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.medicines.destroy', $m) }}" class="d-inline" data-confirm="Hapus obat {{ $m->name }}?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data obat.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($medicines->hasPages())<div class="card-body">{{ $medicines->links() }}</div>@endif
</div>
@endsection
