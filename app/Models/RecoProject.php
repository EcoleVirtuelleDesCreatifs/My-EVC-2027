<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** RECO 60 : projet professionnel réel mené pendant 60 jours (CM, GIA). */
class RecoProject extends Model
{
    protected $guarded = ['id'];

    public const DAYS = 60;

    public const STATUSES = ['en_cours' => ['En cours', 'info'], 'rendu' => ['Rapport rendu', 'warn'], 'evalue' => ['Évalué', 'ok']];

    public const APPRECIATIONS = ['Excellent', 'Très bien', 'Acquis', 'À renforcer'];

    /** Étapes proposées à la création, par formation. */
    public const TEMPLATES = [
        'CM' => [['t' => 'Création de l\'identité du projet', 'd' => 'Nom, logo, positionnement, cible'], ['t' => 'Configuration des canaux', 'd' => 'Facebook, Instagram, TikTok, WhatsApp Business'],
            ['t' => 'Plan d\'action et calendrier', 'd' => 'Calendrier éditorial, campagnes'], ['t' => 'Exécution et suivi des KPI', 'd' => 'Publications, campagnes, résultats'], ['t' => 'Rapport final', 'd' => 'Bilan chiffré et recommandations']],
        'GIA' => [['t' => 'Création du projet professionnel', 'd' => 'Fiche projet et objectifs'], ['t' => 'Dépôt des livrables', 'd' => 'Fichier stocks, factures, présentation'],
            ['t' => 'Suivi des activités', 'd' => 'Journal hebdomadaire'], ['t' => 'Soutenance', 'd' => 'Présentation devant le formateur'], ['t' => 'Rapport final', 'd' => 'Rapport écrit de 10 pages']],
        'default' => [['t' => 'Cadrage du projet', 'd' => 'Objectifs et livrables'], ['t' => 'Réalisation', 'd' => 'Journal d\'activités'], ['t' => 'Rapport final', 'd' => 'Bilan']],
    ];

    protected function casts(): array
    {
        return ['steps' => 'array', 'kpis' => 'array', 'started_on' => 'date', 'submitted_at' => 'datetime', 'evaluated_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(RecoEntry::class)->orderByDesc('entry_date')->orderByDesc('id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /** Jour du projet (1 à 60). */
    public function day(): int
    {
        return max(1, min(self::DAYS, (int) $this->started_on->copy()->startOfDay()->diffInDays(now()->startOfDay()) + 1));
    }

    public function endsOn()
    {
        return $this->started_on->copy()->addDays(self::DAYS - 1);
    }

    public function stepsDone(): int
    {
        return collect($this->steps)->where('done', true)->count();
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status][0];
    }

    public function statusTone(): string
    {
        return self::STATUSES[$this->status][1];
    }
}
