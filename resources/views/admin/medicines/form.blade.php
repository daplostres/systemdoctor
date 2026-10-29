@extends('layouts.admin')
@section('title', $medicine->exists ? 'Edit Obat' : 'Tambah Obat Baru')

@section('content')
<div class="card" style="max-width: 720px">
    <div class="card-body p-4">
        <form method="POST" action="{{ $medicine->exists ? route('admin.medicines.update', $medicine) : route('admin.medicines.store') }}">
            @csrf
            @if($medicine->exists) @method('PUT') @endif

            <div class="row g-3">
                @if($medicine->exists)
                    <div class="col-md-4">
                        <label class="form-label">Kode Obat</label>
                        <input class="form-control font-monospace" value="{{ $medicine->code }}" disabled>
                    </div>
                @endif
                <div class="col-md-{{ $medicine->exists ? 8 : 8 }}">
                    <label class="form-label">Nama Obat *</label>
                    <input name="name" class="form-control" placeholder="Contoh: Paracetamol 500mg" value="{{ old('name', $medicine->name) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Kategori</label>
                    <input name="category" class="form-control" placeholder="Antibiotik, Analgesik, dll" value="{{ old('category', $medicine->category) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Satuan *</label>
                    <select name="unit" class="form-select" required>
                        @foreach(\App\Models\Medicine::UNITS as $u)
                            <option value="{{ $u }}" @selected(old('unit', $medicine->unit) === $u)>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Harga Jual / Satuan *</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" min="0" name="price" class="form-control" value="{{ old('price', $medicine->price) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Stok Saat Ini *</label>
                    <input type="number" min="0" name="stock" class="form-control" value="{{ old('stock', $medicine->stock) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Batas Stok Minimum *</label>
                    <input type="number" min="0" name="min_stock" class="form-control" value="{{ old('min_stock', $medicine->min_stock) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Kedaluwarsa (Expired)</label>
                    <input type="date" name="expired_date" class="form-control" value="{{ old('expired_date', $medicine->expired_date?->toDateString()) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan / Keterangan</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Keterangan penyimpanan atau dosis umum">{{ old('notes', $medicine->notes) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.medicines.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

