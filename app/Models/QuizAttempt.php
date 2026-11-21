<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $guarded = ['id'];

    /** Délai de grâce (secondes) après la fin du temps imparti, pour absorber la latence réseau. */
    public const GRACE = 30;

    protected function casts(): array
    {
        return ['answers' => 'array', 'started_at' => 'datetime', 'submitted_at' => 'datetime'];
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function deadline()
    {
        return $this->started_at->copy()->addMinutes($this->quiz->duration_min);
    }

    /** Temps écoulé, délai de grâce (latence réseau) compris. */
    public function expired(): bool
    {
        return now()->gt($this->deadline()->addSeconds(self::GRACE));
    }

    public function secondsLeft(): int
    {
        return max(0, (int) now()->diffInSeconds($this->deadline(), false));
    }

    public function passed(): bool
    {
        return $this->submitted_at && $this->percent >= $this->quiz->pass_percent;
    }
}
