<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\MedicineStockLog;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medicines = Medicine::query()
            ->when($request->q, function ($q, $s) {
                $q->where(fn ($w) => $w->where('name', 'like', "%$s%")
                    ->orWhere('code', 'like', "%$s%")
                    ->orWhere('category', 'like', "%$s%"));
            })
            ->when($request->filter === 'low', fn ($q) => $q->whereColumn('stock', '<=', 'min_stock'))
            ->when($request->filter === 'expiring', fn ($q) => $q->whereNotNull('expired_date')->whereDate('expired_date', '<=', now()->addDays(60)))
            ->orderBy('name')
            ->paginate(15)->withQueryString();

        $lowStockCount = Medicine::whereColumn('stock', '<=', 'min_stock')->count();
        $expiringCount = Medicine::whereNotNull('expired_date')->whereDate('expired_date', '<=', now()->addDays(60))->count();

        return view('admin.medicines.index', compact('medicines', 'lowStockCount', 'expiringCount'));
    }

    public function logs(Request $request)
    {
        $logs = MedicineStockLog::with(['medicine', 'user'])
            ->when($request->medicine_id, fn ($q, $id) => $q->where('medicine_id', $id))
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20)->withQueryString();

        $medicines = Medicine::orderBy('name')->get(['id', 'name', 'code']);

        return view('admin.medicines.logs', compact('logs', 'medicines'));
    }

    public function create()
    {
        return view('admin.medicines.form', [
            'medicine' => new Medicine(['unit' => 'Tablet', 'stock' => 0, 'min_stock' => 10, 'price' => 0]),
        ]);
    }

    public function store(Request $request)
    {
        $med = Medicine::create($this->validated($request));

        if ($med->stock > 0) {
            MedicineStockLog::create([
                'medicine_id' => $med->id,
                'user_id' => $request->user()->id,
                'type' => 'in',
                'qty' => $med->stock,
                'stock_after' => $med->stock,
                'description' => 'Stok awal obat baru',
            ]);
        }

        return redirect()->route('admin.medicines.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Medicine $medicine)
    {
        return view('admin.medicines.form', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $oldStock = $medicine->stock;
        $medicine->update($this->validated($request));

        if ($medicine->stock !== $oldStock) {
            $diff = $medicine->stock - $oldStock;
            MedicineStockLog::create([
                'medicine_id' => $medicine->id,
                'user_id' => $request->user()->id,
                'type' => $diff > 0 ? 'in' : 'out',
                'qty' => abs($diff),
                'stock_after' => $medicine->stock,
                'description' => 'Penyesuaian stok via edit obat',
            ]);
        }

        return redirect()->route('admin.medicines.index')->with('success', 'Data obat diperbarui.');
    }

    public function adjustStock(Request $request, Medicine $medicine)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|integer|min:1|max:100000',
            'description' => 'nullable|string|max:200',
        ]);

        if ($data['type'] === 'out' && $medicine->stock < $data['amount']) {
            return back()->withErrors(['amount' => 'Stok obat '.$medicine->name.' tidak mencukupi.']);
        }

        $delta = $data['type'] === 'in' ? $data['amount'] : -$data['amount'];
        $medicine->increment('stock', $delta);
        $medicine->refresh();

        MedicineStockLog::create([
            'medicine_id' => $medicine->id,
            'user_id' => $request->user()->id,
            'type' => $data['type'],
            'qty' => $data['amount'],
            'stock_after' => $medicine->stock,
            'description' => $data['description'] ?: ($data['type'] === 'in' ? 'Penambahan stok masuk' : 'Pengurangan stok manual'),
        ]);

        return back()->with('success', 'Stok '.$medicine->name.' berhasil diperbarui menjadi '.$medicine->stock.' '.$medicine->unit.'.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->recordItems()->exists()) {
            return back()->withErrors(['delete' => 'Obat sudah pernah digunakan di rekam medis dan tidak dapat dihapus.']);
        }
        $medicine->delete();

        return redirect()->route('admin.medicines.index')->with('success', 'Obat dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'category' => 'nullable|string|max:50',
            'unit' => 'required|string|max:30',
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'expired_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);
    }
}
