<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $baseQuery = MedicalRecord::with(['patient', 'appointment.service'])
            ->whereBetween('visit_date', [$start->toDateString(), $end->toDateString()]);

        if ($request->filled('payment_method')) {
            $baseQuery->where('payment_method', $request->payment_method);
        }

        $records = (clone $baseQuery)->orderBy('visit_date')->orderBy('id')->get();

        $summary = [
            'visits' => $records->count(),
            'service_income' => $records->sum('fee'),
            'medicine_income' => $records->sum('medicine_fee'),
            'total_income' => $records->sum(fn ($r) => $r->total_fee),
            'by_payment' => $records->groupBy('payment_method')->map(fn ($g) => $g->sum(fn ($r) => $r->total_fee)),
        ];

        // Grafik harian sepanjang bulan terpilih
        $byDate = $records->groupBy(fn ($r) => $r->visit_date->format('Y-m-d'));
        $dailyChart = collect(range(1, $end->day))->map(function ($day) use ($start, $byDate) {
            $dateKey = $start->copy()->day($day)->format('Y-m-d');
            $dayRecords = $byDate->get($dateKey, collect());

            return [
                'label' => str_pad($day, 2, '0', STR_PAD_LEFT),
                'visits' => $dayRecords->count(),
                'income' => $dayRecords->sum(fn ($r) => $r->total_fee),
            ];
        });

        // 5 obat paling sering diresepkan bulan ini
        $topMedicines = DB::table('medical_record_medicines as mrm')
            ->join('medical_records as mr', 'mr.id', '=', 'mrm.medical_record_id')
            ->join('medicines as m', 'm.id', '=', 'mrm.medicine_id')
            ->whereBetween('mr.visit_date', [$start->toDateString(), $end->toDateString()])
            ->select('m.name', 'm.unit', DB::raw('SUM(mrm.qty) as total_qty'), DB::raw('SUM(mrm.qty * mrm.price) as total_sales'))
            ->groupBy('m.id', 'm.name', 'm.unit')
            ->orderByDesc('total_qty')
            ->limit(5)->get();

        return view('admin.reports.index', compact(
            'month', 'start', 'end', 'records', 'summary', 'dailyChart', 'topMedicines'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = MedicalRecord::with('patient')
            ->whereBetween('visit_date', [$start->toDateString(), $end->toDateString()])
            ->when($request->payment_method, fn ($q, $m) => $q->where('payment_method', $m))
            ->orderBy('visit_date')->orderBy('id')->get();

        $filename = 'laporan-keuangan-'.$month.'.csv';

        return response()->streamDownload(function () use ($records) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM agar langsung rapi saat dibuka di Microsoft Excel
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['No. Invoice', 'Tanggal', 'No. RM', 'Nama Pasien', 'Diagnosis', 'Metode Bayar', 'Biaya Jasa (Rp)', 'Biaya Obat (Rp)', 'Total (Rp)']);

            foreach ($records as $r) {
                fputcsv($out, [
                    $r->invoice_no,
                    $r->visit_date->format('Y-m-d'),
                    $r->patient->medical_record_no,
                    $r->patient->name,
                    $r->diagnosis,
                    $r->payment_label,
                    $r->fee,
                    $r->medicine_fee,
                    $r->total_fee,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

