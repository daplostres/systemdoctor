@php($s = \App\Models\Setting::allCached())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $s['clinic_name'] ?? 'Praktek dr. Eko Andrianto')</title>
    <meta name="description" content="{{ $s['tagline'] ?? '' }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="public">
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2" href="{{ route('home') }}">
            <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
            {{ $s['clinic_name'] ?? 'Praktek dr. Eko Andrianto' }}
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#antrean">Pantau Antrean</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#layanan">Layanan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#jadwal">Jadwal Praktek</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#tentang">Tentang Dokter</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#kontak">Lokasi & Kontak</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('queue.display') }}" target="_blank"><i class="bi bi-tv me-1"></i>Layar Antrean</a></li>
                @auth
                    <li class="nav-item ms-lg-2"><a class="btn btn-primary rounded-pill px-4" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Panel Admin</a></li>
                @else
                    <li class="nav-item ms-lg-2"><a class="btn btn-outline-primary rounded-pill px-4" href="{{ route('login') }}"><i class="bi bi-lock me-1"></i>Login Petugas</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main>@yield('content')</main>

<footer class="footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-5">
                <h5 class="fw-bold text-white"><i class="bi bi-heart-pulse-fill"></i> {{ $s['clinic_name'] ?? '' }}</h5>
                <p class="small mb-0">{{ $s['tagline'] ?? '' }}</p>
                @if(!empty($s['doctor_sip']))<p class="small mt-2 mb-0">{{ $s['doctor_sip'] }}</p>@endif
            </div>
            <div class="col-md-4">
                <h6 class="text-white fw-semibold">Alamat Praktek</h6>
                <p class="small mb-0">{{ $s['address'] ?? '-' }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-white fw-semibold">Hubungi Kami</h6>
                <p class="small mb-1"><i class="bi bi-telephone me-1"></i> {{ $s['phone'] ?? '-' }}</p>
                <p class="small mb-0"><i class="bi bi-envelope me-1"></i> {{ $s['email'] ?? '-' }}</p>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="d-flex justify-content-between small">
            <span>&copy; {{ date('Y') }} {{ $s['clinic_name'] ?? '' }} — Ngaliyan, Kota Semarang</span>
            <a href="{{ route('login') }}" class="text-reset text-decoration-none"><i class="bi bi-lock"></i> Login Dokter / Asisten</a>
        </div>
    </div>
</footer>

@if(!empty($s['whatsapp']))
<a href="https://wa.me/{{ preg_replace('/\D/', '', $s['whatsapp']) }}" target="_blank" class="wa-float" title="Chat WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
