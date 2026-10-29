<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = ['start_date', 'end_date', 'reason'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public static function activeOrUpcoming()
    {
        return static::whereDate('end_date', '>=', now()->toDateString())
            ->orderBy('start_date')->get();
    }

    public static function todayHoliday(): ?self
    {
        $today = now()->toDateString();

        return static::whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->first();
    }
}

