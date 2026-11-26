<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\StarterTool;
use App\Models\VirtualClass;

/** Outils de démarrage : logiciels, supports et programmes à télécharger, et classes virtuelles. */
class Demarrage extends Page
{
    public function render()
    {
        $s = $this->student()->load('promotion');

        return $this->page('livewire.etudiant.demarrage', [
            's' => $s,
            'tools' => StarterTool::visibleFor($s)->with('formation')->get()->groupBy('kind'),
            'classes' => VirtualClass::visibleFor($s)->with('formation', 'promotion')->get(),
        ], 'Outils de démarrage', 'Espace étudiant <span class="sep">›</span> Outils de démarrage');
    }
}
