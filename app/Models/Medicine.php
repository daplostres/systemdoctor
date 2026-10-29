<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    public const UNITS = ['Tablet', 'Kapsul', 'Strip', 'Botol', 'Tube', 'Sachet', 'Ampul', 'Pcs'];

    protected $fillable = [
        'code', 'name', 'category', 'unit', 'price', 'stock', 'min_stock', 'expired_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'min_stock' => 'integer',
            'expired_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Medicine $med) {
            if (empty($med->code)) {
                $last = static::orderByDesc('id')->value('id') ?? 0;
                $med->code = 'OBT-'.str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function recordItems(): HasMany
    {
        return $this->hasMany(MedicalRecordMedicine::class);
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(MedicineStockLog::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_date !== null && $this->expired_date->lt(now()->startOfDay());
    }

    /** Mendekati kedaluwarsa dalam <= 60 hari ke depan */
    public function getIsNearExpiryAttribute(): bool
    {
        return $this->expired_date !== null
            && ! $this->is_expired
            && $this->expired_date->lte(now()->addDays(60));
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
