<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Assignment;
use App\Services\ProjectService;
use Livewire\WithFileUploads;

class ProjetDetail extends Page
{
    use WithFileUploads;

    public Assignment $assignment;

    public array $fichiers = [];

    public string $liens = '';

    public string $message = '';

    public string $commentaire = '';

    public function mount(Assignment $assignment): void
    {
        abort_unless($assignment->student_id === $this->student()->id, 403);
    }

    public function demarrer(ProjectService $svc): void
    {
        $svc->start($this->assignment);
        $this->toast('Projet démarré. Votre formateur est informé.');
    }

    public function deposer(ProjectService $svc): void
    {
        $this->validate([
            'fichiers.*' => 'file|max:51200|mimes:pdf,jpg,jpeg,png,webp,gif,zip,rar,ai,psd,indd,eps,svg,doc,docx,xls,xlsx,ppt,pptx,mp4,mov',
            'liens' => 'nullable|string|max:2000', 'message' => 'nullable|string|max:2000',
        ], [], ['fichiers.*' => 'fichier']);
        $links = collect(preg_split('/\s+/', trim($this->liens)))->filter(fn ($l) => filter_var($l, FILTER_VALIDATE_URL))->values()->all();
        $svc->submit($this->assignment, $this->fichiers, $links, trim($this->message) ?: null);
        $this->reset('fichiers', 'liens', 'message');
        $this->toast('Projet rendu ! Votre formateur va le corriger.');
    }

    public function retirerFichier(int $i): void
    {
        array_splice($this->fichiers, $i, 1);
    }

    public function commenter(ProjectService $svc): void
    {
        $this->validate(['commentaire' => 'required|string|max:2000']);
        $svc->comment($this->assignment, trim($this->commentaire));
        $this->reset('commentaire');
        $this->toast('Message envoyé à votre formateur.');
    }

    public function render()
    {
        $a = $this->assignment->load('project.formation', 'comments.user', 'submissions.files');

        return $this->page('livewire.etudiant.projet', compact('a'), $a->project->title,
            'Espace étudiant <span class="sep">›</span> <a href="'.route('etudiant.projets').'" wire:navigate>Mes projets</a>');
    }
}
