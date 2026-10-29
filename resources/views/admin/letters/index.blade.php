@extends('layouts.admin')
@section('title', 'Surat Sakit, Sehat & Rujukan')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 align-items-center">
        <form class="d-flex gap-2" method="GET">
            <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari No. Surat / nama pasien...">
            <select name="type" class="form-select" style="width:auto" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="sick" @selected(request('type') === 'sick')>Surat Sakit</option>
                <option value="healthy" @selected(request('type') === 'healthy')>Surat Sehat</option>
                <option value="referral" @selected(request('type') === 'referral')>Surat Rujukan</option>
            </select>
            <button class="btn btn-light"><i class="bi bi-search"></i></button>
        </form>
        <div class="ms-auto d-flex flex-wrap gap-2">
            <a href="{{ route('admin.letters.create', ['type' => 'sick']) }}" class="btn btn-warning"><i class="bi bi-bandaid me-1"></i> + Surat Sakit</a>
            <a href="{{ route('admin.letters.create', ['type' => 'healthy']) }}" class="btn btn-success"><i class="bi bi-patch-check me-1"></i> + Surat Sehat</a>
            <a href="{{ route('admin.letters.create', ['type' => 'referral']) }}" class="btn btn-primary"><i class="bi bi-hospital me-1"></i> + Surat Rujukan</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>No. Surat</th>
                <th>Jenis Surat</th>
                <th>Tanggal Terbit</th>
                <th>Pasien</th>
                <th>Keterangan / Tujuan</th>
                <th class="text-end">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($letters as $l)
                <tr>
                    <td class="font-monospace small">{{ $l->letter_no }}</td>
                    <td>
                        @if($l->type === 'sick')
                            <span class="badge bg-warning text-dark">Surat Keterangan Sakit</span>
                        @elseif($l->type === 'healthy')
                            <span class="badge bg-success">Surat Keterangan Sehat</span>
                        @else
                            <span class="badge bg-primary">Surat Rujukan Medis</span>
                        @endif
                    </td>
                    <td>{{ $l->issue_date->translatedFormat('d M Y') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $l->patient->name }}</div>
                        <small class="text-secondary font-monospace">{{ $l->patient->medical_record_no }}</small>
                    </td>
                    <td class="small">
                        @if($l->type === 'sick')
                            Istirahat <strong>{{ $l->rest_days }} hari</strong> ({{ $l->start_date?->translatedFormat('d M') }} – {{ $l->end_date?->translatedFormat('d M Y') }})
                        @elseif($l->type === 'healthy')
                            Keperluan: {{ $l->purpose ?? '-' }}
                        @else
                            Rujuk ke: <strong>{{ $l->referral_to }}</strong> ({{ $l->referral_specialist ?? '-' }})
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.letters.show', $l) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer me-1"></i> Lihat & Cetak</a>
                        <form method="POST" action="{{ route('admin.letters.destroy', $l) }}" class="d-inline" data-confirm="Hapus surat ini?">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada surat keterangan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($letters->hasPages())<div class="card-body">{{ $letters->links() }}</div>@endif
</div>
@endsection
