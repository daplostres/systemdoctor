<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecordMedicine extends Model
{
    protected $fillable = [
        'medical_record_id', 'medicine_id', 'qty', 'price', 'dosage',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'price' => 'integer',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->qty * $this->price;
    }
}

