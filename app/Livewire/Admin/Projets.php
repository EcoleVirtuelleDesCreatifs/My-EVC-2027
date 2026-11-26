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

    /** Réglages du parcours automatique (onglet « Attribution automatique »). */
    public array $auto = [];

    public function mount(): void
    {
        $this->auto = \App\Services\ProjectService::autoConfig();
    }

    public function enregistrerAuto(): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $this->validate(['auto.enabled' => 'boolean', 'auto.count' => 'required|integer|min:1|max:20', 'auto.days' => 'required|integer|min:1|max:60',
            'auto.delay' => 'required|integer|min:0|max:10080'], [], ['auto.count' => 'nombre de projets', 'auto.days' => 'délai de remise', 'auto.delay' => 'attente après un rendu']);
        \App\Services\ProjectService::saveAutoConfig($this->auto);
        $c = \App\Services\ProjectService::autoConfig();
        \App\Models\AuditLog::record('Attribution automatique des projets modifiée', ($c['enabled'] ? 'Activée' : 'Désactivée').' — '.$c['count'].' projets, '.$c['days'].' jours, envoi '.\App\Services\ProjectService::delayLabel($c['delay']).' après un rendu');
        $this->toast($c['enabled'] ? 'Attribution automatique enregistrée.' : 'Attribution automatique désactivée.');
    }

    public function envoyerMaintenant(int $studentId, int $formationId): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $a = app(\App\Services\ProjectService::class)->assignNextAuto(\App\Models\Student::findOrFail($studentId), $formationId, true);
        $this->toast($a ? 'Projet envoyé : l’étudiant est prévenu par e-mail.' : 'Aucun projet à envoyer pour cet étudiant.', $a ? 'ok' : 'warn');
    }

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

        $auto = collect();
        foreach ($catalogue->pluck('formation_id')->unique() as $fid) {
            foreach (\App\Services\ProjectService::autoProjects($fid)->pluck('id')->values() as $i => $id) {
                $auto->put($id, $i + 1);
            }
        }

        $destProject = $this->destinatairesProjet ? Project::findOrFail($this->destinatairesProjet) : null;
        $destinations = $destProject ? Assignment::with('student.user')->where('project_id', $destProject->id)
            ->when(trim($this->rechercheDestinataire), fn ($q, $term) => $q->whereHas('student', fn ($s) => $s->where(fn ($w) => $w->where('matricule', 'like', '%'.$term.'%')->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$term.'%')))))
            ->orderBy('student_id')->paginate(15, ['*'], 'destinatairesPage') : null;

        return $this->page('livewire.admin.projets', ['queue' => $queue, 'catalogue' => $catalogue, 'autoRank' => $auto,
            'plan' => $this->onglet === 'auto' ? app(\App\Services\ProjectService::class)->autoPlan() : collect(), 'autoConfig' => \App\Services\ProjectService::autoConfig(), 'destProject' => $destProject, 'destinations' => $destinations, 'formations' => Formation::active()->get()],
            'Projets', 'Administration <span class="sep">›</span> Projets', '<button class="btn btn-primary t-cta" wire:click="ouvrirAttribution"><i class="fa-solid fa-paper-plane"></i>Attribuer un projet</button>');
    }
}
