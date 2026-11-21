<?php

namespace App\Livewire\Gestion;

use App\Livewire\Concerns\PorteeFormations;
use App\Livewire\Page;
use App\Models\RecoProject;
use App\Models\Student;
use Livewire\Attributes\Url;

class Reco extends Page
{
    use PorteeFormations;

    #[Url(except: '')]
    public string $statut = '';

    #[Url(except: '')]
    public string $formation = '';

    public function render()
    {
        $fids = $this->formationsGerees();
        $formations = $this->listeFormations()->where('has_reco', true)->values();
        $base = RecoProject::with('student.user', 'formation')->withCount('entries')->withMax('entries', 'entry_date')
            ->when($fids, fn ($q, $ids) => $q->whereIn('formation_id', $ids))
            ->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation));
        $all = (clone $base)->get();
        $items = $all->when($this->statut, fn ($c) => $c->where('status', $this->statut))->sortBy(fn ($p) => [['rendu' => 0, 'en_cours' => 1, 'evalue' => 2][$p->status], -$p->day()]);
        $sansProjet = Student::with('user', 'formations')->where('status', 'actif')
            ->whereHas('enrollments', fn ($q) => $q->whereIn('formation_id', $formations->pluck('id')))->get()
            ->filter(fn ($s) => $s->formations->whereIn('id', $formations->pluck('id'))->contains(fn ($f) => ! $all->where('student_id', $s->id)->where('formation_id', $f->id)->count()));
        $inactifs = $all->where('status', 'en_cours')->filter(fn ($p) => ! $p->entries_max_entry_date || \Illuminate\Support\Carbon::parse($p->entries_max_entry_date)->lt(now()->subDays(7)))->count();

        return $this->page('livewire.gestion.reco', compact('items', 'all', 'formations', 'sansProjet', 'inactifs'), 'RECO 60', $this->filAriane('RECO 60'));
    }
}
