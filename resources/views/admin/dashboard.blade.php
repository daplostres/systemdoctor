@extends('layouts.admin')
@section('title', 'Dashboard ' . auth()->user()->role_label)

@section('content')
@php($u = auth()->user())

{{-- =======================================================================
     TAMPILAN KHUSUS PETUGAS OBAT / APOTEK (Fokus: Stok Obat & Resep Hari Ini)
     ======================================================================= --}}
@if($u->hasRole('pharmacist'))
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-primary-subtle text-primary"><i class="bi bi-capsule"></i></div>
                    <div>
                        <div class="text-secondary small">Total Jenis Obat</div>
                        <div class="fs-4 fw-bold">{{ $stats['medicines_total'] }} Obat</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-danger-subtle text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <div class="text-secondary small">Stok Menipis / Habis</div>
                        <div class="fs-4 fw-bold text-danger">{{ $stats['medicines_low'] }} Obat</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-success-subtle text-success"><i class="bi bi-receipt"></i></div>
                    <div>
                        <div class="text-secondary small">Resep Keluar Hari Ini</div>
                        <div class="fs-4 fw-bold">{{ $stats['records_today'] }} Resep</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center">
                    <span><i class="bi bi-prescription2 me-1 text-primary"></i> Resep Obat & Kuitansi Hari Ini</span>
                    <a href="{{ route('admin.records.index') }}" class="btn btn-sm btn-light ms-auto">Semua Resep</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>Invoice</th><th>Pasien</th><th>Resep Obat</th><th>Keterangan Dokter</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                        <tbody>
                        @forelse($todayPrescriptions as $r)
                            <tr class="{{ $r->pharmacy_status === 'pending' ? 'table-warning' : '' }}">
                                <td class="font-monospace small">{{ $r->invoice_no }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $r->patient->name }}</div>
                                    <small class="text-secondary font-monospace">{{ $r->patient->medical_record_no }}</small>
                                </td>
                                <td class="small">{!! nl2br(e($r->prescription ?? '-')) !!}</td>
                                <td class="small">
                                    @if($r->pharmacy_note)
                                        <span class="badge bg-warning text-dark text-wrap text-start"><i class="bi bi-chat-dots-fill me-1"></i>{{ $r->pharmacy_note }}</span>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($r->pharmacy_status === 'dispensed')
                                        <span class="badge bg-success">Sudah Diserahkan</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    @if($r->pharmacy_status === 'pending')
                                        <form method="POST" action="{{ route('admin.records.dispense', $r) }}" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-sm btn-success"><i class="bi bi-check2-circle me-1"></i> Serahkan</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.records.labels', $r) }}" class="btn btn-sm btn-outline-dark"><i class="bi bi-tag"></i> Etiket</a>
                                    <a href="{{ route('admin.records.receipt', $r) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-printer"></i> Kuitansi</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-secondary py-4">Belum ada resep hari ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-warning h-100">
                <div class="card-header d-flex align-items-center">
                    <span><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Obat Perlu Restock</span>
                    <a href="{{ route('admin.medicines.index', ['filter' => 'low']) }}" class="btn btn-sm btn-warning ms-auto">Kelola Stok</a>
                </div>
                <ul class="list-group list-group-flush">
                    @forelse($lowMedicines as $m)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $m->name }}</div>
                                <small class="text-secondary">Min: {{ $m->min_stock }} {{ $m->unit }}</small>
                            </div>
                            <span class="badge bg-danger">{{ $m->stock }} {{ $m->unit }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-secondary small">Semua stok obat aman.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

{{-- =======================================================================
     TAMPILAN KHUSUS PETUGAS REKAM MEDIS & SURAT
     ======================================================================= --}}
@elseif($u->hasRole('medical_record'))
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-primary-subtle text-primary"><i class="bi bi-journal-medical"></i></div>
                    <div>
                        <div class="text-secondary small">Rekam Medis Hari Ini</div>
                        <div class="fs-4 fw-bold">{{ $stats['records_today'] }} Kunjungan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-info-subtle text-info"><i class="bi bi-person-vcard"></i></div>
                    <div>
                        <div class="text-secondary small">Total Data Pasien</div>
                        <div class="fs-4 fw-bold">{{ $stats['patients'] }} Pasien</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon bg-warning-subtle text-warning"><i class="bi bi-file-earmark-medical"></i></div>
                    <div>
                        <div class="text-secondary small">Surat Sakit/Sehat Bulan Ini</div>
                        <div class="fs-4 fw-bold">{{ $stats['letters_month'] }} Surat</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center">
            <span><i class="bi bi-people me-1 text-primary"></i> Antrean Pasien Hari Ini (Klik "Catat RM" untuk Input Rekam Medis)</span>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('admin.letters.create', ['type' => 'sick']) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-bandaid"></i> + Surat Sakit</a>
                <a href="{{ route('admin.letters.create', ['type' => 'healthy']) }}" class="btn btn-sm btn-outline-success"><i class="bi bi-patch-check"></i> + Surat Sehat</a>
                <a href="{{ route('admin.records.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Rekam Medis Baru</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>No</th><th>Pasien</th><th>Keluhan</th><th>Status</th><th class="text-end">Aksi Rekam Medis</th></tr></thead>
                <tbody>
                @forelse($todayQueue as $a)
                    <tr>
                        <td><span class="queue-badge">{{ str_pad($a->queue_number, 2, '0', STR_PAD_LEFT) }}</span></td>
                        <td>
                            <a href="{{ route('admin.patients.show', $a->patient) }}" class="fw-semibold text-decoration-none">{{ $a->patient->name }}</a>
                            <div class="small text-secondary font-monospace">{{ $a->patient->medical_record_no }}</div>
                        </td>
                        <td class="small">{{ $a->complaint }}</td>
                        <td><span class="badge bg-{{ $a->status_color }}">{{ $a->status_label }}</span></td>
                        <td class="text-end">
                            @if($a->status !== 'completed')
                                <a href="{{ route('admin.records.create', ['appointment_id' => $a->id]) }}" class="btn btn-sm btn-primary"><i class="bi bi-clipboard2-pulse me-1"></i> Catat RM</a>
                            @else
                                <span class="badge bg-success-subtle text-success">Sudah Tercatat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada antrean hari ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

{{-- =======================================================================
     TAMPILAN DOKTER & PETUGAS PENDAFTARAN (Front Office)
     ======================================================================= --}}
@else
    <div class="row g-3 mb-4">
        @php($cards = [
            ['Total Antrean Hari Ini', $stats['today'], 'bi-people', 'primary'],
            ['Menunggu Dipanggil', $stats['pending'], 'bi-megaphone', 'warning'],
            ['Total Data Pasien', $stats['patients'], 'bi-person-vcard', 'info'],
        ])
        @if($u->hasRole('doctor'))
            @php($cards[] = ['Pendapatan Bulan Ini', 'Rp '.number_format($stats['income_month'], 0, ',', '.'), 'bi-cash-stack', 'success'])
        @endif
        @foreach($cards as [$label, $value, $icon, $color])
            <div class="col-sm-6 col-xl-{{ $u->hasRole('doctor') ? 3 : 4 }}">
                <div class="card stat-card p-3 h-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi {{ $icon }}"></i></div>
                        <div>
                            <div class="text-secondary small">{{ $label }}</div>
                            <div class="fs-4 fw-bold">{{ $value }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-xl-{{ $u->hasRole('doctor') ? 8 : 12 }}">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap gap-2 align-items-center">
                    <span><i class="bi bi-list-ol me-1 text-primary"></i> Antrean On-Site Hari Ini — {{ now()->translatedFormat('l, d F Y') }}</span>
                    <div class="ms-auto d-flex gap-2">
                        <a href="{{ route('queue.display') }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-tv me-1"></i> Layar Ruang Tunggu</a>
                        <a href="{{ route('admin.appointments.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Ambil Antrean</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th>No</th><th>Pasien</th><th>Keluhan / Layanan</th><th>Status</th><th class="text-end">Aksi Cepat</th></tr></thead>
                        <tbody>
                        @forelse($todayQueue as $a)
                            <tr class="{{ $a->status === 'confirmed' ? 'table-info' : '' }}">
                                <td><span class="queue-badge">{{ str_pad($a->queue_number, 2, '0', STR_PAD_LEFT) }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $a->patient->name }}</div>
                                    <small class="text-secondary font-monospace">{{ $a->patient->medical_record_no }}</small>
                                </td>
                                <td class="small">
                                    <div class="fw-semibold">{{ $a->service->name ?? 'Pemeriksaan Umum' }}</div>
                                    <span class="text-secondary">{{ \Illuminate\Support\Str::limit($a->complaint, 35) }}</span>
                                </td>
                                <td><span class="badge bg-{{ $a->status_color }}">{{ $a->status_label }}</span></td>
                                <td class="text-end text-nowrap">
                                    @if($a->status !== 'completed')
                                        <form method="POST" action="{{ route('admin.appointments.call', $a) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" title="Panggil Suara & Tampilkan di TV">
                                                <i class="bi bi-volume-up-fill"></i> Panggil
                                            </button>
                                        </form>
                                        @if($u->hasRole('doctor'))
                                            <a href="{{ route('admin.records.create', ['appointment_id' => $a->id]) }}" class="btn btn-sm btn-primary"><i class="bi bi-clipboard2-pulse"></i> Periksa</a>
                                        @endif
                                    @endif
                                    <a href="{{ route('admin.appointments.ticket', $a) }}" class="btn btn-sm btn-light" title="Cetak Kartu Antrean"><i class="bi bi-printer"></i></a>
                                    <a href="{{ route('admin.appointments.show', $a) }}" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-secondary py-4">Belum ada pasien mengambil antrean hari ini.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($u->hasRole('doctor'))
            <div class="col-xl-4">
                <div class="card mb-3">
                    <div class="card-header"><i class="bi bi-bar-chart me-1 text-primary"></i> Kunjungan 7 Hari Terakhir</div>
                    <div class="card-body"><canvas id="chart" height="190"></canvas></div>
                </div>
                @if($u->isAdmin() && $lowMedicines->isNotEmpty())
                    <div class="card border-warning">
                        <div class="card-header d-flex align-items-center">
                            <span><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Stok Obat Menipis</span>
                            <a href="{{ route('admin.medicines.index', ['filter' => 'low']) }}" class="small ms-auto">Kelola</a>
                        </div>
                        <ul class="list-group list-group-flush small">
                            @foreach($lowMedicines as $m)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $m->name }}</span>
                                    <span class="badge bg-danger">{{ $m->stock }} {{ $m->unit }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endif
@endsection

@push('scripts')
@if($u->hasRole('doctor'))
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('chart'), {
        type: 'bar',
        data: {
            labels: @json($chart->pluck('label')),
            datasets: [{ label: 'Kunjungan', data: @json($chart->pluck('total')), backgroundColor: '#0e9f8f', borderRadius: 8 }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
</script>
@endif
@endpush
