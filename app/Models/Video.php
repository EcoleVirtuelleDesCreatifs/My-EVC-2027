<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    protected $guarded = ['id'];

    public const MODULES = ['Bien démarrer', 'Séances', 'Tutoriels', 'Masterclass'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function scopeVisibleFor(Builder $q, array $formationIds): Builder
    {
        return $q->where('published', true)->whereIn('formation_id', $formationIds);
    }

    /** Lien d'intégration (YouTube, Vimeo) ou null pour un fichier hébergé. */
    public function embedUrl(): ?string
    {
        if ($this->source === 'youtube' && preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})~', (string) $this->url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1].'?rel=0&modestbranding=1';
        }
        if ($this->source === 'vimeo' && preg_match('~vimeo\.com/(?:video/)?(\d+)~', (string) $this->url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }

        return null;
    }

    public function externalUrl(): ?string
    {
        $embed = $this->embedUrl();
        if ($this->source === 'youtube' && $embed && preg_match('~/embed/([\w-]{11})~', $embed, $m)) {
            return 'https://www.youtube.com/watch?v='.$m[1];
        }
        if ($this->source === 'vimeo' && $embed && preg_match('~/video/(\d+)~', $embed, $m)) {
            return 'https://vimeo.com/'.$m[1];
        }

        return null;
    }

    public function durationLabel(): string
    {
        return $this->duration_min >= 60 ? intdiv($this->duration_min, 60).' h '.str_pad((string) ($this->duration_min % 60), 2, '0', STR_PAD_LEFT) : $this->duration_min.' min';
    }
}
