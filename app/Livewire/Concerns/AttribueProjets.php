<?php

namespace App\Livewire\Concerns;

use App\Models\Project;
use App\Models\Student;
use App\Services\ProjectService;
use Illuminate\Support\Carbon;

/** Fenêtre d'attribution d'un projet à un groupe d'étudiants (Projets, Disponibilité). */
trait AttribueProjets
{
    public bool $attribution = false;

    public ?int $aProjet = null;

    public string $aEcheance = '';

    public array $aEtudiants = [];

    public bool $aNotifier = true;

    public function ouvrirAttribution(array $studentIds = [], ?int $projectId = null): void
    {
        $this->aEtudiants = array_map('intval', $studentIds);
        $this->aProjet = $projectId;
        $this->aEcheance = now()->addDays(10)->toDateString();
        $this->aNotifier = true;
        $this->resetErrorBag();
        $this->attribution = true;
    }

    public function confirmerAttribution(ProjectService $svc): void
    {
        $this->validate(['aProjet' => 'required|exists:projects,id', 'aEcheance' => 'required|date|after_or_equal:today', 'aEtudiants' => 'required|array|min:1'],
            ['aEtudiants.required' => 'Choisissez au moins un étudiant.'], ['aProjet' => 'projet', 'aEcheance' => 'échéance']);
        $students = Student::with('formations', 'user')->whereIn('id', $this->aEtudiants)->get();
        [$n, $skip] = $svc->assign(Project::findOrFail($this->aProjet), $students, Carbon::parse($this->aEcheance), $this->aNotifier);
        $this->attribution = false;
        $this->aEtudiants = [];
        $this->toast($n ? "Projet attribué à $n étudiant".($n > 1 ? 's' : '').($skip ? " · $skip ignoré(s) (autre formation ou déjà en cours)" : '').'.' : 'Aucun étudiant ne pouvait recevoir ce projet.', $n ? 'ok' : 'warn');
    }

    /** Étudiants de la formation du projet choisi (pour la liste à cocher). */
    protected function candidatsAttribution()
    {
        $p = $this->aProjet ? Project::find($this->aProjet) : null;

        return Student::with('user')->where('status', 'actif')
            ->when($p, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $p->formation_id)))
            ->get()->sortBy(fn ($s) => $s->user->name);
    }
}
