<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = ['actif' => 'Actif', 'suspendu' => 'Suspendu', 'certifie' => 'Certifié', 'abandon' => 'Abandon'];

    /** Seuil d'assiduité exigé pour la certification. */
    public const MIN_ATTENDANCE = 80;

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class, 'enrollments')->withPivot(['mode', 'status', 'promotion_id'])->orderBy('position');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class)->orderBy('due_on');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_on');
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(ResourceDownload::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function recoProjects(): HasMany
    {
        return $this->hasMany(RecoProject::class);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function videoProgress(): HasMany
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    public function firstName(): string
    {
        return explode(' ', $this->user->name)[0];
    }

    public static function nextMatricule(): string
    {
        $prefix = 'EVC-'.now()->format('y').'-';
        $last = static::where('matricule', 'like', $prefix.'%')->orderByDesc('matricule')->value('matricule');

        return $prefix.str_pad((string) ((int) substr((string) $last, -4) + 1), 4, '0', STR_PAD_LEFT);
    }

    /* ---------- Paiements ---------- */
    public function totalDue(): int
    {
        return (int) $this->schedules->sum('amount');
    }

    public function totalPaid(): int
    {
        return (int) $this->payments->sum('amount');
    }

    public function balance(): int
    {
        return max(0, $this->totalDue() - $this->totalPaid());
    }

    private ?array $allocationCache = null;

    /** Répartition des versements sur les échéances, de la plus ancienne à la plus récente : [schedule_id => montant]. */
    public function allocation(): array
    {
        if ($this->allocationCache !== null) {
            return $this->allocationCache;
        }
        $left = $this->totalPaid();
        $out = [];
        foreach ($this->schedules->sortBy([['due_on', 'asc'], ['id', 'asc']]) as $s) {
            $out[$s->id] = min($left, $s->amount);
            $left -= $out[$s->id];
        }

        return $this->allocationCache = $out;
    }

    /** Échéances prêtes à afficher (imputation calculée une seule fois). */
    public function scheduleRows()
    {
        return $this->schedules->each(fn ($s) => $s->setRelation('student', $this));
    }

    public function refreshAllocation(): void
    {
        $this->allocationCache = null;
        $this->unsetRelation('payments');
        $this->unsetRelation('schedules');
    }

    /** Prochaine échéance non soldée. */
    public function nextSchedule(): ?PaymentSchedule
    {
        return $this->scheduleRows()->first(fn (PaymentSchedule $s) => $s->remaining() > 0);
    }

    /* ---------- Séances ---------- */
    public function seancesQuery(?array $formationIds = null)
    {
        $ids = $formationIds ?? $this->formations->pluck('id')->all();

        return Seance::whereIn('formation_id', $ids)
            ->where(fn ($q) => $q->whereNull('promotion_id')->orWhere('promotion_id', $this->promotion_id))
            ->where('cancelled', false);
    }

    /** Taux de présence (%) sur les séances passées. */
    public function attendanceRate(?int $formationId = null): int
    {
        $past = $this->seancesQuery($formationId ? [$formationId] : null)->where('ends_at', '<', now())->pluck('id');
        if ($past->isEmpty()) {
            return 100;
        }
        $ok = $this->presences()->whereIn('seance_id', $past)->whereIn('status', ['present', 'retard'])->count();

        return (int) round($ok / $past->count() * 100);
    }

    /** Progression (%) : séances réalisées / séances prévues. */
    public function progress(int $formationId): int
    {
        $q = $this->seancesQuery([$formationId]);
        $total = (clone $q)->count();

        return $total ? (int) round((clone $q)->where('ends_at', '<', now())->count() / $total * 100) : 0;
    }

    /** Conditions de certification d'une formation : [libellé => rempli ?]. */
    public function certificationChecks(Formation $f): array
    {
        $paid = $this->scheduleRows()->where('formation_id', $f->id)->every(fn ($s) => $s->remaining() === 0);
        $projects = $this->assignments()->whereHas('project', fn ($q) => $q->where('formation_id', $f->id))->get();
        $projectsOk = $projects->isNotEmpty() && $projects->every(fn ($a) => $a->status === 'valide');
        $seancesDone = $this->progress($f->id) >= 100;

        $checks = [
            'Formation soldée' => $paid,
            'Tous les projets validés' => $projectsOk,
            'Assiduité d\'au moins '.self::MIN_ATTENDANCE.' %' => $this->attendanceRate($f->id) >= self::MIN_ATTENDANCE,
            'Toutes les séances réalisées' => $seancesDone,
        ];
        $required = Quiz::where('formation_id', $f->id)->where('published', true)->where('required', true)->with('attempts')->get();
        if ($required->isNotEmpty()) {
            $checks['Quiz obligatoires réussis'] = $required->every(fn (Quiz $q) => $q->attempts->where('student_id', $this->id)->whereNotNull('submitted_at')->max('percent') >= $q->pass_percent);
        }
        if ($f->has_reco) {
            $checks['RECO 60 évalué'] = $this->recoProjects()->where('formation_id', $f->id)->where('status', 'evalue')->exists();
        }

        return $checks;
    }

    public function isEligible(Formation $f): bool
    {
        return ! in_array(false, $this->certificationChecks($f), true);
    }
}
