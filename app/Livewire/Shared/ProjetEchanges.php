<?php

namespace App\Livewire\Shared;

use App\Livewire\Page;
use App\Models\Assignment;
use App\Services\ProjectService;

class ProjetEchanges extends Page
{
    public Assignment $assignment;

    public string $message = '';

    public function mount(Assignment $assignment): void
    {
        abort_unless($assignment->canDiscuss(auth()->user()), 403);
    }

    public function envoyer(ProjectService $service): void
    {
        abort_unless($this->assignment->canDiscuss(auth()->user()), 403);
        $this->message = trim($this->message);
        $this->validate(['message' => 'required|string|max:2000']);
        $service->comment($this->assignment, $this->message);
        $this->reset('message');
        $this->dispatch('projet-message-envoye');
        $this->toast('Réponse envoyée. L’étudiant est prévenu.');
    }

    public function render()
    {
        abort_unless($this->assignment->canDiscuss(auth()->user()), 403);
        $a = $this->assignment->load('project.formation', 'student.user', 'comments.user');

        return $this->page('livewire.shared.projet-echanges', compact('a'), 'Échanges du projet', 'Suivi pédagogique');
    }
}
