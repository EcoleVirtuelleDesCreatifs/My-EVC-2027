<?php

namespace App\Livewire\Admin;

use App\Models\Formation;
use App\Models\Resource;

class RessourceEditeur extends Bibliotheque
{
    private function authorizeEditor(): void
    {
        abort_unless(auth()->user()->can('admin.bibliotheque') || (auth()->user()->space() === 'formateur' && $this->scope()), 403);
    }

    public function mount(?Resource $resource = null): void
    {
        $this->authorizeEditor();
        if ($resource?->exists) {
            parent::modifier($resource->id);
        } else {
            parent::creer();
        }
    }

    public function enregistrer(): void
    {
        $this->authorizeEditor();
        parent::enregistrer();
        session()->flash('status', 'Ressource enregistrée.');
        $this->redirectRoute(auth()->user()->space() === 'formateur' ? 'formateur.bibliotheque' : 'admin.bibliotheque', navigate: true);
    }

    public function render()
    {
        $trainer = auth()->user()->space() === 'formateur';
        $formations = Formation::active()->when($this->scope() !== null, fn ($q) => $q->whereIn('id', $this->scope()))->get();
        $existing = $this->editId ? $this->query()->findOrFail($this->editId) : null;
        $back = route($trainer ? 'formateur.bibliotheque' : 'admin.bibliotheque');
        $uploadLimit = min(50 * 1048576, ini_parse_quantity(ini_get('upload_max_filesize')) ?: PHP_INT_MAX, ini_parse_quantity(ini_get('post_max_size')) ?: PHP_INT_MAX);

        return $this->page('livewire.admin.ressource-editeur', compact('formations', 'trainer', 'existing', 'back', 'uploadLimit'), $this->editId ? 'Modifier la ressource' : 'Nouvelle ressource', 'Bibliothèque <span class="sep">›</span> Éditeur');
    }
}
