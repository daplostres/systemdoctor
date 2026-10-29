<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Layar Antrean · {{ $settings['clinic_name'] ?? 'Praktek dr. Eko Andrianto' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { background: #0f2b3d; color: #fff; min-height: 100vh; display: flex; flex-direction: column; }
        .tv-header { background: rgba(255,255,255,.05); border-bottom: 1px solid rgba(255,255,255,.1); padding: 1.25rem 2rem; }
        .tv-card { background: #fff; color: #0f2b3d; border-radius: 28px; box-shadow: 0 25px 60px rgba(0,0,0,.25); }
        .tv-number { font-size: 9rem; font-weight: 800; color: #0e9f8f; line-height: 1; letter-spacing: -2px; }
        .wait-item { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); border-radius: 16px; padding: 1rem 1.25rem; }
        .pulse-call { animation: pulseBorder 1.5s infinite; }
        @keyframes pulseBorder {
            0% { box-shadow: 0 0 0 0 rgba(14, 159, 143, 0.7); }
            70% { box-shadow: 0 0 0 22px rgba(14, 159, 143, 0); }
            100% { box-shadow: 0 0 0 0 rgba(14, 159, 143, 0); }
        }
    </style>
</head>
<body>
<header class="tv-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <span class="brand-icon" style="width:52px;height:52px;font-size:1.5rem"><i class="bi bi-heart-pulse-fill"></i></span>
        <div>
            <h3 class="fw-bold mb-0">{{ $settings['clinic_name'] ?? 'Praktek dr. Eko Andrianto' }}</h3>
            <div class="small opacity-75">{{ $settings['address'] ?? '' }}</div>
        </div>
    </div>
    <div class="d-flex align-items-center gap-3">
        <button id="btn-sound" class="btn btn-outline-light rounded-pill px-3">
            <i class="bi bi-volume-up-fill me-1"></i> Suara Panggilan Aktif
        </button>
        <div class="text-end">
            <div class="small opacity-75">Waktu Sekarang</div>
            <div class="fw-bold fs-5" id="clock">{{ now()->translatedFormat('l, d F Y · H:i:s') }}</div>
        </div>
    </div>
</header>

<div class="container-fluid px-4 py-4 flex-grow-1 d-flex flex-column">
    <div class="row g-4 flex-grow-1">
        {{-- Nomor yang sedang dipanggil --}}
        <div class="col-lg-7 d-flex">
            <div class="tv-card p-5 w-100 d-flex flex-column justify-content-center text-center" id="main-call-card">
                <div class="text-uppercase fw-bold text-secondary letter-spacing-1 mb-2">
                    <i class="bi bi-megaphone-fill text-primary me-1"></i> Nomor Antrean yang Dipanggil
                </div>
                <div class="tv-number my-3" id="current-number">--</div>
                <h2 class="fw-bold mb-1" id="current-name">Menunggu Panggilan</h2>
                <div class="text-primary fw-semibold fs-5" id="current-service">Silakan duduk di ruang tunggu</div>

                <div class="row g-3 mt-5 pt-3 border-top">
                    <div class="col-6">
                        <div class="text-secondary small">Sisa Antrean Menunggu</div>
                        <div class="fs-2 fw-bold text-dark" id="waiting-count">0</div>
                    </div>
                    <div class="col-6">
                        <div class="text-secondary small">Selesai Diperiksa Hari Ini</div>
                        <div class="fs-2 fw-bold text-success" id="completed-count">0</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar antrean berikutnya --}}
        <div class="col-lg-5 d-flex flex-column">
            <h5 class="fw-bold mb-3"><i class="bi bi-list-ol me-2 text-info"></i>Antrean Berikutnya</h5>
            <div id="waiting-list" class="d-flex flex-column gap-3">
                <div class="wait-item text-center opacity-75">Memuat daftar antrean...</div>
            </div>
        </div>
    </div>
</div>

<script>
    let lastCalledTs = 0;
    let soundEnabled = true;

    document.getElementById('btn-sound').addEventListener('click', function () {
        soundEnabled = !soundEnabled;
        this.innerHTML = soundEnabled
            ? '<i class="bi bi-volume-up-fill me-1"></i> Suara Panggilan Aktif'
            : '<i class="bi bi-volume-mute-fill me-1"></i> Suara Dimatikan';
        this.className = soundEnabled ? 'btn btn-outline-light rounded-pill px-3' : 'btn btn-danger rounded-pill px-3';
        if (soundEnabled) speak('Suara panggilan antrean diaktifkan.');
    });

    function speak(text) {
        if (!soundEnabled || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(text);
        u.lang = 'id-ID';
        u.rate = 0.92;
        window.speechSynthesis.speak(u);
    }

    async function refreshQueue() {
        try {
            const res = await fetch('{{ route('queue.status') }}');
            const data = await res.json();
            document.getElementById('clock').textContent = data.date_label;
            document.getElementById('waiting-count').textContent = data.waiting_count;
            document.getElementById('completed-count').textContent = data.completed_count;

            const card = document.getElementById('main-call-card');
            if (data.current) {
                document.getElementById('current-number').textContent = data.current.queue_number;
                document.getElementById('current-name').textContent = 'Silakan Masuk ke Ruang Periksa';
                document.getElementById('current-service').textContent = data.current.service_name;

                if (data.current.called_ts > lastCalledTs) {
                    if (lastCalledTs !== 0) {
                        card.classList.add('pulse-call');
                        speak(`Nomor antrean ${data.current.raw_number}, silakan masuk ke ruang periksa.`);
                        setTimeout(() => card.classList.remove('pulse-call'), 6000);
                    }
                    lastCalledTs = data.current.called_ts;
                }
            } else {
                document.getElementById('current-number').textContent = '--';
                document.getElementById('current-name').textContent = 'Belum Ada Panggilan';
                document.getElementById('current-service').textContent = 'Silakan menunggu panggilan petugas';
            }

            const list = document.getElementById('waiting-list');
            if (data.waiting.length === 0) {
                list.innerHTML = '<div class="wait-item text-center opacity-75">Tidak ada antrean menunggu.</div>';
            } else {
                list.innerHTML = data.waiting.map(w => `
                    <div class="wait-item d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <span class="queue-badge fs-5">${w.queue_number}</span>
                            <div>
                                <div class="fw-bold fs-5">Antrean Nomor ${w.queue_number}</div>
                                <small class="opacity-75">${w.service_name}</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark">Menunggu</span>
                    </div>
                `).join('');
            }
        } catch (e) {}
    }

    refreshQueue();
    setInterval(refreshQueue, 4000);
</script>
</body>
</html>

