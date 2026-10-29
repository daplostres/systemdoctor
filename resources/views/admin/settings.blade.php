@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="row g-3">
    @if(auth()->user()->isAdmin())
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-hospital me-1 text-primary"></i> Profil Praktek & Dokter</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.settings.update') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Nama Praktek *</label><input name="clinic_name" class="form-control" value="{{ old('clinic_name', $settings['clinic_name'] ?? '') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Nama Dokter *</label><input name="doctor_name" class="form-control" value="{{ old('doctor_name', $settings['doctor_name'] ?? '') }}" required></div>
                        <div class="col-md-6"><label class="form-label">Spesialisasi</label><input name="doctor_title" class="form-control" value="{{ old('doctor_title', $settings['doctor_title'] ?? '') }}"></div>
                        <div class="col-md-6"><label class="form-label">No. SIP</label><input name="doctor_sip" class="form-control" value="{{ old('doctor_sip', $settings['doctor_sip'] ?? '') }}"></div>
                        <div class="col-12"><label class="form-label">Tagline</label><input name="tagline" class="form-control" value="{{ old('tagline', $settings['tagline'] ?? '') }}"></div>
                        <div class="col-12"><label class="form-label">Biografi Dokter</label><textarea name="doctor_bio" rows="4" class="form-control">{{ old('doctor_bio', $settings['doctor_bio'] ?? '') }}</textarea></div>
                        <div class="col-12"><label class="form-label">Alamat Praktek</label><textarea name="address" rows="2" class="form-control">{{ old('address', $settings['address'] ?? '') }}</textarea></div>
                        <div class="col-md-4"><label class="form-label">Telepon</label><input name="phone" class="form-control" value="{{ old('phone', $settings['phone'] ?? '') }}"></div>
                        <div class="col-md-4"><label class="form-label">WhatsApp</label><input name="whatsapp" class="form-control" placeholder="628xxxxxxxxxx" value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}"></div>
                        <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $settings['email'] ?? '') }}"></div>
                        <div class="col-12">
                            <label class="form-label">URL Embed Google Maps</label>
                            <input name="maps_embed" class="form-control" value="{{ old('maps_embed', $settings['maps_embed'] ?? '') }}">
                            <div class="form-text">Google Maps → Bagikan → Sematkan peta → salin nilai <code>src</code> dari kode iframe.</div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i> Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-4">
        @if(auth()->user()->isAdmin())
        <div class="card mb-3 border-primary-subtle">
            <div class="card-header"><i class="bi bi-database-down me-1 text-primary"></i> Backup Data Praktek</div>
            <div class="card-body">
                <p class="small text-secondary mb-3">
                    Unduh cadangan seluruh data pasien, rekam medis, stok obat, antrean, dan pengaturan dalam format <code>.sql</code> untuk disimpan secara berkala.
                </p>
                <a href="{{ route('admin.settings.backup') }}" class="btn btn-outline-primary w-100">
                    <i class="bi bi-download me-1"></i> Download Backup Database (.sql)
                </a>
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header"><i class="bi bi-shield-lock me-1 text-primary"></i> Ganti Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.password') }}">
                    @csrf @method('PUT')
                    <div class="mb-3"><label class="form-label">Password Saat Ini</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password Baru</label><input type="password" name="password" class="form-control" required minlength="8"></div>
                    <div class="mb-3"><label class="form-label">Ulangi Password Baru</label><input type="password" name="password_confirmation" class="form-control" required></div>
                    <button class="btn btn-primary w-100">Ubah Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

