@php($s = \App\Models\Setting::allCached())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ $s['clinic_name'] ?? '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="admin">
@php($u = auth()->user())
@php($pendingQueue = \App\Models\Appointment::whereDate('appointment_date', now()->toDateString())->where('status', 'pending')->count())
@php($pendingPharmacy = \App\Models\MedicalRecord::whereDate('visit_date', now()->toDateString())->where('pharmacy_status', 'pending')->count())
@php($lowStock = \App\Models\Medicine::whereColumn('stock', '<=', 'min_stock')->count())
<aside class="sidebar d-print-none" id="sidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
        <div class="lh-sm">
            <div class="fw-bold">{{ $s['clinic_name'] ?? 'Panel Praktek' }}</div>
            <small class="opacity-75">{{ $u->role_label }}</small>
        </div>
    </div>
    <nav class="nav flex-column mt-3">
        @php($menu = [
            // [route, activePattern, icon, label, visible]
            ['admin.dashboard', 'admin.dashboard', 'bi-grid-1x2', 'Dashboard', true],
            // Antrean: Admin, Dokter (untuk panggil/periksa), Pendaftaran
            ['admin.appointments.index', 'admin.appointments.*', 'bi-person-lines-fill', 'Antrean Pasien', $u->hasRole('doctor', 'assistant')],
            // Pasien: Admin, Dokter, Pendaftaran, Rekam Medis
            ['admin.patients.index', 'admin.patients.*', 'bi-people', 'Data Pasien', $u->hasRole('doctor', 'assistant', 'medical_record')],
            // Rekam Medis / Resep: Admin, Dokter, Rekam Medis, Apotek
            ['admin.records.index', 'admin.records.*', 'bi-journal-medical', $u->role === 'pharmacist' ? 'Resep & Ket. Dokter' : 'Rekam Medis', $u->hasRole('doctor', 'medical_record', 'pharmacist')],
            // Surat Sakit/Sehat: Admin, Dokter, Rekam Medis
            ['admin.letters.index', 'admin.letters.*', 'bi-file-earmark-medical', 'Surat Sakit / Sehat', $u->hasRole('doctor', 'medical_record')],
            // Stok Obat: Admin & Petugas Obat (Dokter tidak dibebani urus stok obat)
            ['admin.medicines.index', 'admin.medicines.*', 'bi-capsule', 'Stok Obat', $u->hasRole('pharmacist')],
            // Laporan Keuangan: Admin & Dokter
            ['admin.reports.index', 'admin.reports.*', 'bi-graph-up-arrow', 'Laporan Keuangan', $u->hasRole('doctor')],
            // Pengaturan teknis khusus Super Admin (All Access)
            ['admin.services.index', 'admin.services.*', 'bi-clipboard2-pulse', 'Layanan & Tarif', $u->isAdmin()],
            ['admin.schedules.index', 'admin.schedules.*', 'bi-clock', 'Jadwal Praktek', $u->isAdmin()],
            ['admin.holidays.index', 'admin.holidays.*', 'bi-calendar-x', 'Tanggal Libur', $u->isAdmin()],
            ['admin.users.index', 'admin.users.*', 'bi-person-gear', 'Akun Petugas', $u->isAdmin()],
            ['admin.settings.edit', 'admin.settings.*', 'bi-gear', $u->isAdmin() ? 'Pengaturan Praktek' : 'Ganti Password', true],
        ])
        @foreach($menu as [$route, $pattern, $icon, $label, $visible])
            @if($visible)
                <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i> {{ $label }}
                    @if($route === 'admin.appointments.index' && $pendingQueue)
                        <span class="badge bg-warning text-dark ms-auto" title="Menunggu dipanggil hari ini">{{ $pendingQueue }}</span>
                    @elseif($route === 'admin.records.index' && $u->role === 'pharmacist' && $pendingPharmacy)
                        <span class="badge bg-warning text-dark ms-auto" title="Resep menunggu disiapkan">{{ $pendingPharmacy }}</span>
                    @elseif($route === 'admin.medicines.index' && $lowStock)
                        <span class="badge bg-danger ms-auto" title="Stok menipis">{{ $lowStock }}</span>
                    @endif
                </a>
            @endif
        @endforeach
        <hr class="border-light opacity-25 mx-3">
        @if($u->hasRole('doctor', 'assistant'))
            <a href="{{ route('queue.display') }}" target="_blank" class="nav-link"><i class="bi bi-tv"></i> Layar Antrean TV</a>
        @endif
        <a href="{{ route('home') }}" target="_blank" class="nav-link"><i class="bi bi-box-arrow-up-right"></i> Lihat Website</a>
    </nav>
</aside>

<div class="main">
    <header class="topbar d-print-none">
        <button class="btn btn-light d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="h5 fw-bold mb-0">@yield('title', 'Dashboard')</h1>
        <div class="ms-auto d-flex align-items-center gap-2">
            @if($u->role === 'assistant' || $u->isAdmin())
                <a href="{{ route('admin.appointments.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="bi bi-plus-circle me-1"></i> Ambil Antrean Baru
                </a>
            @elseif($u->role === 'doctor' || $u->role === 'medical_record')
                <a href="{{ route('admin.records.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="bi bi-clipboard2-pulse me-1"></i> Periksa / Input RM
                </a>
            @elseif($u->role === 'pharmacist')
                <a href="{{ route('admin.medicines.create') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Obat Baru
                </a>
            @endif
            <div class="dropdown">
                <button class="btn btn-light rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> {{ $u->name }}
                    <span class="badge bg-{{ $u->role_badge }} ms-1">{{ $u->role_label }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><a class="dropdown-item" href="{{ route('admin.settings.edit') }}"><i class="bi bi-key me-2"></i>{{ $u->isAdmin() ? 'Pengaturan & Password' : 'Ganti Password' }}</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-print-none">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger d-print-none">
                <i class="bi bi-exclamation-triangle me-1"></i> Periksa kembali isian Anda:
                <ul class="mb-0 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
        if (!confirm(f.dataset.confirm)) e.preventDefault();
    }));

    @if(session('called_patient'))
        (function () {
            if (!('speechSynthesis' in window)) return;
            const p = @json(session('called_patient'));
            const u = new SpeechSynthesisUtterance(`Nomor antrean ${p.number}, silakan masuk ke ruang periksa.`);
            u.lang = 'id-ID';
            u.rate = 0.92;
            window.speechSynthesis.speak(u);
        })();
    @endif
</script>
@stack('scripts')
</body>
</html>
