@extends('layouts.admin')
@section('title', $service->exists ? 'Edit Layanan' : 'Tambah Layanan')

@section('content')
<div class="card" style="max-width: 720px">
    <div class="card-body p-4">
        <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}">
            @csrf
            @if($service->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Nama Layanan *</label>
                <input name="name" class="form-control" value="{{ old('name', $service->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="description" rows="3" class="form-control">{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tarif *</label>
                    <div class="input-group"><span class="input-group-text">Rp</span>
                        <input type="number" min="0" name="price" class="form-control" value="{{ old('price', $service->price ?? 0) }}" required></div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ikon *</label>
                    <select name="icon" class="form-select" id="icon">
                        @foreach(['bi-heart-pulse', 'bi-chat-heart', 'bi-emoji-smile', 'bi-droplet-half', 'bi-bandaid', 'bi-file-earmark-medical', 'bi-house-heart', 'bi-capsule', 'bi-thermometer-half', 'bi-lungs', 'bi-eye', 'bi-prescription2', 'bi-hospital', 'bi-person-wheelchair', 'bi-virus'] as $icon)
                            <option value="{{ $icon }}" @selected(old('icon', $service->icon) === $icon)>{{ $icon }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2 fs-3 text-primary"><i id="icon-preview" class="bi {{ old('icon', $service->icon) }}"></i></div>
                </div>
            </div>
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active', $service->is_active))>
                <label class="form-check-label" for="active">Tampilkan di website</label>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('icon').addEventListener('change', e => document.getElementById('icon-preview').className = 'bi ' + e.target.value);
</script>
@endpush

