<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AttribueProjets;
use App\Livewire\Concerns\CorrigeProjets;
use App\Livewire\Page;
use App\Models\Assignment;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Project;
use Livewire\Attributes\Url;

class Projets extends Page
{
    use AttribueProjets, CorrigeProjets;

    #[Url(except: 'valider')]
    public string $onglet = 'valider';

    #[Url(except: '')]
    public string $formation = '';

    public bool $form = false;

    public ?int $editId = null;

    public array $p = [];

    public function creer(): void
    {
        $this->p = ['formation_id' => Formation::active()->value('id'), 'title' => '', 'type' => 'Projet', 'brief' => '', 'published' => true];
        $this->editId = null;
        $this->form = true;
    }

    public function modifier(int $id): void
    {
        $this->p = Project::findOrFail($id)->only(['formation_id', 'title', 'type', 'brief', 'published']);
        $this->editId = $id;
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $this->validate(['p.formation_id' => 'required|exists:formations,id', 'p.title' => 'required|string|max:150', 'p.type' => 'required|string|max:60', 'p.brief' => 'required|string|max:5000', 'p.published' => 'boolean'],
            [], ['p.title' => 'titre', 'p.brief' => 'consignes']);
        $x = Project::updateOrCreate(['id' => $this->editId], $this->p);
        AuditLog::record($this->editId ? 'Projet modifié' : 'Projet créé', $x->title);
        $this->form = false;
        $this->toast('Projet enregistré.');
    }

    public function closeModal(): void
    {
        $this->form = false;
        $this->corrige = null;
    }

    public function render()
    {
        $queue = Assignment::with('project.formation', 'student.user')->where('status', 'soumis')
            ->when($this->formation, fn ($q) => $q->whereHas('project', fn ($p) => $p->where('formation_id', $this->formation)))->orderBy('submitted_at')->get();
        $catalogue = Project::with('formation')->withCount(['assignments', 'assignments as valides_count' => fn ($q) => $q->where('status', 'valide')])
            ->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->orderBy('formation_id')->orderBy('title')->get();

        return $this->page('livewire.admin.projets', ['queue' => $queue, 'catalogue' => $catalogue, 'formations' => Formation::active()->get()],
            'Projets', 'Administration <span class="sep">›</span> Projets', '<button class="btn btn-primary t-cta" wire:click="ouvrirAttribution"><i class="fa-solid fa-paper-plane"></i>Attribuer un projet</button>');
    }
}
