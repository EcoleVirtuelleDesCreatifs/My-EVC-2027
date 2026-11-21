<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    protected $guarded = ['id'];

    /** Rayons de la bibliothèque : libellé, mention de couverture, icône, couvertures [fond, accent]. */
    public const CATEGORIES = [
        'logiciels' => ['Logiciels', 'Logiciel', 'fa-laptop-code', [['#26346B', '#8FB3FF'], ['#0E5E6F', '#9FE3D6'], ['#3C3489', '#FF8A6B'], ['#173B2F', '#A7E3B5']]],
        'ebooks' => ['E-books', 'E-book', 'fa-book', [['#1E1B4B', '#F2C76E'], ['#7A1F3D', '#F6D7A7'], ['#0F4C5C', '#F2C76E'], ['#4B2A7B', '#FF8A6B'], ['#8C3B1F', '#F6D7A7'], ['#1F5130', '#E9D8A6']]],
        'polices' => ['Polices', 'Polices', 'fa-font', [['#F4EEE2', '#7A1F3D'], ['#ECE8F6', '#3A3591'], ['#E6EFE8', '#1F5130'], ['#F7E9E2', '#C2401F']]],
        'graphiques' => ['Ressources graphiques', 'Ressources', 'fa-images', [['#EE5A36', '#FFD9A8'], ['#C2255C', '#FFC9DC'], ['#2F6BD8', '#BFD4FF'], ['#D88A17', '#FFF0C2']]],
        'modeles' => ['Modèles', 'Modèle', 'fa-file-lines', [['#2F5FB8', '#DCE7FF'], ['#1F8F5A', '#D5F2E3'], ['#3A3591', '#E4E2F3']]],
        'supports' => ['Supports de cours', 'Support de cours', 'fa-chalkboard', [['#1E1B4B', '#EE5A36'], ['#2B2766', '#FF8A6B'], ['#123B4A', '#EE5A36']]],
        'tutos' => ['Tutoriels vidéo', 'Tutoriel vidéo', 'fa-circle-play', [['#14182B', '#EE5A36'], ['#1B1640', '#FF8A6B']]],
    ];

    public const LICENCES = ['Réservé EVC', 'Libre et gratuit', 'Gratuit', 'Gratuit (version Pro payante)', 'Libre (OFL)', 'Licence payante'];

    public const NEW_DAYS = 7;

    protected function casts(): array
    {
        return ['essential' => 'boolean', 'published' => 'boolean'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(ResourceDownload::class);
    }

    public function scopeVisibleFor(Builder $q, array $formationIds): Builder
    {
        return $q->where('published', true)->where(fn ($w) => $w->whereNull('formation_id')->orWhereIn('formation_id', $formationIds));
    }

    public function isExternal(): bool
    {
        return (bool) $this->url;
    }

    public function isVideo(): bool
    {
        return $this->format === 'Vidéo';
    }

    public function isNew(): bool
    {
        return $this->created_at && $this->created_at->gt(now()->subDays(self::NEW_DAYS));
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category][0] ?? $this->category;
    }

    public function kicker(): string
    {
        return self::CATEGORIES[$this->category][1] ?? '';
    }

    public function icon(): string
    {
        return self::CATEGORIES[$this->category][2] ?? 'fa-book';
    }

    /** Couleurs de couverture, stables pour une ressource donnée. */
    public function cover(): array
    {
        $palette = self::CATEGORIES[$this->category][3] ?? self::CATEGORIES['supports'][3];

        return $palette[crc32((string) $this->id) % count($palette)];
    }

    /** Dimensions du livre sur l'étagère (légères variations, comme en vrai). */
    public function bookSize(): array
    {
        $h = crc32('size'.$this->id);

        return [124 + $h % 3 * 8, 178 + ($h >> 3) % 4 * 8];
    }

    public function coverTitle(): string
    {
        return preg_replace('/\s*\([^)]*\)\s*$/', '', $this->title);
    }

    public function formationCode(): string
    {
        return $this->formation?->code ?? 'EVC';
    }
}
