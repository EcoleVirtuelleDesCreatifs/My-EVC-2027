<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\Candidature;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Candidatures extends Page
{
    use WithPagination;

    #[Url(except: 'actives')]
    public string $etape = 'actives';

    #[Url(except: '')]
    public string $q = '';

    public function updated($prop): void
    {
        if (in_array($prop, ['etape', 'q'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $counts = Candidature::selectRaw('stage, count(*) as n')->groupBy('stage')->pluck('n', 'stage');
        $items = Candidature::query()
            ->when($this->etape === 'actives', fn ($q) => $q->whereNotIn('stage', ['activee', 'refusee']))
            ->when(! in_array($this->etape, ['actives', 'toutes']), fn ($q) => $q->where('stage', $this->etape))
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('reference', 'like', "%{$this->q}%")->orWhere('first_name', 'like', "%{$this->q}%")
                ->orWhere('last_name', 'like', "%{$this->q}%")->orWhere('email', 'like', "%{$this->q}%")->orWhere('phone', 'like', "%{$this->q}%")))
            ->latest()->paginate(15);

        return $this->page('livewire.admin.candidatures', compact('items', 'counts'), 'Préinscriptions', 'Administration <span class="sep">›</span> Préinscriptions');
    }
}
