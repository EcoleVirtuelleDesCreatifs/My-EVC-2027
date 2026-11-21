<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Candidature;
use App\Models\Promotion;
use App\Services\AdmissionService;

class CandidatureFiche extends Page
{
    public Candidature $candidature;

    public ?int $score = null;

    public string $notes = '';

    public ?int $promotion = null;

    public string $mode = 'En ligne';

    public bool $activer = false;

    public function mount(): void
    {
        $this->score = $this->candidature->test_score;
        $this->notes = (string) $this->candidature->notes;
        $this->promotion = Promotion::current()?->id;
        $this->mode = str_contains((string) $this->candidature->mode, 'résentiel') ? 'Présentiel' : 'En ligne';
    }

    public function etape(string $stage): void
    {
        abort_unless(array_key_exists($stage, Candidature::STAGES) && $stage !== 'activee', 422);
        $this->candidature->update(['stage' => $stage]);
        AuditLog::record('Candidature : '.Candidature::STAGES[$stage][0], $this->candidature->reference);
        $this->toast('Étape mise à jour : '.Candidature::STAGES[$stage][0].'.');
    }

    public function enregistrer(): void
    {
        $this->validate(['score' => 'nullable|integer|min:0|max:20', 'notes' => 'nullable|string|max:5000']);
        $this->candidature->update(['test_score' => $this->score, 'notes' => $this->notes ?: null]);
        $this->toast('Dossier enregistré.');
    }

    public function closeModal(): void
    {
        $this->activer = false;
    }

    public function confirmerActivation(AdmissionService $svc)
    {
        $this->validate(['promotion' => 'required|exists:promotions,id', 'mode' => 'required|in:En ligne,Présentiel']);
        $student = $svc->activate($this->candidature, Promotion::findOrFail($this->promotion), $this->mode);
        session()->flash('toast', 'Compte activé : '.$student->matricule.'. L\'e-mail d\'activation est envoyé.');

        return $this->redirect(route('admin.etudiant', $student), navigate: true);
    }

    public function render()
    {
        $c = $this->candidature->load('files', 'student');
        $groups = [
            'Identité' => ['Genre', 'Date de naissance', 'Nationalité', 'Pays', 'Ville', 'Commune'],
            'Profil' => ['Situation', 'Niveau d\'études', 'Établissement', 'Poste', 'Niveau dans le domaine', 'Outils', 'Ordinateur', 'Connexion', 'Objectif', 'Source', 'Motivation', 'Disponibilités', 'Lien portfolio'],
            'Personne à contacter' => ['Contact - Nom', 'Contact - Lien', 'Contact - Téléphone', 'Contact - E-mail', 'Contact - Fonction', 'Contact - Résidence', 'Contact - Prend en charge les frais'],
        ];

        return $this->page('livewire.admin.candidature', ['c' => $c, 'groups' => $groups, 'promotions' => Promotion::orderByDesc('starts_on')->get()],
            $c->full_name, 'Administration <span class="sep">›</span> <a href="'.route('admin.candidatures').'" wire:navigate>Préinscriptions</a> <span class="sep">›</span> '.$c->reference);
    }
}
