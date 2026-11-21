<?php

namespace App\Livewire\Concerns;

use App\Models\Assignment;
use App\Services\ProjectService;

/** Fenêtre de correction d'un projet rendu (formateurs et administration). */
trait CorrigeProjets
{
    public ?int $corrige = null;

    public string $appreciation = 'Acquis';

    public string $commentaire = '';

    public function ouvrirCorrection(int $id): void
    {
        $this->authorizeCorrection(Assignment::with('project')->findOrFail($id));
        $this->corrige = $id;
        $this->appreciation = 'Acquis';
        $this->commentaire = '';
        $this->resetErrorBag();
    }

    public function valider(ProjectService $svc): void
    {
        $a = $this->corrigeEnCours();
        $this->validate(['appreciation' => 'required|in:'.implode(',', Assignment::APPRECIATIONS), 'commentaire' => 'nullable|string|max:2000']);
        $svc->validate($a, $this->appreciation, trim($this->commentaire) ?: null);
        $this->corrige = null;
        $this->toast('Projet validé. L\'étudiant est prévenu.');
    }

    public function aReprendre(ProjectService $svc): void
    {
        $a = $this->corrigeEnCours();
        $this->validate(['commentaire' => 'required|string|min:5|max:2000'], ['commentaire.required' => 'Expliquez à l\'étudiant ce qu\'il doit reprendre.']);
        $svc->rework($a, trim($this->commentaire));
        $this->corrige = null;
        $this->toast('Projet renvoyé à l\'étudiant avec votre commentaire.', 'info');
    }

    public function closeModal(): void
    {
        $this->corrige = null;
    }

    protected function corrigeEnCours(): Assignment
    {
        $a = Assignment::with('project', 'student.user', 'submissions.files', 'comments.user')->findOrFail($this->corrige);
        $this->authorizeCorrection($a);

        return $a;
    }

    protected function authorizeCorrection(Assignment $a): void
    {
        $u = auth()->user();
        abort_unless($u->can('admin.projets') || in_array($a->project->formation_id, $u->taughtFormationIds()), 403);
    }
}
