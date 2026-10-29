<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalLetter extends Model
{
    public const TYPES = [
        'sick' => 'Surat Keterangan Sakit',
        'healthy' => 'Surat Keterangan Sehat',
        'referral' => 'Surat Rujukan Medis',
    ];

    protected $fillable = [
        'letter_no', 'type', 'patient_id', 'medical_record_id', 'issue_date',
        'occupation', 'rest_days', 'start_date', 'end_date',
        'height', 'weight', 'blood_pressure', 'temperature', 'color_blind', 'purpose',
        'referral_to', 'referral_specialist', 'anamnesis', 'provisional_diagnosis', 'given_therapy',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'rest_days' => 'integer',
            'height' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MedicalLetter $letter) {
            if (empty($letter->letter_no)) {
                $prefixCode = match ($letter->type) {
                    'sick' => 'SKS',
                    'healthy' => 'SKH',
                    'referral' => 'SRJ',
                    default => 'SRT',
                };
                $prefix = $prefixCode.'/'.now()->format('Y/m').'/';
                $count = static::where('letter_no', 'like', $prefix.'%')->count() + 1;
                $letter->letter_no = $prefix.str_pad($count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Surat Keterangan';
    }
}
