<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\AttribueProjets;
use App\Livewire\Page;
use App\Models\Formation;
use App\Services\AvailabilityService;
use Livewire\Attributes\Url;

class Disponibilite extends Page
{
    use AttribueProjets;

    #[Url(except: 'tous')]
    public string $etat = 'tous';

    #[Url(except: '')]
    public string $formation = '';

    #[Url(except: '')]
    public string $q = '';

    public array $selection = [];

    public function closeModal(): void
    {
        $this->attribution = false;
    }

    public function donnerAuxLibres(AvailabilityService $svc): void
    {
        $ids = $this->filtrer($svc->rows($this->formation ? (int) $this->formation : null))->where('etat', 'libre')->pluck('s.id')->all();
        $this->ouvrirAttribution($ids);
    }

    private function filtrer($rows)
    {
        $q = mb_strtolower(trim($this->q));

        return $rows->when($q !== '', fn ($c) => $c->filter(fn ($r) => str_contains(mb_strtolower($r['s']->user->name.' '.$r['s']->matricule), $q)));
    }

    public function render(AvailabilityService $svc)
    {
        $all = $this->filtrer($svc->rows($this->formation ? (int) $this->formation : null));
        $n = ['libre' => $all->where('etat', 'libre')->count(), 'recu' => $all->where('etat', 'recu')->count(), 'encours' => $all->where('etat', 'encours')->count(), 'retard' => $all->where('late', '>', 0)->count()];
        $rows = match ($this->etat) {
            'retard' => $all->where('late', '>', 0), 'tous' => $all, default => $all->where('etat', $this->etat),
        };
        $rows = $rows->sortBy(fn ($r) => [['libre' => 0, 'recu' => 1, 'encours' => 2][$r['etat']], $r['s']->user->name]);
        $parFormation = Formation::active()->get()->map(fn ($f) => ['f' => $f, 'rows' => $svc->rows($f->id)]);

        return $this->page('livewire.admin.disponibilite', compact('all', 'n', 'rows', 'parFormation') + ['formations' => Formation::active()->get()],
            'Disponibilité des étudiants', 'Administration <span class="sep">›</span> Projets <span class="sep">›</span> Disponibilité',
            '<button class="btn btn-primary t-cta" wire:click="donnerAuxLibres"><i class="fa-solid fa-paper-plane"></i>Donner un projet aux libres</button>');
    }
}
