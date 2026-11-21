<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Support\Collection;

/**
 * Disponibilité des étudiants pour un nouveau projet :
 *   « en cours »  au moins un projet démarré ou à reprendre ;
 *   « reçu »      au moins un projet attribué mais pas démarré ;
 *   « libre »     plus aucun projet à faire (rendus ou validés, ou aucun projet reçu).
 * « en retard » s'ajoute quand une échéance est dépassée sur un projet à faire.
 */
class AvailabilityService
{
    public const ETATS = [
        'libre' => ['Libre', 'ok', 'fa-circle-check', 'Prêt pour un nouveau projet'],
        'recu' => ['Projet reçu', 'warn', 'fa-envelope-open-text', 'Pas encore démarré'],
        'encours' => ['En cours', 'info', 'fa-pen-nib', 'Travaille sur un projet'],
    ];

    public function rows(?int $formationId = null): Collection
    {
        return Student::with(['user', 'formations', 'assignments.project'])->where('status', 'actif')
            ->when($formationId, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $formationId)))
            ->get()->map(fn (Student $s) => $this->row($s));
    }

    public function row(Student $s): array
    {
        $open = $s->assignments->whereIn('status', Assignment::OPEN);
        $etat = $open->whereIn('status', ['encours', 'reprendre'])->isNotEmpty() ? 'encours' : ($open->where('status', 'afaire')->isNotEmpty() ? 'recu' : 'libre');
        $last = $s->assignments->max(fn ($a) => $a->submitted_at ?? $a->validated_at);

        return [
            's' => $s, 'etat' => $etat, 'late' => $open->filter->isLate()->count(),
            'current' => $open->sortBy('due_on')->first(), 'valides' => $s->assignments->where('status', 'valide')->count(),
            'aValider' => $s->assignments->where('status', 'soumis')->count(), 'total' => $s->assignments->count(),
            'libreDepuis' => $etat === 'libre' && $last ? (int) $last->diffInDays(now()) : null,
        ];
    }

    public function summary(): array
    {
        $rows = $this->rows();

        return ['libre' => $rows->where('etat', 'libre')->count(), 'recu' => $rows->where('etat', 'recu')->count(),
            'encours' => $rows->where('etat', 'encours')->count(), 'retard' => $rows->where('late', '>', 0)->count(), 'total' => $rows->count()];
    }
}
