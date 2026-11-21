<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formation extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['modules' => 'array', 'active' => 'boolean'];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function trainers(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }

    public function balance(): int
    {
        return $this->price - $this->deposit;
    }

    /** Classe CSS de couleur de la maquette (c-DG, f-CM…). */
    public function css(): string
    {
        return $this->code;
    }

    public function scopeActive($q)
    {
        return $q->where('active', true)->orderBy('position');
    }
}
