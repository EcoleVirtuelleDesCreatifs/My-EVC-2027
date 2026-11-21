<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Seance;
use App\Services\PresenceService;
use Livewire\Attributes\Url;

class Cours extends Page
{
    use ChoixFormation;

    #[Url(except: 'avenir')]
    public string $vue = 'avenir';

    public function pointer(int $id, PresenceService $presences): void
    {
        $seance = Seance::findOrFail($id);
        $p = $presences->checkIn($this->student(), $seance);
        if ($seance->mode === 'virtuel' && $seance->meet_url) {
            $this->dispatch('open-url', url: $seance->meet_url);
        }
        $this->toast($p->status === 'retard' ? 'Présence enregistrée (en retard).' : 'Présence enregistrée. Bon cours !', $p->status === 'retard' ? 'warn' : 'ok');
    }

    public function render()
    {
        $s = $this->student();
        $q = $s->seancesQuery($this->formationIds())->with('formation', 'trainer');
        $seances = $this->vue === 'avenir'
            ? $q->where('ends_at', '>=', now())->orderBy('starts_at')->take(40)->get()
            : $q->where('ends_at', '<', now())->orderByDesc('starts_at')->take(60)->get();
        $presences = $s->presences()->whereIn('seance_id', $seances->pluck('id'))->get()->keyBy('seance_id');
        $days = $seances->groupBy(fn ($x) => $x->starts_at->toDateString());

        return $this->page('livewire.etudiant.cours', compact('s', 'days', 'presences'), 'Mes cours', 'Espace étudiant <span class="sep">›</span> Mes cours');
    }
}
