<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Project;
use App\Support\ProjectBrief;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;

class ProjetEditeur extends Page
{
    #[Locked]
    public ?int $projectId = null;

    public array $p = [];

    public function mount(?Project $project = null): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        if ($project?->exists) {
            $this->projectId = $project->id;
            $this->p = $project->only(['formation_id', 'title', 'type', 'published']);
            $this->p['brief'] = $project->briefHtml();
        } else {
            $formation = Formation::active()->whereKey(request()->integer('formation'))->value('id') ?? Formation::active()->value('id');
            $this->p = ['formation_id' => $formation, 'title' => '', 'type' => 'Projet', 'brief' => ProjectBrief::template(), 'published' => false];
        }
    }

    public function enregistrer(): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $data = $this->validate([
            'p.formation_id' => 'required|exists:formations,id', 'p.title' => 'required|string|max:150',
            'p.type' => 'required|string|max:60', 'p.brief' => 'required|string|max:100000', 'p.published' => 'required|boolean',
        ], [], ['p.title' => 'titre', 'p.type' => 'type', 'p.brief' => 'consignes', 'p.formation_id' => 'formation'])['p'];
        $data['brief'] = ProjectBrief::sanitize($data['brief']);
        $text = ProjectBrief::text($data['brief']);
        if ($text === '' || mb_strlen($text) > 20000) {
            throw ValidationException::withMessages(['p.brief' => 'Renseignez des consignes entre 1 et 20 000 caractères de texte.']);
        }
        $data['brief_format'] = 'html';
        DB::transaction(function () use ($data) {
            $project = $this->projectId ? Project::lockForUpdate()->findOrFail($this->projectId) : new Project();
            if ($project->exists && (int) $data['formation_id'] !== $project->formation_id && $project->assignments()->exists()) {
                throw ValidationException::withMessages(['p.formation_id' => 'La formation ne peut plus être changée après attribution du projet.']);
            }
            $project->fill($data)->save();
            AuditLog::record($this->projectId ? 'Projet modifié' : 'Projet créé', $project->title);
        });
        session()->flash('toast', $this->projectId ? 'Projet modifié.' : 'Projet créé.');
        $this->redirectRoute('admin.projets', ['onglet' => 'catalogue', 'formation' => $data['formation_id']], navigate: true);
    }

    public function render()
    {
        $assigned = $this->projectId ? Project::findOrFail($this->projectId)->assignments()->count() : 0;

        return $this->page('livewire.admin.projet-editeur', ['formations' => Formation::orderBy('name')->get(), 'assigned' => $assigned],
            $this->projectId ? 'Modifier le projet' : 'Nouveau projet', 'Administration <span class="sep">›</span> Projets <span class="sep">›</span> Éditeur');
    }
}
