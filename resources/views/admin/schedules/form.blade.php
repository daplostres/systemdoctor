@extends('layouts.admin')
@section('title', $schedule->exists ? 'Edit Jadwal' : 'Tambah Jadwal')

@section('content')
<div class="card" style="max-width: 600px">
    <div class="card-body p-4">
        <form method="POST" action="{{ $schedule->exists ? route('admin.schedules.update', $schedule) : route('admin.schedules.store') }}">
            @csrf
            @if($schedule->exists) @method('PUT') @endif
            <div class="mb-3">
                <label class="form-label">Hari *</label>
                <select name="day" class="form-select" required>
                    @foreach(\App\Models\Schedule::DAYS as $num => $name)
                        <option value="{{ $num }}" @selected((string) old('day', $schedule->day) === (string) $num)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">Jam Mulai *</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $schedule->start_time ? substr($schedule->start_time, 0, 5) : '') }}" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Jam Selesai *</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $schedule->end_time ? substr($schedule->end_time, 0, 5) : '') }}" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Kuota Pasien per Hari *</label>
                <input type="number" min="1" name="quota" class="form-control" value="{{ old('quota', $schedule->quota) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Keterangan</label>
                <input name="note" class="form-control" value="{{ old('note', $schedule->note) }}" placeholder="Misal: Praktek pagi">
            </div>
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active" @checked(old('is_active', $schedule->is_active))>
                <label class="form-check-label" for="active">Aktif</label>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

