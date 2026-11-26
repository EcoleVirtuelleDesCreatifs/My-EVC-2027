<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Classe virtuelle (groupe WhatsApp, visioconférence…) d'une formation, éventuellement réservée à une session. */
class VirtualClass extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    /** Classes actives visibles par un étudiant (ses formations, sa session ou toutes les sessions). */
    public function scopeVisibleFor(Builder $q, Student $s): Builder
    {
        return $q->where('active', true)->whereIn('formation_id', $s->formations->pluck('id'))
            ->where(fn ($w) => $w->whereNull('promotion_id')->orWhere('promotion_id', $s->promotion_id))->orderBy('position')->orderBy('id');
    }

    /** Plateforme reconnue d'après le lien : [libellé, icône]. */
    public function platform(): array
    {
        $host = strtolower((string) parse_url($this->url, PHP_URL_HOST));

        return match (true) {
            str_contains($host, 'whatsapp.com') || str_contains($host, 'wa.me') => ['WhatsApp', 'fa-brands fa-whatsapp'],
            str_contains($host, 'meet.google') => ['Google Meet', 'fa-solid fa-video'],
            str_contains($host, 'zoom.') => ['Zoom', 'fa-solid fa-video'],
            str_contains($host, 'teams.') => ['Microsoft Teams', 'fa-solid fa-users'],
            str_contains($host, 't.me') || str_contains($host, 'telegram') => ['Telegram', 'fa-brands fa-telegram'],
            default => ['Classe en ligne', 'fa-solid fa-chalkboard-user'],
        };
    }
}
