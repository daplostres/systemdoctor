@extends('layouts.public')

@section('content')
{{-- ================= PENGUMUMAN LIBUR / TUTUP SEMENTARA ================= --}}
@if($holidays->isNotEmpty())
<div class="bg-warning-subtle border-bottom border-warning py-2">
    <div class="container d-flex flex-wrap align-items-center justify-content-center gap-2 small text-dark">
        <span class="badge bg-warning text-dark"><i class="bi bi-megaphone-fill me-1"></i>Pengumuman</span>
        @foreach($holidays as $h)
            <span>
                Praktek tutup sementara pada
                <strong>
                    {{ $h->start_date->translatedFormat('d M Y') }}
                    @if(!$h->start_date->isSameDay($h->end_date)) s/d {{ $h->end_date->translatedFormat('d M Y') }} @endif
                </strong>
                ({{ $h->reason }}).
            </span>
        @endforeach
    </div>
</div>
@endif

{{-- ================= HERO ================= --}}
<section class="hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="badge-soft"><i class="bi bi-geo-alt-fill me-1"></i> Tambakaji, Kec. Ngaliyan — Kota Semarang</span>
                <h1 class="display-5 mt-3 mb-3">Sehat Bersama<br><span class="text-primary">{{ $settings['doctor_name'] ?? 'dr. Eko Andrianto' }}</span></h1>
                <p class="lead text-secondary mb-4">{{ $settings['tagline'] ?? '' }}</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#antrean" class="btn btn-primary btn-lg rounded-pill px-4"><i class="bi bi-people-fill me-2"></i>Pantau Antrean & Cara Daftar</a>
                    <a href="#jadwal" class="btn btn-outline-primary btn-lg rounded-pill px-4"><i class="bi bi-clock me-2"></i>Jadwal Praktek</a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4 text-secondary small">
                    <span><i class="bi bi-check-circle-fill text-primary"></i> Ambil antrean langsung di tempat</span>
                    <span><i class="bi bi-check-circle-fill text-primary"></i> Biaya transparan & terjangkau</span>
                    <span><i class="bi bi-check-circle-fill text-primary"></i> Pantau nomor antrean dari rumah</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card text-center">
                    <div class="hero-avatar mb-3"><i class="bi bi-person-badge"></i></div>
                    <h4 class="fw-bold mb-0">{{ $settings['doctor_name'] ?? 'dr. Eko Andrianto' }}</h4>
                    <p class="text-primary fw-semibold mb-1">{{ $settings['doctor_title'] ?? 'Dokter Umum' }}</p>
                    <p class="text-secondary small mb-3">{{ $settings['doctor_sip'] ?? '' }}</p>
                    <div class="row g-2">
                        <div class="col-4"><div class="stat-pill"><strong>On-Site</strong><small class="text-secondary">Pendaftaran</small></div></div>
                        <div class="col-4"><div class="stat-pill"><strong>{{ $schedules->pluck('day')->unique()->count() }}</strong><small class="text-secondary">Hari/Minggu</small></div></div>
                        <div class="col-4"><div class="stat-pill"><strong>{{ $services->count() }}</strong><small class="text-secondary">Layanan</small></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= PANTAU ANTREAN LIVE & CARA AMBIL ANTREAN ON-SITE ================= --}}
