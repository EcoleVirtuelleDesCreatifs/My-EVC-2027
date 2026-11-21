<?php

namespace App\Livewire\Formateur;

use App\Livewire\Page;
use App\Models\Seance;
use Livewire\Attributes\Url;

class Seances extends Page
{
    #[Url(except: 'avenir')]
    public string $vue = 'avenir';

    public function render()
    {
        $fids = auth()->user()->taughtFormationIds();
        $q = Seance::with('formation')->withCount('presences')->whereIn('formation_id', $fids);
        $seances = $this->vue === 'avenir'
            ? $q->where('ends_at', '>=', now())->orderBy('starts_at')->take(40)->get()
            : $q->where('ends_at', '<', now())->orderByDesc('starts_at')->take(60)->get();

        return $this->page('livewire.formateur.seances', compact('seances'), 'Séances et appel', 'Espace formateur <span class="sep">›</span> Séances');
    }
}
