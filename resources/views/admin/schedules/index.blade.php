@extends('layouts.admin')
@section('title', 'Jadwal Praktek')

@section('content')
<div class="card" style="max-width: 900px">
    <div class="card-header d-flex align-items-center">
        <span>Jadwal Mingguan</span>
        <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary ms-auto"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Hari</th><th>Jam</th><th>Kuota/hari</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            @forelse($schedules as $sch)
                <tr>
                    <td class="fw-semibold">{{ $sch->day_name }}</td>
                    <td>{{ $sch->time_range }}</td>
                    <td>{{ $sch->quota }} pasien</td>
                    <td class="small text-secondary">{{ $sch->note ?? '-' }}</td>
                    <td>{!! $sch->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Libur</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.schedules.edit', $sch) }}" class="btn btn-sm btn-light"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.schedules.destroy', $sch) }}" class="d-inline" data-confirm="Hapus jadwal ini?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada jadwal.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body small text-secondary border-top">
        <i class="bi bi-info-circle"></i> Pasien hanya bisa booking online pada hari yang memiliki jadwal aktif. Kuota membatasi jumlah booking per tanggal.
    </div>
</div>
@endsection

