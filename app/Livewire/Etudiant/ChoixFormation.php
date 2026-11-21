<?php

namespace App\Livewire\Etudiant;

use Livewire\Attributes\Url;

/** Sélecteur « Toutes / DG / CM / GIA » des étudiants inscrits à plusieurs formations (mémorisé dans l'adresse). */
trait ChoixFormation
{
    #[Url(as: 'formation', except: '')]
    public string $f = '';

    public function choisirFormation(string $code): void
    {
        $this->f = $code;
        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    /** Identifiants des formations affichées. */
    protected function formationIds(): array
    {
        $fs = $this->student()->formations;
        $sel = $fs->firstWhere('code', $this->f);

        return $sel ? [$sel->id] : $fs->pluck('id')->all();
    }
}
