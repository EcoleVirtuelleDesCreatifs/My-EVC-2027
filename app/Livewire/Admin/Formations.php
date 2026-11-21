<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Promotion;

class Formations extends Page
{
    public ?int $editF = null;

    public array $f = [];

    public ?int $editP = null;

    public bool $newP = false;

    public array $p = [];

    public function modifierFormation(int $id): void
    {
        $x = Formation::findOrFail($id);
        $this->f = $x->only(['name', 'short_name', 'pitch', 'price', 'deposit', 'months', 'balance_after_days', 'pay_rule', 'active']);
        $this->editF = $id;
    }

    public function enregistrerFormation(): void
    {
        $this->validate([
            'f.name' => 'required|string|max:120', 'f.short_name' => 'required|string|max:60', 'f.pitch' => 'nullable|string|max:600',
            'f.price' => 'required|integer|min:0', 'f.deposit' => 'required|integer|min:0|lte:f.price', 'f.months' => 'required|integer|min:1|max:24',
            'f.balance_after_days' => 'required|integer|min:0|max:365', 'f.pay_rule' => 'nullable|string|max:255', 'f.active' => 'boolean',
        ], [], ['f.deposit' => 'acompte', 'f.price' => 'prix']);
        $x = Formation::findOrFail($this->editF);
        $x->update($this->f);
        AuditLog::record('Formation modifiée', $x->code);
        $this->editF = null;
        $this->toast('Formation mise à jour. Les nouveaux échéanciers utiliseront ces montants.');
    }

    public function nouvelleSession(): void
    {
        $this->p = ['name' => 'Session '.ucfirst(now()->addMonth()->translatedFormat('F Y')), 'starts_on' => now()->addMonth()->startOfMonth()->toDateString(), 'ends_on' => now()->addMonths(5)->startOfMonth()->toDateString(), 'status' => 'ouverte'];
        $this->editP = null;
        $this->newP = true;
    }

    public function modifierSession(int $id): void
    {
        $x = Promotion::findOrFail($id);
        $this->p = ['name' => $x->name, 'starts_on' => $x->starts_on->toDateString(), 'ends_on' => $x->ends_on->toDateString(), 'status' => $x->status];
        $this->editP = $id;
        $this->newP = true;
    }

    public function enregistrerSession(): void
    {
        $this->validate(['p.name' => 'required|string|max:80', 'p.starts_on' => 'required|date', 'p.ends_on' => 'required|date|after:p.starts_on', 'p.status' => 'required|in:'.implode(',', array_keys(Promotion::STATUSES))],
            [], ['p.ends_on' => 'date de fin', 'p.starts_on' => 'date de début']);
        $x = Promotion::updateOrCreate(['id' => $this->editP], $this->p);
        AuditLog::record($this->editP ? 'Session modifiée' : 'Session créée', $x->name);
        $this->newP = false;
        $this->toast('Session enregistrée.');
    }

    public function closeModal(): void
    {
        $this->editF = null;
        $this->newP = false;
    }

    public function render()
    {
        return $this->page('livewire.admin.formations', [
            'formations' => Formation::withCount(['enrollments' => fn ($q) => $q->where('status', 'en_cours')])->orderBy('position')->get(),
            'promotions' => Promotion::withCount('students')->orderByDesc('starts_on')->get(),
        ], 'Formations et sessions', 'Administration <span class="sep">›</span> Scolarité');
    }
}
