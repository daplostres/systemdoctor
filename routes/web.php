<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// ===== Halaman Publik & Layar Antrean Live =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/antrean', [HomeController::class, 'queueDisplay'])->name('queue.display');
Route::get('/antrean/status', [HomeController::class, 'queueStatus'])->name('queue.status');

// ===== Autentikasi =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1');
});
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

// ===== Panel Admin Berdasarkan Role (Admin otomatis memiliki akses ke semua grup) =====
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    // 1. Pendaftaran & Antrean On-Site (Admin, Dokter, Petugas Pendaftaran)
    Route::middleware('role:doctor,assistant')->group(function () {
        Route::get('appointments/{appointment}/ticket', [Admin\AppointmentController::class, 'ticket'])->name('appointments.ticket');
        Route::post('appointments/{appointment}/call', [Admin\AppointmentController::class, 'call'])->name('appointments.call');
        Route::patch('appointments/{appointment}/status', [Admin\AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::resource('appointments', Admin\AppointmentController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    // 2. Data Pasien (Admin, Dokter, Petugas Pendaftaran, Petugas Rekam Medis)
    Route::middleware('role:doctor,assistant,medical_record')->group(function () {
        Route::resource('patients', Admin\PatientController::class);
    });

    // 3. Rekam Medis & Surat Keterangan (Admin, Dokter, Petugas Rekam Medis)
    Route::middleware('role:doctor,medical_record')->group(function () {
        Route::resource('records', Admin\MedicalRecordController::class)->except(['index', 'show']);
        Route::resource('letters', Admin\MedicalLetterController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    });

    // Lihat daftar Rekam Medis/Resep, Cetak Kuitansi & Cetak Etiket Obat
    Route::middleware('role:doctor,medical_record,pharmacist')->group(function () {
        Route::get('records', [Admin\MedicalRecordController::class, 'index'])->name('records.index');
        Route::get('records/{record}', [Admin\MedicalRecordController::class, 'show'])->name('records.show');
        Route::get('records/{record}/receipt', [Admin\MedicalRecordController::class, 'receipt'])->name('records.receipt');
        Route::get('records/{record}/labels', [Admin\MedicalRecordController::class, 'labels'])->name('records.labels');
    });

    // 4. Stok Obat, Riwayat Mutasi & Konfirmasi Penyerahan Obat (Admin & Petugas Obat)
    Route::middleware('role:pharmacist')->group(function () {
        Route::patch('records/{record}/dispense', [Admin\MedicalRecordController::class, 'dispense'])->name('records.dispense');
        Route::get('medicines/logs', [Admin\MedicineController::class, 'logs'])->name('medicines.logs');
        Route::patch('medicines/{medicine}/stock', [Admin\MedicineController::class, 'adjustStock'])->name('medicines.stock');
        Route::resource('medicines', Admin\MedicineController::class)->except('show');
    });

    // 5. Laporan Keuangan (Admin & Dokter)
    Route::middleware('role:doctor')->group(function () {
        Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [Admin\ReportController::class, 'exportCsv'])->name('reports.export');
    });

    // 6. Khusus Super Admin (Kelola Layanan, Jadwal Praktek, Tanggal Libur, Akun Petugas)
    Route::middleware('role:admin')->group(function () {
        Route::resource('services', Admin\ServiceController::class)->except('show');
        Route::resource('schedules', Admin\ScheduleController::class)->except('show');
        Route::resource('holidays', Admin\HolidayController::class)->only(['index', 'store', 'destroy']);
        Route::resource('users', Admin\UserController::class)->only(['index', 'store', 'destroy']);
    });

    // Pengaturan & ganti password sendiri
    Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [Admin\SettingController::class, 'update'])->middleware('role:admin')->name('settings.update');
    Route::put('settings/password', [Admin\SettingController::class, 'updatePassword'])->name('settings.password');
});
