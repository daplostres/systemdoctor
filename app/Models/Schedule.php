<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public const DAYS = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
        5 => 'Jumat', 6 => 'Sabtu', 0 => 'Minggu',
    ];

    protected $fillable = ['day', 'start_time', 'end_time', 'quota', 'is_active', 'note'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'day' => 'integer', 'quota' => 'integer'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /** Urutkan Senin..Minggu */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByRaw('CASE WHEN day = 0 THEN 7 ELSE day END')->orderBy('start_time');
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day] ?? '-';
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }
}

