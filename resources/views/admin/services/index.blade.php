@extends('layouts.admin')
@section('title', 'Layanan')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <span>Daftar Layanan</span>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg"></i> Tambah Layanan</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Layanan</th><th>Tarif</th><th>Dipakai</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($services as $s)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="service-icon mb-0" style="width:42px;height:42px;font-size:1.2rem;border-radius:12px"><i class="bi {{ $s->icon }}"></i></div>
                            <div>
                                <div class="fw-semibold">{{ $s->name }}</div>
                                <small class="text-secondary">{{ \Illuminate\Support\Str::limit($s->description, 60) }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $s->price_formatted }}</td>
                    <td>{{ $s->appointments_count }}x</td>
                    <td>{!! $s->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.services.edit', $s) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.services.destroy', $s) }}" class="d-inline" data-confirm="Hapus layanan ini?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada layanan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

