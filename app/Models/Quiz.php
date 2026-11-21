<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['required' => 'boolean', 'published' => 'boolean', 'opens_at' => 'datetime', 'closes_at' => 'datetime'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('position')->orderBy('id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function scopeVisibleFor(Builder $q, array $formationIds): Builder
    {
        return $q->where('published', true)->whereIn('formation_id', $formationIds)->has('questions');
    }

    public function isOpen(): bool
    {
        return $this->published && (! $this->opens_at || $this->opens_at->isPast()) && (! $this->closes_at || $this->closes_at->isFuture());
    }

    /** État pour un étudiant : [libellé, ton, peut commencer ?, meilleure tentative]. */
    public function stateFor(Student $s): array
    {
        $done = $this->attempts->where('student_id', $s->id)->whereNotNull('submitted_at');
        $best = $done->sortByDesc('percent')->first();
        $left = $this->attempts_allowed - $done->count();
        $passed = $best && $best->percent >= $this->pass_percent;
        $canStart = $this->isOpen() && ! $passed && $left > 0;

        return match (true) {
            $passed => ['Réussi · '.$best->percent.' %', 'ok', false, $best],
            (bool) $best && $left <= 0 => ['Non validé · '.$best->percent.' %', 'bad', false, $best],
            (bool) $best => ['À repasser · '.$best->percent.' %', 'warn', $canStart, $best],
            ! $this->isOpen() && $this->opens_at?->isFuture() => ['Ouvre le '.$this->opens_at->translatedFormat('j M à H\hi'), 'mute', false, null],
            ! $this->isOpen() => ['Clôturé', 'mute', false, null],
            default => ['À faire', 'acc', true, null],
        };
    }
}
