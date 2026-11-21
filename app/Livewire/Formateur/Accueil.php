<?php

namespace App\Livewire\Formateur;

use App\Livewire\Page;
use App\Models\Assignment;
use App\Models\Formation;
use App\Models\Presence;
use App\Models\Seance;

class Accueil extends Page
{
    public function render()
    {
        $u = auth()->user();
        $fids = $u->taughtFormationIds();
        $formations = Formation::whereIn('id', $fids)->get();
        $week = Seance::whereIn('formation_id', $fids)->whereBetween('starts_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $next = Seance::with('formation')->whereIn('formation_id', $fids)->where('ends_at', '>=', now())->where('cancelled', false)->orderBy('starts_at')->take(5)->get();
        $queue = Assignment::with('project.formation', 'student.user')->where('status', 'soumis')->whereHas('project', fn ($q) => $q->whereIn('formation_id', $fids))->orderBy('submitted_at')->take(6)->get();
        $pending = Assignment::where('status', 'soumis')->whereHas('project', fn ($q) => $q->whereIn('formation_id', $fids))->count();
        $students = \App\Models\Student::whereHas('enrollments', fn ($q) => $q->whereIn('formation_id', $fids))->where('status', 'actif')->count();
        $pastIds = Seance::whereIn('formation_id', $fids)->where('ends_at', '<', now())->pluck('id');
        $rate = Presence::whereIn('seance_id', $pastIds)->count() ? round(Presence::whereIn('seance_id', $pastIds)->whereIn('status', ['present', 'retard'])->count() / Presence::whereIn('seance_id', $pastIds)->count() * 100) : 100;
        $toCall = Seance::whereIn('id', $pastIds)->where('starts_at', '>=', now()->subDays(14))->whereDoesntHave('presences')->count();

        return $this->page('livewire.formateur.accueil', compact('u', 'formations', 'week', 'next', 'queue', 'pending', 'students', 'rate', 'toCall'),
            'Bonjour '.explode(' ', $u->name)[0], 'Espace formateur · '.ucfirst(now()->translatedFormat('l j F Y')));
    }
}
