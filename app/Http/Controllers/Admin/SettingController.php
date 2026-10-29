<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    /** Unduh Backup Database MySQL (.sql) 1-Klik */
    public function backupDatabase(): StreamedResponse
    {
        $filename = 'backup-praktek-dokter-'.now()->format('Y-m-d_His').'.sql';

        return response()->streamDownload(function () {
            $pdo = DB::connection()->getPdo();
            $out = fopen('php://output', 'w');

            fwrite($out, "-- =========================================================\n");
            fwrite($out, '-- Backup Database Praktek dr. Eko Andrianto'."\n");
            fwrite($out, '-- Waktu Backup: '.now()->format('Y-m-d H:i:s')."\n");
            fwrite($out, "-- =========================================================\n\n");
            fwrite($out, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            $tables = DB::select('SHOW TABLES');
            foreach ($tables as $tObj) {
                $table = array_values((array) $tObj)[0];
                $create = DB::selectOne("SHOW CREATE TABLE `{$table}`");
                $createSql = ((array) $create)['Create Table'] ?? '';

                fwrite($out, "-- Tabel: `{$table}`\n");
                fwrite($out, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($out, $createSql.";\n\n");

                $rows = DB::table($table)->get();
                if ($rows->isNotEmpty()) {
                    foreach ($rows as $row) {
                        $arr = (array) $row;
                        $cols = array_map(fn ($c) => "`{$c}`", array_keys($arr));
                        $vals = array_map(fn ($v) => is_null($v) ? 'NULL' : $pdo->quote((string) $v), array_values($arr));
                        fwrite($out, "INSERT INTO `{$table}` (".implode(', ', $cols).') VALUES ('.implode(', ', $vals).");\n");
                    }
                    fwrite($out, "\n");
                }
            }

            fwrite($out, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($out);
        }, $filename, ['Content-Type' => 'application/sql; charset=UTF-8']);
    }
}

