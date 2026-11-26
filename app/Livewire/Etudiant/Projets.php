<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use Livewire\Attributes\Url;

class Projets extends Page
{
    use ChoixFormation;

    #[Url(except: 'afaire')]
    public string $etat = 'afaire';

    public function render()
    {
        $s = $this->student();
        $base = $s->assignments()->with('project.formation')->whereHas('project', fn ($q) => $q->whereIn('formation_id', $this->formationIds()));
        $groups = ['afaire' => ['afaire', 'encours', 'reprendre'], 'rendus' => ['soumis'], 'valides' => ['valide'], 'tous' => null];
        $counts = collect($groups)->map(fn ($st) => (clone $base)->when($st, fn ($q) => $q->whereIn('status', $st))->count());
        $items = (clone $base)->when($groups[$this->etat] ?? null, fn ($q, $st) => $q->whereIn('status', $st))
            ->orderByRaw("case status when 'reprendre' then 0 when 'encours' then 1 when 'afaire' then 2 when 'soumis' then 3 else 4 end")->orderBy('due_on')->get();

        return $this->page('livewire.etudiant.projets', compact('s', 'items', 'counts') + ['classes' => \App\Models\VirtualClass::visibleFor($s)->with('formation', 'promotion')->get()], 'Mes projets', 'Espace étudiant <span class="sep">›</span> Mes projets');
    }
}
