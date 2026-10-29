<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        return view('admin.schedules.index', ['schedules' => Schedule::ordered()->get()]);
    }

    public function create()
    {
        return view('admin.schedules.form', ['schedule' => new Schedule(['is_active' => true, 'quota' => 20])]);
    }

    public function store(Request $request)
    {
        Schedule::create($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.form', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $schedule->update($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'day' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'quota' => 'required|integer|min:1|max:500',
            'note' => 'nullable|string|max:255',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}

