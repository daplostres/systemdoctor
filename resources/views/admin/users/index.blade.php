@extends('layouts.admin')
@section('title', 'Kelola Akun Petugas')

@section('content')
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-plus me-1 text-primary"></i> Tambah Akun Petugas Baru</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap *</label>
                        <input name="name" class="form-control" placeholder="Contoh: Rini (Bag. Obat)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Login *</label>
                        <input type="email" name="email" class="form-control" placeholder="nama@praktek.test" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Peran / Bagian (Role) *</label>
                        <select name="role" class="form-select" required>
                            @foreach(\App\Models\User::ROLES as $roleKey => $roleLabel)
                                <option value="{{ $roleKey }}">{{ $roleLabel }}</option>
                            @endforeach
                        </select>
                        <div class="form-text small mt-2">
                            <ul class="ps-3 mb-0">
                                <li><strong>Petugas Pendaftaran:</strong> hanya melihat menu Antrean On-Site & Data Pasien.</li>
                                <li><strong>Petugas Rekam Medis:</strong> hanya melihat menu Data Pasien, Rekam Medis & Surat Sakit/Sehat.</li>
                                <li><strong>Petugas Obat / Apotek:</strong> hanya melihat menu Stok Obat & Resep/Kuitansi.</li>
                                <li><strong>Dokter:</strong> akses penuh ke seluruh fitur & laporan keuangan.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password *</label>
                        <input type="password" name="password" minlength="6" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Tambah Akun</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">Daftar Akun Berdasarkan Bagian</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Nama</th><th>Email Login</th><th>Bagian / Hak Akses</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td class="fw-semibold">{{ $u->name }}</td>
                            <td class="font-monospace small">{{ $u->email }}</td>
                            <td>
                                <span class="badge bg-{{ $u->role_badge }}">{{ $u->role_label }}</span>
                            </td>
                            <td class="text-end">
                                @if($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus akun {{ $u->name }}?">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-light text-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <span class="small text-secondary">Sedang digunakan</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
