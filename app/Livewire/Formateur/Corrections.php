<?php

namespace App\Livewire\Formateur;

use App\Livewire\Concerns\CorrigeProjets;
use App\Livewire\Page;
use App\Models\Assignment;
use Livewire\Attributes\Url;

class Corrections extends Page
{
    use CorrigeProjets;

    #[Url(except: 'soumis')]
    public string $etat = 'soumis';

    public function render()
    {
        $fids = auth()->user()->taughtFormationIds();
        $base = Assignment::with('project.formation', 'student.user')->whereHas('project', fn ($q) => $q->whereIn('formation_id', $fids));
        $counts = ['soumis' => (clone $base)->where('status', 'soumis')->count(), 'reprendre' => (clone $base)->where('status', 'reprendre')->count(), 'valide' => (clone $base)->where('status', 'valide')->count()];
        $items = (clone $base)->where('status', $this->etat)->orderBy($this->etat === 'soumis' ? 'submitted_at' : 'updated_at', $this->etat === 'soumis' ? 'asc' : 'desc')->take(80)->get();

        return $this->page('livewire.formateur.corrections', compact('items', 'counts'), 'Corrections', 'Espace formateur <span class="sep">›</span> Corrections');
    }
}
