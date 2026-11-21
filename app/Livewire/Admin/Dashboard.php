<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\Assignment;
use App\Models\Candidature;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Seance;
use App\Models\Student;
use App\Services\AvailabilityService;

class Dashboard extends Page
{
    public function render(AvailabilityService $dispo)
    {
        $u = auth()->user();
        $actifs = Student::where('status', 'actif')->count();
        $aTraiter = Candidature::whereIn('stage', ['recue', 'paiement'])->count();
        $moisEncaisse = Payment::whereBetween('paid_on', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');
        $totalDu = PaymentSchedule::whereHas('student', fn ($q) => $q->where('status', '!=', 'abandon'))->sum('amount');
        $totalPaye = Payment::sum('amount');
        $aValider = Assignment::where('status', 'soumis')->count();
        $etats = $u->can('admin.projets') ? $dispo->summary() : null;
        $candidatures = Candidature::latest()->take(6)->get();
        $retards = $u->can('admin.finances') ? Student::with('user', 'schedules', 'payments')->where('status', 'actif')->get()
            ->map(fn ($s) => ['s' => $s, 'late' => $s->scheduleRows()->filter(fn ($x) => $x->remaining() > 0 && $x->due_on->endOfDay()->isPast())->sum(fn ($x) => $x->remaining())])
            ->filter(fn ($r) => $r['late'] > 0)->sortByDesc('late')->take(5) : collect();
        $today = Seance::with('formation', 'trainer')->whereDate('starts_at', today())->orderBy('starts_at')->get();

        return $this->page('livewire.admin.dashboard', compact('actifs', 'aTraiter', 'moisEncaisse', 'totalDu', 'totalPaye', 'aValider', 'etats', 'candidatures', 'retards', 'today'),
            'Tableau de bord', 'Administration · '.ucfirst(now()->translatedFormat('l j F Y')));
    }
}
