<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\News;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class Actualites extends Page
{
    use WithFileUploads;

    public bool $form = false;

    public ?int $editId = null;

    public array $n = [];

    public $couverture;

    public function creer(): void
    {
        $this->n = ['title' => '', 'category' => 'Vie de l\'école', 'label' => '', 'excerpt' => '', 'body' => '', 'featured' => false, 'publie' => true, 'date' => now()->toDateString()];
        $this->reset('couverture', 'editId');
        $this->form = true;
    }

    public function modifier(int $id): void
    {
        $x = News::findOrFail($id);
        $this->n = $x->only(['title', 'category', 'label', 'excerpt', 'body', 'featured']) + ['publie' => (bool) $x->published_at, 'date' => ($x->published_at ?? now())->toDateString()];
        $this->n['label'] ??= '';
        $this->editId = $id;
        $this->reset('couverture');
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $this->validate([
            'n.title' => 'required|string|max:180', 'n.category' => 'required|string|max:60', 'n.label' => 'nullable|string|max:60', 'n.excerpt' => 'required|string|max:400',
            'n.body' => 'required|string', 'n.date' => 'required|date', 'couverture' => ($this->editId ? 'nullable' : 'required').'|image|max:5120',
        ], [], ['n.title' => 'titre', 'n.excerpt' => 'chapeau', 'n.body' => 'texte', 'couverture' => 'image de couverture']);
        $x = $this->editId ? News::findOrFail($this->editId) : new News(['slug' => $this->slug($this->n['title'])]);
        $body = trim($this->n['body']);
        if (! str_contains($body, '<')) {
            $body = collect(preg_split('/\n\s*\n/', $body))->map(fn ($p) => '<p>'.nl2br(e(trim($p))).'</p>')->implode("\n");
        }
        $x->fill(['title' => $this->n['title'], 'category' => $this->n['category'], 'label' => $this->n['label'] ?: null, 'excerpt' => $this->n['excerpt'],
            'body' => $body, 'featured' => (bool) $this->n['featured'], 'published_at' => $this->n['publie'] ? $this->n['date'].' '.now()->format('H:i:s') : null]);
        if ($this->couverture) {
            $x->cover = '/storage/'.$this->couverture->store('actualites', 'public');
        }
        if ($x->featured) {
            News::where('id', '!=', $x->id ?? 0)->update(['featured' => false]);
        }
        $x->save();
        AuditLog::record($this->editId ? 'Actualité modifiée' : 'Actualité publiée', $x->title);
        $this->form = false;
        $this->toast('Actualité enregistrée.');
    }

    public function supprimer(int $id): void
    {
        $x = News::findOrFail($id);
        $x->delete();
        AuditLog::record('Actualité supprimée', $x->title);
        $this->toast('Actualité supprimée.');
    }

    private function slug(string $title): string
    {
        $base = Str::slug($title) ?: 'actualite';
        $slug = $base;
        for ($i = 2; News::where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        return $this->page('livewire.admin.actualites', ['items' => News::orderByDesc('published_at')->orderByDesc('id')->get()], 'Actualités', 'Administration <span class="sep">›</span> Contenus <span class="sep">›</span> Actualités',
            '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouvelle actualité</button>');
    }
}
