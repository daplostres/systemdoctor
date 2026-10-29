<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalLetter;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $today = now()->toDateString();

        $incomeMonth = MedicalRecord::whereYear('visit_date', now()->year)
            ->whereMonth('visit_date', now()->month)
            ->selectRaw('COALESCE(SUM(fee + medicine_fee), 0) as total')
            ->value('total');

        $stats = [
            'today' => Appointment::whereDate('appointment_date', $today)->where('status', '!=', 'cancelled')->count(),
            'pending' => Appointment::whereDate('appointment_date', $today)->where('status', 'pending')->count(),
            'patients' => Patient::count(),
            'income_month' => (int) $incomeMonth,
            'records_today' => MedicalRecord::whereDate('visit_date', $today)->count(),
            'letters_month' => MedicalLetter::whereYear('issue_date', now()->year)->whereMonth('issue_date', now()->month)->count(),
            'medicines_total' => Medicine::count(),
            'medicines_low' => Medicine::whereColumn('stock', '<=', 'min_stock')->count(),
        ];

        $todayQueue = Appointment::with(['patient', 'service'])
            ->whereDate('appointment_date', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('queue_number')->get();

        // Resep terbaru hari ini (khusus tampilan Petugas Obat / Rekam Medis)
        $todayPrescriptions = MedicalRecord::with(['patient', 'medicineItems.medicine'])
            ->whereDate('visit_date', $today)
            ->latest('id')
            ->limit(10)->get();

        $lowMedicines = Medicine::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')->limit(8)->get();

        // Grafik kunjungan 7 hari terakhir
        $chart = collect(range(6, 0))->map(function ($i) {
            $d = now()->subDays($i);

            return [
                'label' => $d->translatedFormat('D d/m'),
                'total' => MedicalRecord::whereDate('visit_date', $d->toDateString())->count(),
            ];
        });

        return view('admin.dashboard', compact('stats', 'todayQueue', 'todayPrescriptions', 'lowMedicines', 'chart'));
    }
}
