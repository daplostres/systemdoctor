<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Ambil semua pengaturan sebagai array key => value (di-cache). */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings.all', fn () => static::pluck('value', 'key')->toArray());
    }

    public static function get(string $key, $default = null)
    {
        return static::allCached()[$key] ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('settings.all');
    }
}

