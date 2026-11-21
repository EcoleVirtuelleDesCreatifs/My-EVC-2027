<?php

namespace App\Livewire;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/** Base des écrans MY EVC : titre, fil d'Ariane et bouton d'action dans la barre du haut, notifications « toast ». */
abstract class Page extends Component
{
    protected function page(string $view, array $data, string $title, ?string $crumb = null, ?string $cta = null): View
    {
        return view($view, $data)->layoutData(['title' => $title, 'crumb' => $crumb, 'cta' => $cta]);
    }

    protected function toast(string $message, string $kind = 'ok'): void
    {
        $this->dispatch('toast', message: $message, kind: $kind);
    }

    protected function student(): Student
    {
        $s = auth()->user()->student;
        abort_unless($s, 403);

        return $s->loadMissing('formations', 'user');
    }
}
