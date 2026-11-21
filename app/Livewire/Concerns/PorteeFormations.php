<?php

namespace App\Livewire\Concerns;

use App\Models\Formation;

/** Périmètre des écrans de gestion : un formateur voit ses formations, l'administration voit tout. */
trait PorteeFormations
{
    protected function estFormateur(): bool
    {
        return auth()->user()->space() === 'formateur';
    }

    /** null = toutes les formations. */
    protected function formationsGerees(): ?array
    {
        return $this->estFormateur() ? auth()->user()->taughtFormationIds() : null;
    }

    protected function listeFormations()
    {
        return Formation::active()->when($this->formationsGerees(), fn ($q, $ids) => $q->whereIn('id', $ids))->get();
    }

    protected function verifierFormation(int $formationId): void
    {
        $ids = $this->formationsGerees();
        abort_if($ids !== null && ! in_array($formationId, $ids, true), 403);
    }

    protected function filAriane(string $module): string
    {
        return ($this->estFormateur() ? 'Espace formateur' : 'Administration <span class="sep">›</span> Programmes').' <span class="sep">›</span> '.$module;
    }
}
