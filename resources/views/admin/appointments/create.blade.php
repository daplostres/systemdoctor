@extends('layouts.admin')
@section('title', 'Pendaftaran Antrean di Tempat (On-Site)')

@section('content')
<div class="card" style="max-width: 820px">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.appointments.store') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">Jenis Pasien</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="patient_mode" id="mode-existing" value="existing" @checked(old('patient_mode', 'existing') === 'existing')>
                    <label class="btn btn-outline-primary" for="mode-existing"><i class="bi bi-person-check me-1"></i> Pasien Lama (Sudah Terdaftar)</label>

                    <input type="radio" class="btn-check" name="patient_mode" id="mode-new" value="new" @checked(old('patient_mode') === 'new')>
                    <label class="btn btn-outline-primary" for="mode-new"><i class="bi bi-person-plus me-1"></i> Pasien Baru Pertama Kali Datang</label>
                </div>
            </div>

            {{-- Form Pilih Pasien Lama --}}
            <div id="section-existing" class="mb-3">
                <label class="form-label">Cari / Pilih Pasien Lama *</label>
                <select name="patient_id" id="patient-select" class="form-select form-select-lg">
                    <option value="">- Pilih pasien -</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" @selected(old('patient_id', $selectedPatient) == $p->id)>
                            {{ $p->name }} — {{ $p->medical_record_no }} ({{ $p->phone }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Form Input Cepat Pasien Baru --}}
            <div id="section-new" class="border rounded-4 p-3 mb-3 bg-light" style="display:none">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-vcard me-1"></i> Data Pasien Baru (No. RM Dibuat Otomatis)</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap *</label>
                        <input name="name" class="form-control" value="{{ old('name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. HP / WhatsApp *</label>
                        <input name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jenis Kelamin *</label>
                        <select name="gender" class="form-select">
                            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Lahir</label>
                        <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date') }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Alamat</label>
                        <input name="address" class="form-control" value="{{ old('address') }}" placeholder="Tambakaji, Ngaliyan, Semarang">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Alergi Obat (Jika Ada)</label>
                        <input name="allergies" class="form-control" value="{{ old('allergies') }}" placeholder="Misal: Amoxicillin">
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Tanggal Kunjungan *</label>
                    <input type="date" name="appointment_date" class="form-control" value="{{ old('appointment_date', now()->toDateString()) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Layanan</label>
                    <select name="service_id" class="form-select">
                        <option value="">Pemeriksaan Umum</option>
                        @foreach($services as $s)
                            <option value="{{ $s->id }}" @selected(old('service_id') == $s->id)>{{ $s->name }} ({{ $s->price_formatted }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Keluhan Singkat</label>
                <textarea name="complaint" rows="2" class="form-control" placeholder="Contoh: Demam 2 hari, batuk berdahak">{{ old('complaint') }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-lg"><i class="bi bi-printer me-1"></i> Simpan & Cetak Nomor Antrean</button>
                <a href="{{ route('admin.appointments.index') }}" class="btn btn-light btn-lg">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const modeExisting = document.getElementById('mode-existing');
    const modeNew = document.getElementById('mode-new');
    const secExisting = document.getElementById('section-existing');
    const secNew = document.getElementById('section-new');

    function toggleMode() {
        const isNew = modeNew.checked;
        secExisting.style.display = isNew ? 'none' : 'block';
        secNew.style.display = isNew ? 'block' : 'none';
    }
    modeExisting.addEventListener('change', toggleMode);
    modeNew.addEventListener('change', toggleMode);
    toggleMode();
</script>
@endpush
