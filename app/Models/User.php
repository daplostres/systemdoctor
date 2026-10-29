<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLES = [
        'admin' => 'Administrator (All Access)',
        'doctor' => 'Dokter Pemeriksa',
        'assistant' => 'Petugas Pendaftaran & Antrean',
        'medical_record' => 'Petugas Rekam Medis & Surat',
        'pharmacist' => 'Petugas Obat / Apotek',
    ];

    public const ROLE_BADGES = [
        'admin' => 'dark',
        'doctor' => 'primary',
        'assistant' => 'info',
        'medical_record' => 'success',
        'pharmacist' => 'warning',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function hasRole(string ...$roles): bool
    {
        if ($this->role === 'admin') {
            return true; // Admin memiliki akses ke semua bagian
        }

        return in_array($this->role, $roles, true);
    }

    public function getRoleLabelAttribute(): string
    {
        return self::ROLES[$this->role] ?? 'Petugas';
    }

    public function getRoleBadgeAttribute(): string
    {
        return self::ROLE_BADGES[$this->role] ?? 'secondary';
    }
}
