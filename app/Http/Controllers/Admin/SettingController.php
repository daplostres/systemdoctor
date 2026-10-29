<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public const KEYS = [
        'clinic_name', 'doctor_name', 'doctor_title', 'doctor_sip', 'doctor_bio',
        'tagline', 'address', 'phone', 'whatsapp', 'email', 'maps_embed',
    ];

    public function edit()
    {
        return view('admin.settings', ['settings' => Setting::allCached()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'clinic_name' => 'required|string|max:100',
            'doctor_name' => 'required|string|max:100',
            'doctor_title' => 'nullable|string|max:100',
            'doctor_sip' => 'nullable|string|max:100',
            'doctor_bio' => 'nullable|string|max:2000',
            'tagline' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'maps_embed' => 'nullable|url|max:2000',
        ]);

        foreach (self::KEYS as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        return back()->with('success', 'Pengaturan disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}

