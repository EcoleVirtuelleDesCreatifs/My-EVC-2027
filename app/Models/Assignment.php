<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    protected $guarded = ['id'];

    /** Statut => [libellé côté étudiant, libellé côté équipe, ton] */
    public const STATUSES = [
        'afaire' => ['À démarrer', 'Projet reçu', 'mute'],
        'encours' => ['En cours', 'En cours', 'info'],
        'soumis' => ['Rendu, en validation', 'À valider', 'warn'],
        'reprendre' => ['À reprendre', 'À reprendre', 'bad'],
        'valide' => ['Validé', 'Validé', 'ok'],
    ];

    public const OPEN = ['afaire', 'encours', 'reprendre'];

    public const APPRECIATIONS = ['Excellent', 'Très bien', 'Acquis', 'À renforcer'];

    protected function casts(): array
    {
        return ['due_on' => 'date', 'started_at' => 'datetime', 'submitted_at' => 'datetime', 'validated_at' => 'datetime'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class)->latest();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AssignmentComment::class)->oldest();
    }

    public function isLate(): bool
    {
        return in_array($this->status, self::OPEN, true) && $this->due_on->endOfDay()->isPast();
    }

    public function label(bool $team = false): string
    {
        return self::STATUSES[$this->status][$team ? 1 : 0];
    }

    public function tone(): string
    {
        return $this->isLate() ? 'bad' : self::STATUSES[$this->status][2];
    }

    public function dueText(): string
    {
        $d = (int) now()->startOfDay()->diffInDays($this->due_on->copy()->startOfDay(), false);

        return match (true) {
            $d === 0 => 'Pour aujourd\'hui',
            $d === 1 => 'Pour demain',
            $d > 1 => 'Dans '.$d.' jours',
            $d === -1 => 'Hier',
            default => 'Il y a '.abs($d).' jours',
        };
    }

    public function log(string $kind, string $body, ?int $userId = null): void
    {
        $this->comments()->create(['kind' => $kind, 'body' => $body, 'user_id' => $userId ?? auth()->id()]);
    }
}