<section class="section bg-soft" id="antrean">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                <div class="card booking-card p-4 p-md-5 h-100 text-center d-flex flex-column justify-content-center ticket">
                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                        @if($todayHoliday)
                            <span class="badge bg-danger rounded-pill px-3 py-2">Hari Ini Praktek Libur ({{ $todayHoliday->reason }})</span>
                        @elseif($todaySchedule)
                            <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size:.55rem"></i> Jadwal Hari Ini: {{ $todaySchedule->time_range }} WIB</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-3 py-2">Hari Ini Tidak Ada Jadwal Praktek</span>
                        @endif
                    </div>

                    <div class="text-secondary small text-uppercase fw-semibold mt-2">Antrean Sedang Diperiksa</div>
                    <div class="queue-number my-2" id="live-current-number">
                        {{ $currentCalled ? str_pad($currentCalled->queue_number, 2, '0', STR_PAD_LEFT) : '--' }}
                    </div>
                    <div class="fw-semibold text-dark mb-3" id="live-current-label">
                        {{ $currentCalled ? 'Sedang di ruang periksa' : 'Belum ada panggilan aktif' }}
                    </div>

                    <div class="row g-2 mt-auto">
                        <div class="col-6">
                            <div class="stat-pill">
                                <strong id="live-waiting-count">{{ $waitingCount }}</strong>
                                <small class="text-secondary">Menunggu Dipanggil</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-pill">
                                <strong id="live-total-today">{{ $totalToday }}</strong>
                                <small class="text-secondary">Total Terdaftar Hari Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('queue.display') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-tv me-1"></i> Buka Layar Penuh Ruang Tunggu
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card booking-card p-4 p-md-5 h-100">
                    <span class="badge bg-primary-subtle text-primary align-self-start rounded-pill px-3 py-2 mb-3">
                        <i class="bi bi-Signpost-split me-1"></i> Melayani Pendaftaran Langsung di Tempat (On-Site)
                    </span>
                    <h3 class="section-title mb-3">Alur Pendaftaran & Ambil Antrean</h3>
                    <p class="text-secondary mb-4">
                        Praktek <strong>{{ $settings['doctor_name'] ?? 'dr. Eko Andrianto' }}</strong> melayani pemeriksaan secara tatap muka (*offline*) dengan pengambilan nomor antrean langsung di lokasi praktek.
                    </p>

                    <div class="d-flex gap-3 mb-3">
                        <div class="queue-badge flex-shrink-0">1</div>
                        <div>
                            <div class="fw-bold">Datang ke Lokasi Praktek Sesuai Jadwal</div>
                            <div class="text-secondary small">{{ $settings['address'] ?? '' }}. Pendaftaran dibuka mulai 15 menit sebelum jam praktek.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="queue-badge flex-shrink-0">2</div>
                        <div>
                            <div class="fw-bold">Ambil Nomor Antrean di Meja Pendaftaran</div>
                            <div class="text-secondary small">Petugas akan mencatat data singkat Anda (atau menunjukkan kartu/No. RM bagi pasien lama) dan memberikan kartu nomor antrean cetak.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-3">
                        <div class="queue-badge flex-shrink-0">3</div>
                        <div>
                            <div class="fw-bold">Tunggu Panggilan Pemeriksaan</div>
                            <div class="text-secondary small">Nomor antrean dipanggil secara berurutan melalui layar monitor ruang tunggu & panggilan suara.</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="queue-badge flex-shrink-0">4</div>
                        <div>
                            <div class="fw-bold">Pemeriksaan, Tebus Obat & Kuitansi</div>
                            <div class="text-secondary small">Setelah diperiksa dokter, pasien menerima obat, kuitansi resmi, serta Surat Keterangan Sakit/Sehat bila diperlukan.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= LAYANAN ================= --}}
<section class="section" id="layanan">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Layanan Kesehatan</h2>
            <p class="section-sub">Berbagai layanan kesehatan dasar untuk Anda dan keluarga dengan biaya yang transparan.</p>
        </div>
        <div class="row g-4">
            @forelse($services as $service)
                <div class="col-md-6 col-lg-4">
                    <div class="service-card">
                        <div class="service-icon"><i class="bi {{ $service->icon }}"></i></div>
                        <h5 class="fw-bold">{{ $service->name }}</h5>
                        <p class="text-secondary small">{{ $service->description }}</p>
                        <div class="fw-bold text-primary">Mulai {{ $service->price_formatted }}</div>
                    </div>
                </div>
            @empty
                <p class="text-center text-secondary">Belum ada layanan.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ================= JADWAL ================= --}}
