<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineStockLog extends Model
{
    protected $fillable = [
        'medicine_id', 'user_id', 'type', 'qty', 'stock_after', 'description',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'stock_after' => 'integer',
        ];
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

