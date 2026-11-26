<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Support\Portfolio as Catalogue;

/** Mon portfolio : catalogue des projets validés, avec couverture et export PDF. */
class Portfolio extends Page
{
    use ChoixFormation;

    public function render()
    {
        $s = $this->student();
        $items = Catalogue::items($s, $this->formationIds());

        return $this->page('livewire.etudiant.portfolio', ['s' => $s, 'items' => $items, 'stats' => Catalogue::stats($items)],
            'Mon portfolio', 'Espace étudiant <span class="sep">›</span> Mon portfolio');
    }
}
