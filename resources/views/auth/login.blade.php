@php($s = \App\Models\Setting::allCached())
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin · {{ $s['clinic_name'] ?? '' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="hero d-flex align-items-center min-vh-100 py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="text-center mb-4">
                <span class="brand-icon" style="width:60px;height:60px;font-size:1.8rem"><i class="bi bi-heart-pulse-fill"></i></span>
                <h4 class="fw-bold mt-3 mb-0">{{ $s['clinic_name'] ?? 'Praktek Dokter' }}</h4>
                <p class="text-secondary">Masuk ke panel admin</p>
            </div>
            <div class="card booking-card p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <button class="btn btn-primary btn-lg w-100 rounded-pill">Masuk</button>
                </form>
            </div>
            <p class="text-center mt-3"><a href="{{ route('home') }}" class="text-secondary small"><i class="bi bi-arrow-left"></i> Kembali ke website</a></p>
        </div>
    </div>
</div>
</body>
</html>

