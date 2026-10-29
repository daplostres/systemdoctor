<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Appointment extends Model
{
    public const STATUSES = [
        'pending' => ['label' => 'Menunggu Dipanggil', 'color' => 'warning'],
        'confirmed' => ['label' => 'Sedang Dipanggil / Diperiksa', 'color' => 'primary'],
        'completed' => ['label' => 'Selesai', 'color' => 'success'],
        'cancelled' => ['label' => 'Batal / Tidak Hadir', 'color' => 'danger'],
    ];

    protected $fillable = [
        'code', 'patient_id', 'service_id', 'appointment_date',
        'queue_number', 'complaint', 'status', 'called_at', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'called_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Appointment $a) {
            if (empty($a->code)) {
                do {
                    $code = 'ANT'.now()->format('ymd').strtoupper(Str::random(3));
                } while (static::where('code', $code)->exists());
                $a->code = $code;
            }
            if (empty($a->queue_number)) {
                $a->queue_number = static::whereDate('appointment_date', $a->appointment_date)->max('queue_number') + 1;
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUSES[$this->status]['color'] ?? 'secondary';
    }
}
