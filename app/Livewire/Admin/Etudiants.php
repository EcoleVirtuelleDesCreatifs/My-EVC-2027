<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\Formation;
use App\Models\Promotion;
use App\Models\Student;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Etudiants extends Page
{
    use WithPagination;

    #[Url(except: '')]
    public string $q = '';

    #[Url(except: '')]
    public string $formation = '';

    #[Url(except: '')]
    public string $promotion = '';

    #[Url(except: 'actif')]
    public string $statut = 'actif';

    public function updated($prop): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = Student::with(['user', 'formations', 'schedules', 'payments', 'promotion'])
            ->when($this->statut, fn ($q) => $q->where('status', $this->statut))
            ->when($this->formation, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $this->formation)))
            ->when($this->promotion, fn ($q) => $q->where('promotion_id', $this->promotion))
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('matricule', 'like', "%{$this->q}%")
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->q}%")->orWhere('email', 'like', "%{$this->q}%")->orWhere('phone', 'like', "%{$this->q}%"))))
            ->orderBy(\App\Models\User::select('name')->whereColumn('users.id', 'students.user_id'))
            ->paginate(20);

        return $this->page('livewire.admin.etudiants', ['items' => $items, 'formations' => Formation::active()->get(), 'promotions' => Promotion::orderByDesc('starts_on')->get()],
            'Étudiants', 'Administration <span class="sep">›</span> Étudiants');
    }
}
