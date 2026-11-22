<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AttribueProjets;
use App\Livewire\Concerns\CorrigeProjets;
use App\Livewire\Page;
use App\Models\Assignment;
use App\Models\Formation;
use App\Models\Project;
use Livewire\Attributes\Url;

class Projets extends Page
{
    use AttribueProjets, CorrigeProjets, \Livewire\WithPagination;

    #[\Livewire\Attributes\Locked]
    public ?int $destinatairesProjet = null;

    public string $rechercheDestinataire = '';

    public function voirDestinataires(int $id): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $this->destinatairesProjet = Project::findOrFail($id)->id;
        $this->rechercheDestinataire = '';
        $this->resetPage('destinatairesPage');
    }

    public function updatedRechercheDestinataire(): void
    {
        $this->resetPage('destinatairesPage');
    }

    #[Url(except: 'valider')]
    public string $onglet = 'valider';

    #[Url(except: '')]
    public string $formation = '';

    public function creer(): void
    {
        $this->redirectRoute('admin.projet.nouveau', ['formation' => $this->formation], navigate: true);
    }

    public function modifier(int $id): void
    {
        $this->redirectRoute('admin.projet.modifier', ['project' => Project::findOrFail($id)], navigate: true);
    }

    public function closeModal(): void
    {
        $this->corrige = null;
        $this->destinatairesProjet = null;
        $this->rechercheDestinataire = '';
        $this->resetPage('destinatairesPage');
    }

    public function render()
    {
        $queue = Assignment::with('project.formation', 'student.user')->where('status', 'soumis')
            ->when($this->formation, fn ($q) => $q->whereHas('project', fn ($p) => $p->where('formation_id', $this->formation)))->orderBy('submitted_at')->get();
        $catalogue = Project::with('formation')->withCount(['assignments', 'assignments as valides_count' => fn ($q) => $q->where('status', 'valide')])
            ->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->orderBy('formation_id')->orderBy('title')->get();

        $destProject = $this->destinatairesProjet ? Project::findOrFail($this->destinatairesProjet) : null;
        $destinations = $destProject ? Assignment::with('student.user')->where('project_id', $destProject->id)
            ->when(trim($this->rechercheDestinataire), fn ($q, $term) => $q->whereHas('student', fn ($s) => $s->where(fn ($w) => $w->where('matricule', 'like', '%'.$term.'%')->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$term.'%')))))
            ->orderBy('student_id')->paginate(15, ['*'], 'destinatairesPage') : null;

        return $this->page('livewire.admin.projets', ['queue' => $queue, 'catalogue' => $catalogue, 'destProject' => $destProject, 'destinations' => $destinations, 'formations' => Formation::active()->get()],
            'Projets', 'Administration <span class="sep">›</span> Projets', '<button class="btn btn-primary t-cta" wire:click="ouvrirAttribution"><i class="fa-solid fa-paper-plane"></i>Attribuer un projet</button>');
    }
}
