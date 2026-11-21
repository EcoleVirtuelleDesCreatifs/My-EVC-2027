<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;

class Certificat extends Page
{
    public function render()
    {
        $s = $this->student()->load('certificates');
        $rows = $s->formations->map(fn ($f) => ['f' => $f, 'checks' => $s->certificationChecks($f), 'cert' => $s->certificates->firstWhere('formation_id', $f->id)]);

        return $this->page('livewire.etudiant.certificat', compact('s', 'rows'), 'Certificat', 'Espace étudiant <span class="sep">›</span> Certificat');
    }
}
