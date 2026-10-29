@extends('layouts.admin')
@section('title', $record->exists ? 'Edit Rekam Medis' : 'Pemeriksaan & Rekam Medis Baru')

@section('content')
<form method="POST" action="{{ $record->exists ? route('admin.records.update', $record) : route('admin.records.store') }}">
    @csrf
    @if($record->exists) @method('PUT') @endif
    <input type="hidden" name="appointment_id" value="{{ old('appointment_id', $record->appointment_id) }}">

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-person me-1 text-primary"></i> Pasien & Kunjungan</div>
                <div class="card-body row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Pasien *</label>
                        <select name="patient_id" class="form-select" required @if($appointment) style="pointer-events:none;background:#f3f5f7" @endif>
                            <option value="">- Pilih pasien -</option>
                            @foreach($patients as $p)
                                <option value="{{ $p->id }}" @selected(old('patient_id', $record->patient_id) == $p->id)>{{ $p->name }} ({{ $p->medical_record_no }})</option>
                            @endforeach
                        </select>
                        @if($appointment)<div class="form-text">Dari janji temu <span class="font-monospace">{{ $appointment->code }}</span> · antrean {{ $appointment->queue_number }}</div>@endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal Periksa *</label>
                        <input type="date" name="visit_date" class="form-control" value="{{ old('visit_date', optional($record->visit_date)->toDateString() ?? $record->visit_date) }}" required>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-activity me-1 text-primary"></i> Pemeriksaan Klinis</div>
                <div class="card-body row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tekanan Darah</label>
                        <div class="input-group"><input name="blood_pressure" class="form-control" placeholder="120/80" value="{{ old('blood_pressure', $record->blood_pressure) }}"><span class="input-group-text">mmHg</span></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Berat Badan</label>
                        <div class="input-group"><input type="number" step="0.1" name="weight" class="form-control" value="{{ old('weight', $record->weight) }}"><span class="input-group-text">kg</span></div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Suhu Tubuh</label>
                        <div class="input-group"><input type="number" step="0.1" name="temperature" class="form-control" value="{{ old('temperature', $record->temperature) }}"><span class="input-group-text">°C</span></div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Keluhan (Subjektif) *</label>
                        <textarea name="complaint" rows="2" class="form-control" required>{{ old('complaint', $record->complaint) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Diagnosis *</label>
                        <textarea name="diagnosis" rows="2" class="form-control" required>{{ old('diagnosis', $record->diagnosis) }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Tindakan Medis</label>
                        <textarea name="treatment" rows="2" class="form-control">{{ old('treatment', $record->treatment) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- RESEP OBAT DARI STOK --}}
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center">
                    <span><i class="bi bi-capsule me-1 text-primary"></i> Resep & Obat dari Stok</span>
                    <button type="button" class="btn btn-sm btn-outline-primary ms-auto" id="add-med-row">
                        <i class="bi bi-plus-lg"></i> Tambah Obat
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-2" id="med-table">
                            <thead>
                            <tr>
                                <th style="width:42%">Nama Obat (Stok)</th>
                                <th style="width:15%">Jumlah</th>
                                <th style="width:28%">Aturan Pakai</th>
                                <th style="width:15%" class="text-end">Subtotal</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="med-rows">
                            @php($existingItems = old('items', $record->medicineItems->map(fn($i) => ['medicine_id' => $i->medicine_id, 'qty' => $i->qty, 'dosage' => $i->dosage])->toArray()))
                            @forelse($existingItems as $idx => $item)
                                <tr class="med-row">
                                    <td>
                                        <select name="items[{{ $idx }}][medicine_id]" class="form-select form-select-sm med-select">
                                            <option value="" data-price="0">- Pilih Obat -</option>
                                            @foreach($medicines as $m)
                                                <option value="{{ $m->id }}" data-price="{{ $m->price }}" @selected(($item['medicine_id'] ?? '') == $m->id)>
                                                    {{ $m->name }} (Stok: {{ $m->stock }} {{ $m->unit }} · Rp {{ number_format($m->price, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[{{ $idx }}][qty]" min="1" value="{{ $item['qty'] ?? 1 }}" class="form-control form-control-sm med-qty"></td>
                                    <td><input type="text" name="items[{{ $idx }}][dosage]" value="{{ $item['dosage'] ?? '' }}" placeholder="3x1 sesudah makan" class="form-control form-control-sm"></td>
                                    <td class="text-end fw-semibold small med-subtotal">Rp 0</td>
                                    <td><button type="button" class="btn btn-sm btn-light text-danger remove-row"><i class="bi bi-x-lg"></i></button></td>
                                </tr>
                            @empty
                                <tr class="med-row">
                                    <td>
                                        <select name="items[0][medicine_id]" class="form-select form-select-sm med-select">
                                            <option value="" data-price="0">- Pilih Obat -</option>
                                            @foreach($medicines as $m)
                                                <option value="{{ $m->id }}" data-price="{{ $m->price }}">
                                                    {{ $m->name }} (Stok: {{ $m->stock }} {{ $m->unit }} · Rp {{ number_format($m->price, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><input type="number" name="items[0][qty]" min="1" value="1" class="form-control form-control-sm med-qty"></td>
                                    <td><input type="text" name="items[0][dosage]" placeholder="3x1 sesudah makan" class="form-control form-control-sm"></td>
                                    <td class="text-end fw-semibold small med-subtotal">Rp 0</td>
                                    <td><button type="button" class="btn btn-sm btn-light text-danger remove-row"><i class="bi bi-x-lg"></i></button></td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary">
                                <i class="bi bi-chat-left-text me-1"></i> Keterangan / Instruksi Obat untuk Pengelola Obat (Apotek)
                            </label>
                            <textarea name="pharmacy_note" rows="2" class="form-control border-primary-subtle" placeholder="Contoh: Racik puyer 10 bungkus / Edukasi pasien habiskan antibiotik / Jika stok merk A habis ganti generik">{{ old('pharmacy_note', $record->pharmacy_note) }}</textarea>
                            <div class="form-text">Pesan ini akan tampil mencolok di layar Petugas Obat / Apotek saat menyiapkan obat pasien.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Resep Tambahan (Opsional / Resep Luar)</label>
                            <textarea name="prescription" rows="2" class="form-control font-monospace" placeholder="Diisi otomatis dari daftar obat di atas jika dikosongkan">{{ old('prescription', $record->prescription) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan / Saran Dokter untuk Pasien</label>
                            <textarea name="notes" rows="2" class="form-control">{{ old('notes', $record->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="bi bi-receipt me-1 text-primary"></i> Rincian Pembayaran</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Biaya Jasa Dokter / Tindakan *</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="fee" id="service-fee" min="0" class="form-control" value="{{ old('fee', $record->fee ?? 0) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Obat (Otomatis)</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="med-fee-display" class="form-control bg-light" value="0" readonly>
                        </div>
                    </div>
                    <div class="p-3 rounded-3 bg-primary-subtle mb-3 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Total Tagihan</span>
                        <span class="fs-4 fw-bold text-primary" id="grand-total">Rp 0</span>
                    </div>
                    <div>
                        <label class="form-label">Metode Pembayaran *</label>
                        <select name="payment_method" class="form-select">
                            @foreach(\App\Models\MedicalRecord::PAYMENT_METHODS as $key => $label)
                                <option value="{{ $key }}" @selected(old('payment_method', $record->payment_method ?? 'cash') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            @if($appointment?->patient?->allergies)
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> <strong>Alergi:</strong> {{ $appointment->patient->allergies }}</div>
            @endif

            <div class="d-grid gap-2">
                <button class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Simpan & Buat Kuitansi</button>
                <a href="{{ url()->previous() }}" class="btn btn-light">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const tbody = document.getElementById('med-rows');
    const serviceInput = document.getElementById('service-fee');
    const medFeeDisplay = document.getElementById('med-fee-display');
    const grandTotalEl = document.getElementById('grand-total');
    let rowIdx = tbody.querySelectorAll('.med-row').length;

    function formatRp(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function recalc() {
        let medTotal = 0;
        tbody.querySelectorAll('.med-row').forEach(tr => {
            const sel = tr.querySelector('.med-select');
            const price = Number(sel.selectedOptions[0]?.dataset.price || 0);
            const qty = Math.max(1, Number(tr.querySelector('.med-qty').value || 1));
            const sub = price * qty;
            medTotal += sub;
            tr.querySelector('.med-subtotal').textContent = formatRp(sub);
        });
        const serviceFee = Number(serviceInput.value || 0);
        medFeeDisplay.value = medTotal.toLocaleString('id-ID');
        grandTotalEl.textContent = formatRp(serviceFee + medTotal);
    }

    document.getElementById('add-med-row').addEventListener('click', () => {
        const first = tbody.querySelector('.med-row');
        const clone = first.cloneNode(true);
        clone.querySelectorAll('select, input').forEach(el => {
            el.name = el.name.replace(/\[\d+\]/, `[${rowIdx}]`);
            if (el.tagName === 'SELECT') el.selectedIndex = 0;
            else if (el.classList.contains('med-qty')) el.value = 1;
            else el.value = '';
        });
        rowIdx++;
        tbody.appendChild(clone);
        recalc();
    });

    tbody.addEventListener('change', recalc);
    tbody.addEventListener('input', recalc);
    serviceInput.addEventListener('input', recalc);

    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;
        const rows = tbody.querySelectorAll('.med-row');
        if (rows.length > 1) {
            btn.closest('.med-row').remove();
        } else {
            const tr = rows[0];
            tr.querySelector('.med-select').selectedIndex = 0;
            tr.querySelector('.med-qty').value = 1;
            tr.querySelector('input[type="text"]').value = '';
        }
        recalc();
    });

    recalc();
</script>
@endpush
