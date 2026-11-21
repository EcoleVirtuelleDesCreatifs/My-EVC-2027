<?php

namespace App\Livewire\Gestion;

use App\Livewire\Concerns\PorteeFormations;
use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Quiz as QuizModel;
use Livewire\Attributes\Url;

class Quiz extends Page
{
    use PorteeFormations;

    #[Url(except: '')]
    public string $formation = '';

    public bool $form = false;

    public array $q = [];

    public function creer(): void
    {
        $this->q = ['formation_id' => $this->listeFormations()->first()?->id, 'title' => '', 'description' => '', 'duration_min' => 15, 'attempts_allowed' => 2, 'pass_percent' => 60, 'required' => false];
        $this->resetErrorBag();
        $this->form = true;
    }

    public function enregistrer()
    {
        $this->validate(['q.formation_id' => 'required|exists:formations,id', 'q.title' => 'required|string|max:150', 'q.description' => 'nullable|string|max:2000',
            'q.duration_min' => 'required|integer|min:1|max:240', 'q.attempts_allowed' => 'required|integer|min:1|max:10', 'q.pass_percent' => 'required|integer|min:1|max:100', 'q.required' => 'boolean'],
            [], ['q.title' => 'titre', 'q.duration_min' => 'durée', 'q.attempts_allowed' => 'tentatives', 'q.pass_percent' => 'seuil de réussite']);
        $this->verifierFormation((int) $this->q['formation_id']);
        $quiz = QuizModel::create($this->q + ['created_by' => auth()->id(), 'published' => false]);
        AuditLog::record('Quiz créé', $quiz->title);
        session()->flash('toast', 'Quiz créé : ajoutez maintenant les questions.');

        return $this->redirect(gestion_route('quiz.editeur', $quiz), navigate: true);
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        $fids = $this->formationsGerees();
        $items = QuizModel::with('formation')->withCount(['questions', 'attempts as passages' => fn ($a) => $a->whereNotNull('submitted_at')])
            ->withAvg(['attempts as moyenne' => fn ($a) => $a->whereNotNull('submitted_at')], 'percent')
            ->when($fids, fn ($q, $ids) => $q->whereIn('formation_id', $ids))->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->latest()->get();

        return $this->page('livewire.gestion.quiz', ['items' => $items, 'formations' => $this->listeFormations()], 'Quiz', $this->filAriane('Quiz'),
            '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouveau quiz</button>');
    }
}
