<?php

namespace App\Livewire\Gestion;

use App\Livewire\Concerns\PorteeFormations;
use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Seance;
use App\Models\User;
use App\Models\Video;
use App\Notifications\Avis;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class WebTv extends Page
{
    use PorteeFormations, WithFileUploads, WithPagination;

    #[Url(except: '')]
    public string $formation = '';

    public bool $form = false;

    public ?int $editId = null;

    public array $v = [];

    public $fichier;

    public bool $notifier = true;

    public function updatedFormation(): void
    {
        $this->resetPage();
    }

    private function query()
    {
        return Video::with('formation', 'seance')->withCount(['progress as vues_completes' => fn ($q) => $q->whereNotNull('completed_at')])
            ->when($this->formationsGerees(), fn ($q, $ids) => $q->whereIn('formation_id', $ids));
    }

    public function creer(?int $seanceId = null): void
    {
        $se = $seanceId ? Seance::find($seanceId) : null;
        $this->v = ['formation_id' => $se?->formation_id ?? $this->listeFormations()->first()?->id, 'seance_id' => $se?->id ?? '', 'module' => $se ? 'Séances' : 'Tutoriels',
            'title' => $se ? 'Replay — '.$se->title : '', 'description' => '', 'source' => 'fichier', 'url' => '', 'duration_min' => '', 'published' => true];
        $this->reset('fichier', 'editId');
        $this->notifier = true;
        $this->resetErrorBag();
        $this->form = true;
    }

    public function modifier(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        $this->v = array_map(fn ($val) => $val ?? '', $x->only(['formation_id', 'seance_id', 'module', 'title', 'description', 'source', 'url', 'duration_min', 'published']));
        $this->editId = $id;
        $this->reset('fichier');
        $this->resetErrorBag();
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $fichier = $this->v['source'] === 'fichier';
        $this->validate([
            'v.formation_id' => 'required|exists:formations,id', 'v.seance_id' => 'nullable|exists:seances,id', 'v.module' => 'required|string|max:60',
            'v.title' => 'required|string|max:180', 'v.description' => 'nullable|string|max:3000', 'v.source' => 'required|in:fichier,youtube,vimeo',
            'v.url' => $fichier ? 'nullable' : ['required', 'url', $this->v['source'] === 'youtube' ? 'regex:~(youtube\.com|youtu\.be)~' : 'regex:~vimeo\.com~'],
            'v.duration_min' => 'nullable|integer|min:0|max:600',
            'fichier' => ($fichier && ! $this->editId ? 'required|' : 'nullable|').'file|mimetypes:video/mp4,video/webm,video/quicktime|max:512000',
        ], ['v.url.regex' => 'L\'adresse ne correspond pas à la plateforme choisie.'], ['v.title' => 'titre', 'v.url' => 'lien de la vidéo', 'fichier' => 'fichier vidéo']);
        $this->verifierFormation((int) $this->v['formation_id']);

        $x = $this->editId ? $this->query()->findOrFail($this->editId) : new Video(['created_by' => auth()->id()]);
        $x->fill(collect($this->v)->map(fn ($val) => $val === '' ? null : $val)->all());
        $x->duration_min = (int) ($this->v['duration_min'] ?: 0);
        $x->published = (bool) $this->v['published'];
        if ($fichier && $this->fichier) {
            if ($x->path) {
                Storage::disk('local')->delete($x->path);
            }
            $x->path = $this->fichier->store('webtv', 'local');
        }
        if (! $fichier) {
            $x->path = null;
        } else {
            $x->url = null;
        }
        $isNew = ! $x->exists;
        $x->save();
        if ($isNew && $x->published && $this->notifier) {
            User::whereIn('id', \App\Models\Student::whereHas('enrollments', fn ($q) => $q->where('formation_id', $x->formation_id))->where('status', 'actif')->pluck('user_id'))->get()
                ->each->notify(new Avis('Nouvelle vidéo', '« '.$x->title.' » est disponible dans la Web TV.', route('etudiant.video', $x), 'fa-tv', 'info'));
        }
        AuditLog::record($isNew ? 'Vidéo publiée' : 'Vidéo modifiée', 'Web TV — '.$x->title);
        $this->form = false;
        $this->toast($isNew ? 'Vidéo enregistrée.' : 'Vidéo mise à jour.');
    }

    public function publier(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        $x->update(['published' => ! $x->published]);
        $this->toast($x->published ? 'Vidéo publiée.' : 'Vidéo masquée.', $x->published ? 'ok' : 'info');
    }

    public function supprimer(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        if ($x->path) {
            Storage::disk('local')->delete($x->path);
        }
        $x->delete();
        AuditLog::record('Vidéo supprimée', 'Web TV — '.$x->title);
        $this->toast('Vidéo supprimée.');
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        $items = $this->query()->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->latest()->paginate(20);
        $fids = $this->formationsGerees();
        $sansReplay = Seance::with('formation')->where('ends_at', '<', now())->where('ends_at', '>=', now()->subDays(21))->where('cancelled', false)
            ->when($fids, fn ($q) => $q->whereIn('formation_id', $fids))
            ->whereNotIn('id', Video::whereNotNull('seance_id')->pluck('seance_id'))->orderByDesc('starts_at')->take(5)->get();
        $seances = ($this->form && ($this->v['formation_id'] ?? null)) ? Seance::where('formation_id', $this->v['formation_id'])->where('starts_at', '<', now())->orderByDesc('starts_at')->take(40)->get() : collect();
        $stats = ['total' => (clone $this->query())->count(), 'vues' => (clone $this->query())->sum('views_count')];

        return $this->page('livewire.gestion.webtv', ['items' => $items, 'formations' => $this->listeFormations(), 'sansReplay' => $sansReplay, 'seances' => $seances, 'stats' => $stats],
            'Web TV', $this->filAriane('Web TV'), '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-plus"></i>Ajouter une vidéo</button>');
    }
}
