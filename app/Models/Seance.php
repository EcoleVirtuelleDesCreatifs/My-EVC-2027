<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seance extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'cancelled' => 'boolean'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    /** Le pointage est ouvert de 30 min avant le début jusqu'à la fin de la séance. */
    public function checkInOpen(): bool
    {
        return now()->between($this->starts_at->copy()->subMinutes(30), $this->ends_at);
    }

    public function isLive(): bool
    {
        return now()->between($this->starts_at, $this->ends_at);
    }

    public function hours(): string
    {
        return $this->starts_at->format('H\hi').' – '.$this->ends_at->format('H\hi');
    }

    public function modeLabel(): string
    {
        return $this->mode === 'virtuel' ? 'En ligne' : 'Présentiel';
    }

    /** Étudiants attendus à cette séance. */
    public function expectedStudents()
    {
        return Student::whereHas('enrollments', fn ($q) => $q->where('formation_id', $this->formation_id))
            ->when($this->promotion_id, fn ($q) => $q->where('promotion_id', $this->promotion_id))
            ->where('status', 'actif')->with('user');
    }
}
