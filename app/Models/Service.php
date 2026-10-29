<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'price', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'price' => 'integer'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}