<section class="section bg-soft" id="jadwal">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <h2 class="section-title">Jadwal Praktek</h2>
                <p class="text-secondary">Silakan datang langsung ke lokasi praktek sesuai jadwal di samping untuk mengambil nomor antrean.</p>
                <div class="d-flex align-items-start gap-3 mt-4">
                    <div class="service-icon mb-0 flex-shrink-0"><i class="bi bi-geo-alt"></i></div>
                    <div>
                        <div class="fw-bold">Alamat Praktek</div>
                        <div class="text-secondary small">{{ $settings['address'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card booking-card overflow-hidden">
                    <table class="table schedule-table mb-0 align-middle">
                        <thead><tr><th>Hari</th><th>Jam Praktek</th><th>Kuota Harian</th><th>Keterangan</th></tr></thead>
                        <tbody>
                        @forelse($schedules as $sch)
                            <tr class="{{ $sch->day === now()->dayOfWeek ? 'today' : '' }}">
                                <td class="fw-semibold">{{ $sch->day_name }}
                                    @if($sch->day === now()->dayOfWeek)<span class="badge bg-primary ms-1">Hari ini</span>@endif
                                </td>
                                <td><i class="bi bi-clock text-primary me-1"></i> {{ $sch->time_range }} WIB</td>
                                <td>{{ $sch->quota }} pasien</td>
                                <td class="text-secondary small">{{ $sch->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary">Jadwal belum tersedia.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= TENTANG ================= --}}
<section class="section" id="tentang">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 text-center">
                <div class="hero-avatar" style="width:220px;height:220px;font-size:6rem"><i class="bi bi-person-heart"></i></div>
            </div>
            <div class="col-lg-7">
                <h2 class="section-title">Tentang {{ $settings['doctor_name'] ?? 'dr. Eko Andrianto' }}</h2>
                <p class="text-primary fw-semibold">{{ $settings['doctor_title'] ?? '' }} · {{ $settings['doctor_sip'] ?? '' }}</p>
                <p class="text-secondary">{!! nl2br(e($settings['doctor_bio'] ?? '')) !!}</p>
            </div>
        </div>
    </div>
</section>

{{-- ================= KONTAK ================= --}}
<section class="section bg-soft" id="kontak">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-5">
                <h2 class="section-title mb-4">Lokasi & Kontak</h2>
                <div class="d-flex gap-3 mb-3"><div class="service-icon mb-0 flex-shrink-0"><i class="bi bi-geo-alt"></i></div><div><div class="fw-bold">Alamat</div><div class="text-secondary small">{{ $settings['address'] ?? '-' }}</div></div></div>
                <div class="d-flex gap-3 mb-3"><div class="service-icon mb-0 flex-shrink-0"><i class="bi bi-telephone"></i></div><div><div class="fw-bold">Telepon</div><div class="text-secondary small">{{ $settings['phone'] ?? '-' }}</div></div></div>
                <div class="d-flex gap-3 mb-3"><div class="service-icon mb-0 flex-shrink-0"><i class="bi bi-whatsapp"></i></div><div><div class="fw-bold">WhatsApp Informasi</div><div class="text-secondary small">{{ $settings['whatsapp'] ?? '-' }}</div></div></div>
                <div class="d-flex gap-3"><div class="service-icon mb-0 flex-shrink-0"><i class="bi bi-envelope"></i></div><div><div class="fw-bold">Email</div><div class="text-secondary small">{{ $settings['email'] ?? '-' }}</div></div></div>
            </div>
            <div class="col-lg-7">
                @if(!empty($settings['maps_embed']))
                    <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm">
                        <iframe src="{{ $settings['maps_embed'] }}" loading="lazy" style="border:0"></iframe>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Perbarui nomor antrean di halaman utama setiap 8 detik
    setInterval(async () => {
        try {
            const res = await fetch('{{ route('queue.status') }}');
            const data = await res.json();
            document.getElementById('live-current-number').textContent = data.current ? data.current.queue_number : '--';
            document.getElementById('live-current-label').textContent = data.current ? 'Silakan masuk ke ruang periksa' : 'Belum ada panggilan aktif';
            document.getElementById('live-waiting-count').textContent = data.waiting_count;
        } catch (e) {}
    }, 8000);
</script>
@endpush
