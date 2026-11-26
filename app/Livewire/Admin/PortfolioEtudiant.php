<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\Student;
use App\Support\Portfolio;

/** Portfolio d'un étudiant, vu par l'administration (même présentation que dans l'espace étudiant). */
class PortfolioEtudiant extends Page
{
    public Student $student;

    public function render()
    {
        $s = $this->student->load('user', 'formations', 'promotion');
        $items = Portfolio::items($s);

        return $this->page('livewire.admin.portfolio', ['s' => $s, 'items' => $items, 'stats' => Portfolio::stats($items), 'admin' => true],
            'Portfolio · '.$s->user->name, 'Administration <span class="sep">›</span> <a href="'.route('admin.portfolios').'" wire:navigate>Portfolios</a> <span class="sep">›</span> '.$s->matricule);
    }
}
