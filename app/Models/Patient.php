<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'medical_record_no', 'name', 'nik', 'gender', 'birth_date',
        'phone', 'address', 'blood_type', 'allergies',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            if (empty($patient->medical_record_no)) {
                $patient->medical_record_no = static::generateRecordNo();
            }
        });
    }

    /** Format: RM-YYMM-0001 */
    public static function generateRecordNo(): string
    {
        $prefix = 'RM-'.now()->format('ym').'-';
        $last = static::where('medical_record_no', 'like', $prefix.'%')
            ->orderByDesc('medical_record_no')->value('medical_record_no');
        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getAgeAttribute(): ?string
    {
        return $this->birth_date ? $this->birth_date->age.' th' : null;
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}

