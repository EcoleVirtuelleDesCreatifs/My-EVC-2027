<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Resource;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/** Gestion de la bibliothèque : administration (toutes formations) et formateurs (leur formation). */
class Bibliotheque extends Page
{
    use WithFileUploads, WithPagination;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $formation = '';

    #[Url(except: '')]
    public string $rayon = '';

    #[Url(except: '')]
    public string $statut = '';

    public bool $form = false;

    public ?int $editId = null;

    public array $r = [];

    public $fichier;

    public bool $notifier = true;

    public function updated($prop): void
    {
        if (in_array($prop, ['q', 'formation', 'rayon', 'statut'])) {
            $this->resetPage();
        }
        if ($prop === 'fichier' && $this->fichier) {
            $mo = $this->fichier->getSize() / 1048576;
            $this->r['format'] = strtoupper($this->fichier->getClientOriginalExtension());
            $this->r['size'] = $mo >= 1 ? number_format($mo, 1, ',', ' ').' Mo' : max(1, round($this->fichier->getSize() / 1024)).' Ko';
            $this->r['title'] = $this->r['title'] ?: str(pathinfo($this->fichier->getClientOriginalName(), PATHINFO_FILENAME))->replace(['-', '_'], ' ')->ucfirst()->value();
        }
    }

    /** Formations gérées par l'utilisateur (null = toutes, y compris les ressources communes). */
    private function scope(): ?array
    {
        return auth()->user()->space() === 'formateur' ? auth()->user()->taughtFormationIds() : null;
    }

    private function query()
    {
        return Resource::with('formation')->when($this->scope(), fn ($q, $ids) => $q->whereIn('formation_id', $ids));
    }

    public function creer(): void
    {
        $this->r = ['title' => '', 'category' => 'ebooks', 'formation_id' => $this->scope()[0] ?? '', 'description' => '', 'source' => 'fichier', 'url' => '',
            'format' => '', 'size' => '', 'licence' => 'Réservé EVC', 'compatible' => '', 'essential' => false, 'published' => true];
        $this->reset('fichier', 'editId');
        $this->notifier = true;
        $this->resetErrorBag();
        $this->form = true;
    }

    public function modifier(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        $this->r = $x->only(['title', 'category', 'description', 'url', 'format', 'size', 'licence', 'compatible', 'essential', 'published'])
            + ['formation_id' => $x->formation_id ?? '', 'source' => $x->url ? 'lien' : 'fichier'];
        $this->r = array_map(fn ($v) => $v ?? '', $this->r);
        $this->editId = $id;
        $this->reset('fichier');
        $this->resetErrorBag();
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $lien = ($this->r['source'] ?? 'fichier') === 'lien';
        $scope = $this->scope();
        $this->validate([
            'r.title' => 'required|string|max:150', 'r.category' => 'required|in:'.implode(',', array_keys(Resource::CATEGORIES)),
            'r.formation_id' => $scope ? 'required|in:'.implode(',', $scope) : 'nullable|exists:formations,id',
            'r.description' => 'required|string|max:300', 'r.licence' => 'required|in:'.implode(',', Resource::LICENCES),
            'r.url' => $lien ? 'required|url|max:255' : 'nullable', 'r.format' => 'nullable|string|max:60', 'r.size' => 'nullable|string|max:30', 'r.compatible' => 'nullable|string|max:120',
            'fichier' => (! $lien && ! $this->editId ? 'required|' : 'nullable|').'file|max:51200',
        ], [], ['r.title' => 'titre', 'r.description' => 'description', 'r.url' => 'adresse du site officiel', 'r.formation_id' => 'formation']);

        $x = $this->editId ? $this->query()->findOrFail($this->editId) : new Resource(['created_by' => auth()->id()]);
        $x->fill([
            'title' => $this->r['title'], 'category' => $this->r['category'], 'formation_id' => $this->r['formation_id'] ?: null, 'description' => $this->r['description'],
            'format' => $this->r['format'] ?: ($lien ? 'Site officiel' : 'Fichier'), 'size' => $this->r['size'] ?: null, 'licence' => $this->r['licence'],
            'compatible' => $this->r['compatible'] ?: null, 'url' => $lien ? $this->r['url'] : null, 'essential' => (bool) $this->r['essential'], 'published' => (bool) $this->r['published'],
        ]);
        if ($this->fichier && ! $lien) {
            if ($x->path) {
                Storage::disk('local')->delete($x->path);
            }
            $x->path = $this->fichier->storeAs('library', str($this->r['title'])->slug().'-'.now()->format('YmdHis').'.'.$this->fichier->getClientOriginalExtension(), 'local');
        }
        if ($lien) {
            $x->path = null;
        }
        $isNew = ! $x->exists;
        $x->save();

        if ($isNew && $x->published && $this->notifier) {
            $users = User::role('etudiant')->whereHas('student', fn ($s) => $s->where('status', 'actif')
                ->when($x->formation_id, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $x->formation_id))))->get();
            $users->each->notify(new Avis('Nouvelle ressource', '« '.$x->title.' » est disponible dans votre bibliothèque.', route('etudiant.bibliotheque'), 'fa-book-bookmark', 'lav'));
        }
        AuditLog::record($isNew ? 'Ressource ajoutée' : 'Ressource modifiée', 'Bibliothèque — '.$x->title);
        $this->form = false;
        $this->toast($isNew ? ($x->published ? '« '.$x->title.' » est publiée.' : 'Ressource enregistrée en brouillon.') : 'Ressource mise à jour.');
    }

    public function publier(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        $x->update(['published' => ! $x->published]);
        AuditLog::record($x->published ? 'Ressource publiée' : 'Ressource masquée', 'Bibliothèque — '.$x->title);
        $this->toast($x->published ? 'Ressource publiée.' : 'Ressource repassée en brouillon.', $x->published ? 'ok' : 'info');
    }

    public function supprimer(int $id): void
    {
        $x = $this->query()->findOrFail($id);
        if ($x->path) {
            Storage::disk('local')->delete($x->path);
        }
        $x->delete();
        AuditLog::record('Ressource supprimée', 'Bibliothèque — '.$x->title);
        $this->toast('Ressource supprimée.');
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        $base = $this->query();
        $all = (clone $base)->get();
        $items = (clone $base)
            ->when($this->q, fn ($q) => $q->where('title', 'like', "%{$this->q}%"))
            ->when($this->formation !== '', fn ($q) => $this->formation === 'ALL' ? $q->whereNull('formation_id') : $q->where('formation_id', $this->formation))
            ->when($this->rayon, fn ($q) => $q->where('category', $this->rayon))
            ->when($this->statut, fn ($q) => $q->where('published', $this->statut === 'pub'))
            ->latest()->paginate(20);
        $formations = Formation::active()->when($this->scope(), fn ($q, $ids) => $q->whereIn('id', $ids))->get();
        $trainer = auth()->user()->space() === 'formateur';

        return $this->page('livewire.admin.bibliotheque', compact('all', 'items', 'formations', 'trainer'),
            $trainer ? 'Bibliothèque de ma formation' : 'Bibliothèque des étudiants',
            ($trainer ? 'Espace formateur' : 'Administration <span class="sep">›</span> Contenus').' <span class="sep">›</span> Bibliothèque',
            '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-plus"></i>Ajouter une ressource</button>');
    }
}
