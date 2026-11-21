<?php

namespace App\Livewire\Formateur;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Presence;
use App\Models\Seance;

/** Appel d'une séance : présent, en retard ou absent pour chaque étudiant attendu. */
class Appel extends Page
{
    public Seance $seance;

    public array $statuts = [];

    public function mount(Seance $seance): void
    {
        $u = auth()->user();
        abort_unless(in_array($seance->formation_id, $u->taughtFormationIds()) || $u->can('admin.formations'), 403);
        $existing = $seance->presences()->pluck('status', 'student_id');
        foreach ($seance->expectedStudents()->get() as $s) {
            $this->statuts[$s->id] = $existing[$s->id] ?? '';
        }
    }

    public function tousPresents(): void
    {
        foreach ($this->statuts as $id => $v) {
            $this->statuts[$id] = $v ?: 'present';
        }
    }

    public function enregistrer(): void
    {
        $this->validate(['statuts.*' => 'nullable|in:present,retard,absent']);
        $n = 0;
        foreach ($this->statuts as $id => $st) {
            if (! $st) {
                continue;
            }
            Presence::updateOrCreate(['seance_id' => $this->seance->id, 'student_id' => $id], ['status' => $st, 'checked_at' => $st === 'absent' ? null : now()]);
            $n++;
        }
        AuditLog::record('Appel enregistré', $this->seance->formation->code.' — '.$this->seance->title.' ('.$n.')');
        $this->toast('Appel enregistré pour '.$n.' étudiant'.($n > 1 ? 's' : '').'.');
    }

    public function render()
    {
        $students = $this->seance->expectedStudents()->get()->sortBy(fn ($s) => $s->user->name);
        $counts = collect($this->statuts)->countBy();

        return $this->page('livewire.formateur.appel', ['students' => $students, 'counts' => $counts, 'x' => $this->seance->load('formation')],
            'Appel : '.$this->seance->title, 'Espace formateur <span class="sep">›</span> <a href="'.route('formateur.seances').'" wire:navigate>Séances</a>');
    }
}
