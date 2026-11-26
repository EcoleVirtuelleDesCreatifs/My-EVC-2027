<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidature extends Model
{
    protected $guarded = ['id'];

    /** Étapes du parcours d'admission, dans l'ordre. */
    public const STAGES = [
        'recue' => ['Dossier reçu', 'info', 'Étude du profil sous 48 h'],
        'eligible' => ['Profil éligible', 'acc', 'Questionnaire SAOP envoyé par e-mail · 30 minutes au démarrage'],
        'test' => ['Test réussi', 'warn', 'En attente du 1er versement'],
        'paiement' => ['1er versement reçu', 'ok', 'Affectation à une promotion et activation du compte'],
        'activee' => ['Compte activé', 'ok', 'Bienvenue à l\'EVC'],
        'refusee' => ['Non retenue', 'bad', 'Candidature clôturée'],
    ];

    protected function casts(): array
    {
        return ['formations' => 'array', 'answers' => 'array', 'attribution' => 'array', 'saop_answers' => 'array', 'first_payment' => 'array',
            'saop_started_at' => 'datetime', 'saop_submitted_at' => 'datetime'];
    }

    /** Provenance lisible : « facebook / cpc · rentree-2027 », « Accès direct » sinon. */
    public function attributionLabel(): string
    {
        $a = $this->attribution ?? [];

        return ! empty($a['source']) ? implode(' · ', array_filter([$a['source'].(! empty($a['medium']) ? ' / '.$a['medium'] : ''), $a['campaign'] ?? null])) : 'Accès direct ou non renseigné';
    }

    public function saopClosed(): bool
    {
        return (bool) $this->saop_submitted_at || ($this->saop_started_at && now()->greaterThanOrEqualTo($this->saop_started_at->copy()->addMinutes(30)));
    }

    public function saopUrl(): string
    {
        return \Illuminate\Support\Facades\URL::temporarySignedRoute('candidature.questionnaire', now()->addDays(90), ['candidature' => $this->id]);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CandidatureFile::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function firstPayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'first_payment_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function stageLabel(): string
    {
        return self::STAGES[$this->stage][0] ?? $this->stage;
    }

    public function stageTone(): string
    {
        return self::STAGES[$this->stage][1] ?? 'mute';
    }

    /** Position de l'étape dans le parcours (0 à 4). */
    public function stageIndex(): int
    {
        return (int) array_search($this->stage, array_keys(self::STAGES), true);
    }

    public static function nextReference(): string
    {
        $prefix = 'PRE-'.now()->year.'-';
        $last = static::where('reference', 'like', $prefix.'%')->orderByDesc('reference')->value('reference');

        return $prefix.str_pad((string) ((int) substr((string) $last, -4) + 1), 4, '0', STR_PAD_LEFT);
    }
}
