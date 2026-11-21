<?php

namespace App\Livewire\Gestion;

use App\Livewire\Concerns\PorteeFormations;
use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\RecoEntry;
use App\Models\RecoProject;
use App\Notifications\Avis;

class RecoFiche extends Page
{
    use PorteeFormations;

    public RecoProject $project;

    public array $retours = [];

    public string $appreciation = 'Acquis';

    public string $evaluation = '';

    public function mount(): void
    {
        $this->verifierFormation($this->project->formation_id);
    }

    public function commenter(int $entryId): void
    {
        $e = RecoEntry::where('reco_project_id', $this->project->id)->findOrFail($entryId);
        $txt = trim($this->retours[$entryId] ?? '');
        if ($txt === '') {
            return;
        }
        $e->update(['feedback' => mb_substr($txt, 0, 2000), 'feedback_by' => auth()->id(), 'feedback_at' => now()]);
        unset($this->retours[$entryId]);
        $this->project->student->user->notify(new Avis('Retour sur votre RECO 60', auth()->user()->name.' a commenté « '.$e->action.' ».', route('etudiant.reco'), 'fa-comment', 'lav'));
        $this->toast('Commentaire envoyé à l\'étudiant.');
    }

    public function evaluer(): void
    {
        abort_unless($this->project->status === 'rendu', 422);
        $this->validate(['appreciation' => 'required|in:'.implode(',', RecoProject::APPRECIATIONS), 'evaluation' => 'required|string|min:10|max:3000'], [], ['evaluation' => 'commentaire d\'évaluation']);
        $this->project->update(['status' => 'evalue', 'appreciation' => $this->appreciation, 'evaluation' => trim($this->evaluation), 'evaluated_by' => auth()->id(), 'evaluated_at' => now()]);
        $this->project->student->user->notify(new Avis('RECO 60 évalué', '« '.$this->project->name.' » : '.$this->appreciation.'.', route('etudiant.reco'), 'fa-award', 'ok', true));
        AuditLog::record('RECO 60 évalué', $this->project->name.' — '.$this->project->student->user->name);
        $this->toast('Évaluation enregistrée.');
    }

    public function rouvrir(): void
    {
        $this->project->update(['status' => 'en_cours', 'submitted_at' => null]);
        $this->project->student->user->notify(new Avis('RECO 60 à compléter', 'Votre formateur vous demande de compléter « '.$this->project->name.' » avant l\'évaluation.', route('etudiant.reco'), 'fa-rotate', 'warn', true));
        $this->toast('Le RECO 60 est rouvert : l\'étudiant peut le compléter.', 'info');
    }

    public function render()
    {
        $p = $this->project->load('student.user', 'formation', 'entries.feedbackAuthor', 'evaluator');

        return $this->page('livewire.gestion.reco-fiche', compact('p'), $p->name,
            $this->filAriane('<a href="'.gestion_route('reco').'" wire:navigate>RECO 60</a>').' <span class="sep">›</span> '.e($p->student->user->name));
    }
}
