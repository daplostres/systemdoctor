@extends('layouts.admin')
@section('title', 'Laporan Keuangan & Kunjungan')

@section('content')
@php($s = \App\Models\Setting::allCached())

<div class="card mb-3 d-print-none">
    <div class="card-body">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Periode Bulan</label>
                <input type="month" name="month" value="{{ $month }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Metode Pembayaran</label>
                <select name="payment_method" class="form-select">
                    <option value="">Semua Metode</option>
                    @foreach(\App\Models\MedicalRecord::PAYMENT_METHODS as $k => $lbl)
                        <option value="{{ $k }}" @selected(request('payment_method') === $k)>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 d-flex flex-wrap gap-2">
                <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Tampilkan</button>
                <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-outline-success ms-auto">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV / Excel
                </a>
                <button type="button" onclick="window.print()" class="btn btn-outline-primary">
                    <i class="bi bi-printer me-1"></i> Cetak Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="d-none d-print-block mb-4 border-bottom pb-3">
    <h4 class="fw-bold mb-0">{{ $s['clinic_name'] ?? 'Praktek Dokter' }} — Laporan Keuangan Bulanan</h4>
    <div class="text-secondary">Periode: {{ $start->translatedFormat('F Y') }}</div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-primary-subtle text-primary"><i class="bi bi-people"></i></div>
                <div>
                    <div class="text-secondary small">Total Kunjungan</div>
                    <div class="fs-4 fw-bold">{{ $summary['visits'] }} Pasien</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-info-subtle text-info"><i class="bi bi-heart-pulse"></i></div>
                <div>
                    <div class="text-secondary small">Pendapatan Jasa Dokter</div>
                    <div class="fs-5 fw-bold">Rp {{ number_format($summary['service_income'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-warning-subtle text-warning"><i class="bi bi-capsule"></i></div>
                <div>
                    <div class="text-secondary small">Pendapatan Obat</div>
                    <div class="fs-5 fw-bold">Rp {{ number_format($summary['medicine_income'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-success-subtle text-success"><i class="bi bi-cash-coin"></i></div>
                <div>
                    <div class="text-secondary small">Total Pemasukan</div>
                    <div class="fs-5 fw-bold text-success">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4 d-print-none">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-bar-chart-line me-1 text-primary"></i> Grafik Pemasukan Harian — {{ $start->translatedFormat('F Y') }}</div>
            <div class="card-body"><canvas id="incomeChart" height="80"></canvas></div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-clipboard2-pulse me-1 text-primary"></i> 10 Penyakit / Diagnosis Terbanyak ({{ $start->translatedFormat('F Y') }})</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead><tr><th>#</th><th>Diagnosis Penyakit</th><th class="text-center">Jumlah Kasus</th><th style="width:32%">Proporsi</th></tr></thead>
                    <tbody>
                    @php($totalCases = max(1, $summary['visits']))
                    @forelse($topDiagnoses as $idx => $d)
                        @php($pct = round(($d->total_cases / $totalCases) * 100))
                        <tr>
                            <td class="text-secondary">{{ $idx + 1 }}</td>
                            <td class="fw-semibold">{{ $d->diagnosis }}</td>
                            <td class="text-center"><span class="badge bg-primary-subtle text-primary">{{ $d->total_cases }} pasien</span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 7px">
                                        <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-secondary">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-3">Belum ada data diagnosis bulan ini.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-capsule-pill me-1 text-primary"></i> 5 Obat Terbanyak Diresepkan ({{ $start->translatedFormat('F Y') }})</div>
            <ul class="list-group list-group-flush">
                @forelse($topMedicines as $tm)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $tm->name }}</div>
                            <small class="text-secondary">Total Penjualan: Rp {{ number_format($tm->total_sales, 0, ',', '.') }}</small>
                        </div>
                        <span class="badge bg-primary-subtle text-primary">{{ $tm->total_qty }} {{ $tm->unit }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-secondary small">Belum ada data obat bulan ini.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-1 text-primary"></i> Rincian Transaksi ({{ $start->translatedFormat('F Y') }})</span>
        <div class="small text-secondary">
            @foreach(\App\Models\MedicalRecord::PAYMENT_METHODS as $k => $lbl)
                <span class="ms-2">{{ $lbl }}: <strong>Rp {{ number_format($summary['by_payment'][$k] ?? 0, 0, ',', '.') }}</strong></span>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
            <tr>
                <th>No. Invoice</th>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Diagnosis</th>
                <th>Metode</th>
                <th class="text-end">Jasa</th>
                <th class="text-end">Obat</th>
                <th class="text-end">Total</th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $r)
                <tr>
                    <td class="font-monospace small"><a href="{{ route('admin.records.receipt', $r) }}">{{ $r->invoice_no }}</a></td>
                    <td>{{ $r->visit_date->translatedFormat('d M Y') }}</td>
                    <td>
                        <div class="fw-semibold">{{ $r->patient->name }}</div>
                        <small class="text-secondary font-monospace">{{ $r->patient->medical_record_no }}</small>
                    </td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($r->diagnosis, 35) }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $r->payment_label }}</span></td>
                    <td class="text-end">Rp {{ number_format($r->fee, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($r->medicine_fee, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($r->total_fee, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-secondary py-4">Tidak ada transaksi pada periode ini.</td></tr>
            @endforelse
            </tbody>
            @if($records->isNotEmpty())
                <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="5" class="text-end">TOTAL</td>
                    <td class="text-end">Rp {{ number_format($summary['service_income'], 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($summary['medicine_income'], 0, ',', '.') }}</td>
                    <td class="text-end text-primary">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</td>
                </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    new Chart(document.getElementById('incomeChart'), {
        type: 'bar',
        data: {
            labels: @json($dailyChart->pluck('label')),
            datasets: [{
                label: 'Pemasukan (Rp)',
                data: @json($dailyChart->pluck('income')),
                backgroundColor: '#0e9f8f',
                borderRadius: 6
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush

