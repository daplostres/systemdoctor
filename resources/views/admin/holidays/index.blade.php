@extends('layouts.admin')
@section('title', 'Pengumuman Tanggal Libur / Tutup Sementara')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-plus me-1 text-primary"></i> Tambah Jadwal Tutup Sementara</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.holidays.store') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label">Dari Tanggal *</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Sampai Tanggal *</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', now()->toDateString()) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan / Keterangan Pengumuman *</label>
                        <input name="reason" class="form-control" placeholder="Contoh: Libur Hari Raya / Seminar Medis" required>
                        <div class="form-text">Akan otomatis ditampilkan pada banner pengumuman di halaman depan website.</div>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Jadwal Libur</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Daftar Tanggal Libur Praktek</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Tanggal</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    @forelse($holidays as $h)
                        <tr>
                            <td class="fw-semibold">
                                {{ $h->start_date->translatedFormat('d M Y') }}
                                @if(!$h->start_date->isSameDay($h->end_date))
                                    – {{ $h->end_date->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td>{{ $h->reason }}</td>
                            <td>
                                @if($h->end_date->isPast())
                                    <span class="badge bg-secondary">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Aktif / Mendatang</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.holidays.destroy', $h) }}" data-confirm="Hapus jadwal libur ini?">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">Belum ada jadwal libur sementara.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

