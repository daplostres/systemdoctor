@extends('layouts.admin')
@section('title', $letter->type_label)

@section('content')
@php($s = \App\Models\Setting::allCached())
<div class="d-flex gap-2 mb-3 d-print-none mx-auto" style="max-width: 780px">
    <a href="{{ route('admin.letters.index') }}" class="btn btn-light"><i class="bi bi-arrow-left"></i> Daftar Surat</a>
    <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="bi bi-printer me-1"></i> Cetak Surat</button>
</div>

<div class="card mx-auto" style="max-width: 780px">
    <div class="card-body p-4 p-md-5">
        {{-- Kop Surat --}}
        <div class="d-flex align-items-center border-bottom border-2 border-dark pb-3 mb-4">
            <span class="brand-icon me-3" style="width:54px;height:54px;font-size:1.5rem"><i class="bi bi-heart-pulse-fill"></i></span>
            <div>
                <h4 class="fw-bold mb-0">{{ strtoupper($s['clinic_name'] ?? 'PRAKTEK DR. EKO ANDRIANTO') }}</h4>
                <div class="small fw-semibold">{{ $s['doctor_name'] ?? 'dr. Eko Andrianto' }} · {{ $s['doctor_sip'] ?? '' }}</div>
                <div class="small text-secondary">{{ $s['address'] ?? '' }} · Telp: {{ $s['phone'] ?? '' }}</div>
            </div>
        </div>

        <div class="text-center mb-4">
            <h5 class="fw-bold text-decoration-underline mb-1">{{ strtoupper($letter->type_label) }}</h5>
            <div class="small font-monospace">Nomor: {{ $letter->letter_no }}</div>
        </div>

        @if($letter->type === 'referral')
            <div class="mb-3">
                <div>Kepada Yth.</div>
                <div class="fw-bold">{{ $letter->referral_specialist ?? 'Dokter Spesialis / Bagian Terkait' }}</div>
                <div class="fw-semibold">{{ $letter->referral_to ?? 'Rumah Sakit Tujuan' }}</div>
                <div>di Tempat</div>
            </div>
            <p>Dengan hormat,<br>Mohon pemeriksaan, penanganan, dan tindak lanjut medis terhadap pasien:</p>
        @else
            <p>Yang bertanda tangan di bawah ini, <strong>{{ $s['doctor_name'] ?? 'dr. Eko Andrianto' }}</strong>, menerangkan bahwa:</p>
        @endif

        <table class="table table-borderless table-sm ms-3 mb-3" style="max-width: 560px">
            <tr><td style="width:165px">Nama Lengkap</td><td>: <strong>{{ $letter->patient->name }}</strong> ({{ $letter->patient->medical_record_no }})</td></tr>
            <tr><td>Jenis Kelamin / Umur</td><td>: {{ $letter->patient->gender_label }} / {{ $letter->patient->age ?? '-' }}</td></tr>
            <tr><td>Pekerjaan</td><td>: {{ $letter->occupation ?? '-' }}</td></tr>
            <tr><td>Alamat</td><td>: {{ $letter->patient->address ?? '-' }}</td></tr>
        </table>

        @if($letter->type === 'sick')
            <p class="lh-lg">
                Berdasarkan hasil pemeriksaan medis yang telah dilakukan, pasien tersebut di atas dalam keadaan <strong>SAKIT</strong> sehingga memerlukan istirahat selama
                <strong>{{ $letter->rest_days }} hari</strong>, terhitung mulai tanggal
                <strong>{{ $letter->start_date?->translatedFormat('d F Y') }}</strong> sampai dengan tanggal
                <strong>{{ $letter->end_date?->translatedFormat('d F Y') }}</strong>.
            </p>
        @elseif($letter->type === 'healthy')
            <p>Telah dilakukan pemeriksaan fisik dengan hasil sebagai berikut:</p>
            <div class="row g-2 bg-light rounded-3 p-3 mb-3 small">
                <div class="col-sm-4">Tinggi Badan: <strong>{{ $letter->height ?? '-' }} cm</strong></div>
                <div class="col-sm-4">Berat Badan: <strong>{{ $letter->weight ?? '-' }} kg</strong></div>
                <div class="col-sm-4">Tekanan Darah: <strong>{{ $letter->blood_pressure ?? '-' }} mmHg</strong></div>
                <div class="col-sm-4">Suhu Tubuh: <strong>{{ $letter->temperature ?? '-' }} °C</strong></div>
                <div class="col-sm-4">Golongan Darah: <strong>{{ $letter->patient->blood_type ?? '-' }}</strong></div>
                <div class="col-sm-4">Buta Warna: <strong>{{ $letter->color_blind ?? '-' }}</strong></div>
            </div>
            <p class="lh-lg">
                Berdasarkan pemeriksaan fisik tersebut, yang bersangkutan dinyatakan dalam keadaan <strong>SEHAT JASMANI</strong>.
                @if($letter->purpose)
                    Surat keterangan ini dibuat untuk keperluan: <strong>{{ $letter->purpose }}</strong>.
                @endif
            </p>
        @else
            <div class="bg-light rounded-3 p-3 mb-3">
                <div class="mb-2">
                    <div class="label-sm">Anamnesis & Pemeriksaan Fisik</div>
                    <div>{!! nl2br(e($letter->anamnesis ?? '-')) !!}</div>
                </div>
                <div class="mb-2">
                    <div class="label-sm">Diagnosis Sementara (Working Diagnosis)</div>
                    <div class="fw-bold">{{ $letter->provisional_diagnosis ?? '-' }}</div>
                </div>
                <div>
                    <div class="label-sm">Terapi / Tindakan yang Telah Diberikan</div>
                    <div>{!! nl2br(e($letter->given_therapy ?? '-')) !!}</div>
                </div>
            </div>
            <p>Atas perhatian dan kerja sama Sejawat, kami ucapkan terima kasih.</p>
        @endif

        @if($letter->notes)
            <p class="small text-secondary">Catatan: {{ $letter->notes }}</p>
        @endif

        @if($letter->type !== 'referral')
            <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
        @endif

        <div class="d-flex justify-content-end mt-5">
            <div class="text-center" style="min-width: 230px">
                <div>Semarang, {{ $letter->issue_date->translatedFormat('d F Y') }}</div>
                <div class="small text-secondary mb-5">Dokter Pemeriksa,</div>
                <div class="fw-bold text-decoration-underline mt-4">{{ $s['doctor_name'] ?? 'dr. Eko Andrianto' }}</div>
                <div class="small text-secondary">{{ $s['doctor_sip'] ?? '' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
