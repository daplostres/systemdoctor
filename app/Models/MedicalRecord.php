<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalRecord extends Model
{
    public const PAYMENT_METHODS = [
        'cash' => 'Tunai (Cash)',
        'qris' => 'QRIS',
        'transfer' => 'Transfer Bank',
    ];

    protected $fillable = [
        'invoice_no', 'patient_id', 'appointment_id', 'visit_date', 'blood_pressure', 'weight',
        'temperature', 'complaint', 'diagnosis', 'treatment', 'prescription', 'pharmacy_note',
        'pharmacy_status', 'notes', 'fee', 'medicine_fee', 'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'fee' => 'integer',
            'medicine_fee' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MedicalRecord $record) {
            if (empty($record->invoice_no)) {
                $prefix = 'INV-'.now()->format('ymd').'-';
                $last = static::where('invoice_no', 'like', $prefix.'%')
                    ->orderByDesc('invoice_no')->value('invoice_no');
                $next = $last ? ((int) substr($last, -4)) + 1 : 1;
                $record->invoice_no = $prefix.str_pad($next, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicineItems(): HasMany
    {
        return $this->hasMany(MedicalRecordMedicine::class);
    }

    public function getTotalFeeAttribute(): int
    {
        return (int) $this->fee + (int) $this->medicine_fee;
    }

    public function getPaymentLabelAttribute(): string
    {
        return self::PAYMENT_METHODS[$this->payment_method] ?? 'Tunai';
    }
}
