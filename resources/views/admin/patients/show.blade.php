@extends('layouts.admin')
@section('title', 'Detail Pasien')

@section('content')
@php
    // Siapkan data grafik tanda vital berurutan dari kunjungan paling lama ke terbaru
    $vitals = $patient->medicalRecords->sortBy('visit_date')->values()->map(function ($r) {
        $sys = null;
        $dia = null;
        if ($r->blood_pressure && preg_match('/(\d{2,3})\s*\/\s*(\d{2,3})/', $r->blood_pressure, $m)) {
            $sys = (int) $m[1];
            $dia = (int) $m[2];
        }
        return [
            'date' => $r->visit_date->translatedFormat('d M y'),
            'sys' => $sys,
            'dia' => $dia,
            'weight' => $r->weight ? (float) $r->weight : null,
            'temp' => $r->temperature ? (float) $r->temperature : null,
        ];
    });
@endphp

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center p-4">
                <div class="hero-avatar mb-3" style="width:90px;height:90px;font-size:2.5rem">
                    <i class="bi {{ $patient->gender === 'L' ? 'bi-person' : 'bi-person-heart' }}"></i>
                </div>
                <h5 class="fw-bold mb-0">{{ $patient->name }}</h5>
                <div class="font-monospace text-primary">{{ $patient->medical_record_no }}</div>
            </div>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex"><span class="text-secondary">Jenis Kelamin</span><span class="ms-auto">{{ $patient->gender_label }}</span></li>
                <li class="list-group-item d-flex"><span class="text-secondary">Tgl. Lahir</span><span class="ms-auto">{{ $patient->birth_date?->translatedFormat('d M Y') ?? '-' }} {{ $patient->age ? '('.$patient->age.')' : '' }}</span></li>
                <li class="list-group-item d-flex"><span class="text-secondary">NIK</span><span class="ms-auto">{{ $patient->nik ?? '-' }}</span></li>
                <li class="list-group-item d-flex"><span class="text-secondary">No. HP</span><span class="ms-auto">{{ $patient->phone }}</span></li>
                <li class="list-group-item d-flex"><span class="text-secondary">Gol. Darah</span><span class="ms-auto">{{ $patient->blood_type ?? '-' }}</span></li>
                <li class="list-group-item"><span class="text-secondary">Alamat</span><div>{{ $patient->address ?? '-' }}</div></li>
                <li class="list-group-item"><span class="text-secondary">Alergi</span><div class="{{ $patient->allergies ? 'text-danger fw-semibold' : '' }}">{{ $patient->allergies ?? 'Tidak ada' }}</div></li>
            </ul>
            <div class="card-body d-flex flex-wrap gap-2">
                @if(auth()->user()->hasRole('doctor', 'medical_record'))
                    <a href="{{ route('admin.records.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary btn-sm"><i class="bi bi-clipboard2-plus"></i> Periksa / Isi RM</a>
                    <a href="{{ route('admin.letters.create', ['patient_id' => $patient->id, 'type' => 'sick']) }}" class="btn btn-outline-warning btn-sm"><i class="bi bi-file-earmark-medical"></i> Surat</a>
                    <a href="{{ route('admin.letters.create', ['patient_id' => $patient->id, 'type' => 'referral']) }}" class="btn btn-outline-info btn-sm"><i class="bi bi-hospital"></i> Rujuk</a>
                @endif
                @if(auth()->user()->hasRole('doctor', 'assistant'))
                    <a href="{{ route('admin.appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-person-lines-fill"></i> Ambil Antrean</a>
                @endif
                <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-light btn-sm"><i class="bi bi-pencil"></i> Edit</a>
                <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}" data-confirm="Hapus pasien beserta seluruh riwayatnya?">
                    @csrf @method('DELETE')
                    <button class="btn btn-light btn-sm text-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        {{-- GRAFIK PERKEMBANGAN TANDA VITAL PASIEN --}}
        @if($vitals->count() >= 1)
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span><i class="bi bi-activity text-primary me-1"></i> Grafik Riwayat Tanda Vital (Tekanan Darah & Berat Badan)</span>
                </div>
                <div class="card-body">
                    <canvas id="vitalsChart" height="105"></canvas>
                </div>
            </div>
        @endif

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-journal-medical text-primary me-1"></i> Riwayat Rekam Medis ({{ $patient->medicalRecords->count() }})</div>
            <div class="card-body">
                @forelse($patient->medicalRecords as $r)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                            <span class="fw-bold"><i class="bi bi-calendar3 me-1"></i> {{ $r->visit_date->translatedFormat('d F Y') }}</span>
                            <span class="badge bg-light text-dark border">TD: {{ $r->blood_pressure ?? '-' }} mmHg</span>
                            <span class="badge bg-light text-dark border">BB: {{ $r->weight ? $r->weight.' kg' : '-' }}</span>
                            <span class="badge bg-light text-dark border">Suhu: {{ $r->temperature ? $r->temperature.' °C' : '-' }}</span>
                            <a href="{{ route('admin.records.show', $r) }}" class="btn btn-sm btn-light ms-auto">Detail</a>
                        </div>
                        <div class="row small g-2">
                            <div class="col-md-6"><div class="label-sm">Keluhan</div>{{ $r->complaint }}</div>
                            <div class="col-md-6"><div class="label-sm">Diagnosis</div><span class="fw-semibold">{{ $r->diagnosis }}</span></div>
                            @if($r->prescription)
                                <div class="col-12"><div class="label-sm">Resep</div>{!! nl2br(e($r->prescription)) !!}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-secondary mb-0">Belum ada rekam medis.</p>
                @endforelse
            </div>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-check text-primary me-1"></i> Riwayat Kunjungan & Antrean</div>
            <div class="table-responsive">
                <table class="table mb-0 align-middle small">
                    <thead><tr><th>Kode</th><th>Tanggal</th><th>Layanan</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($patient->appointments as $a)
                        <tr>
                            <td><a href="{{ route('admin.appointments.show', $a) }}" class="font-monospace">{{ $a->code }}</a></td>
                            <td>{{ $a->appointment_date->translatedFormat('d M Y') }}</td>
                            <td>{{ $a->service->name ?? 'Pemeriksaan Umum' }}</td>
                            <td><span class="badge bg-{{ $a->status_color }}">{{ $a->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">Belum ada.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($vitals->count() >= 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('vitalsChart'), {
        type: 'line',
        data: {
            labels: @json($vitals->pluck('date')),
            datasets: [
                {
                    label: 'Tensi Sistolik (mmHg)',
                    data: @json($vitals->pluck('sys')),
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220,53,69,.1)',
                    tension: 0.3,
                    spanGaps: true
                },
                {
                    label: 'Tensi Diastolik (mmHg)',
                    data: @json($vitals->pluck('dia')),
                    borderColor: '#fd7e14',
                    backgroundColor: 'rgba(253,126,20,.1)',
                    tension: 0.3,
                    spanGaps: true
                },
                {
                    label: 'Berat Badan (kg)',
                    data: @json($vitals->pluck('weight')),
                    borderColor: '#0e9f8f',
                    backgroundColor: 'rgba(14,159,143,.1)',
                    tension: 0.3,
                    spanGaps: true
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endif
@endpush
