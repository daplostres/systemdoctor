<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $todaySchedule = Schedule::active()->where('day', now()->dayOfWeek)->first();
        $todayHoliday = Holiday::todayHoliday();

        $currentCalled = Appointment::with('patient')
            ->whereDate('appointment_date', $today)
            ->where('status', 'confirmed')
            ->orderByDesc('called_at')
            ->orderBy('queue_number')
            ->first();

        $waitingCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'pending')->count();

        $totalToday = Appointment::whereDate('appointment_date', $today)
            ->where('status', '!=', 'cancelled')->count();

        return view('public.home', [
            'services' => Service::active()->orderBy('name')->get(),
            'schedules' => Schedule::active()->ordered()->get(),
            'settings' => Setting::allCached(),
            'holidays' => Holiday::activeOrUpcoming(),
            'todayHoliday' => $todayHoliday,
            'todaySchedule' => $todaySchedule,
            'currentCalled' => $currentCalled,
            'waitingCount' => $waitingCount,
            'totalToday' => $totalToday,
        ]);
    }

    /** Layar TV / Monitor Antrean Ruang Tunggu (/antrean) */
    public function queueDisplay()
    {
        return view('public.queue-display', [
            'settings' => Setting::allCached(),
        ]);
    }

    /** Endpoint JSON Real-time untuk Layar Antrean & Suara Panggilan */
    public function queueStatus()
    {
        $today = now()->toDateString();

        $current = Appointment::with(['patient', 'service'])
            ->whereDate('appointment_date', $today)
            ->where('status', 'confirmed')
            ->orderByDesc('called_at')
            ->orderBy('queue_number')
            ->first();

        $waiting = Appointment::with(['patient', 'service'])
            ->whereDate('appointment_date', $today)
            ->where('status', 'pending')
            ->orderBy('queue_number')
            ->limit(8)
            ->get();

        $completedCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'completed')->count();

        return response()->json([
            'date_label' => now()->translatedFormat('l, d F Y · H:i:s'),
            'current' => $current ? [
                'id' => $current->id,
                'queue_number' => str_pad($current->queue_number, 2, '0', STR_PAD_LEFT),
                'raw_number' => $current->queue_number,
                'patient_name' => $current->patient->name,
                'service_name' => $current->service->name ?? 'Pemeriksaan Umum',
                'called_ts' => $current->called_at?->timestamp ?? 0,
            ] : null,
            'waiting' => $waiting->map(fn ($a) => [
                'queue_number' => str_pad($a->queue_number, 2, '0', STR_PAD_LEFT),
                'patient_name' => $a->patient->name,
                'service_name' => $a->service->name ?? 'Pemeriksaan Umum',
            ]),
            'waiting_count' => Appointment::whereDate('appointment_date', $today)->where('status', 'pending')->count(),
            'completed_count' => $completedCount,
        ]);
    }
}
