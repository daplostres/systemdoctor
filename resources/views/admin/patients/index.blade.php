@extends('layouts.admin')
@section('title', 'Data Pasien')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2" method="GET">
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama, No. RM, HP, NIK...">
            <button class="btn btn-light"><i class="bi bi-search"></i></button>
        </form>
        <a href="{{ route('admin.patients.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg"></i> Pasien Baru</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>No. RM</th><th>Nama</th><th>JK / Umur</th><th>No. HP</th><th>Kunjungan</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($patients as $p)
                <tr>
                    <td class="font-monospace small">{{ $p->medical_record_no }}</td>
                    <td><a href="{{ route('admin.patients.show', $p) }}" class="fw-semibold text-decoration-none">{{ $p->name }}</a></td>
                    <td>{{ $p->gender }} · {{ $p->age ?? '-' }}</td>
                    <td>{{ $p->phone }}</td>
                    <td><span class="badge bg-primary-subtle text-primary">{{ $p->medical_records_count }}x</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.patients.show', $p) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('admin.patients.edit', $p) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Data pasien tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($patients->hasPages())<div class="card-body">{{ $patients->links() }}</div>@endif
</div>
@endsection

