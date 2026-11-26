<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\Formation;
use App\Models\Student;
use App\Support\Portfolio;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

/** Galerie des portfolios : tous les étudiants et leurs projets validés. */
class Portfolios extends Page
{
    use WithPagination;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $formation = '';

    #[Url(except: 'recents')]
    public string $tri = 'recents';

    #[Url(except: true)]
    public bool $avecProjets = true;

    public function updated($prop): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $valid = fn ($q) => $q->where('status', 'valide');
        $base = Student::query()
            ->when($this->formation, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $this->formation)))
            ->when(trim($this->q), fn ($q, $t) => $q->where(fn ($w) => $w->where('matricule', 'like', "%{$t}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$t}%"))));
        $items = (clone $base)->with(['user', 'formations', 'promotion'])
            ->withCount(['assignments as valides_count' => $valid])->withMax(['assignments as last_validated_at' => $valid], 'validated_at')
            ->when($this->avecProjets, fn ($q) => $q->whereHas('assignments', $valid))
            ->when($this->tri === 'projets', fn ($q) => $q->orderByDesc('valides_count'))
            ->when($this->tri === 'nom', fn ($q) => $q->orderBy(\App\Models\User::select('name')->whereColumn('users.id', 'students.user_id')))
            ->when($this->tri === 'recents', fn ($q) => $q->orderByDesc('last_validated_at'))
            ->orderBy('id')->paginate(12);
        $items->getCollection()->each(function (Student $s) {
            $works = Portfolio::items($s);
            $s->setAttribute('pf', Portfolio::stats($works));
            $s->setAttribute('pfVisuals', $works->flatMap(fn ($a) => $a->visuals->take(1))->take(3)->values());
            $s->setAttribute('pfLast', $works->first());
        });
        $totals = ['portfolios' => (clone $base)->whereHas('assignments', $valid)->count(), 'projets' => \App\Models\Assignment::where('status', 'valide')
            ->whereIn('student_id', (clone $base)->select('id'))->count()];

        return $this->page('livewire.admin.portfolios', ['items' => $items, 'totals' => $totals, 'formations' => Formation::active()->get()],
            'Portfolios', 'Administration <span class="sep">›</span> Portfolios');
    }
}
