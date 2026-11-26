<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Carbon;

/** Caisse de l'école : encaissements (versements), dépenses et soldes, au total, par période et par moyen de paiement. */
class CashService
{
    /** Synthèse globale et pour une période [début, fin]. */
    public function summary(Carbon $from, Carbon $to): array
    {
        $in = (int) Payment::sum('amount');
        $out = (int) Expense::sum('amount');
        $periodIn = (int) Payment::whereBetween('paid_on', [$from->toDateString(), $to->toDateString()])->sum('amount');
        $periodOut = (int) Expense::whereBetween('spent_on', [$from->toDateString(), $to->toDateString()])->sum('amount');
        /* Reste à encaisser : somme des soldes dus par étudiant (hors abandons), sans compenser un trop-perçu par un impayé */
        $due = (int) Student::where('status', '!=', 'abandon')->with('schedules', 'payments')->get()->sum(fn (Student $s) => $s->balance());

        return ['in' => $in, 'out' => $out, 'balance' => $in - $out, 'periodIn' => $periodIn, 'periodOut' => $periodOut,
            'periodResult' => $periodIn - $periodOut, 'due' => $due];
    }

    /** Solde par caisse (moyen de paiement) : entrées, sorties, solde ; et mouvements de la période. */
    public function byMethod(Carbon $from, Carbon $to): array
    {
        $range = [$from->toDateString(), $to->toDateString()];
        $in = Payment::selectRaw('method, SUM(amount) as total')->groupBy('method')->pluck('total', 'method');
        $out = Expense::selectRaw('method, SUM(amount) as total')->groupBy('method')->pluck('total', 'method');
        $pin = Payment::whereBetween('paid_on', $range)->selectRaw('method, SUM(amount) as total')->groupBy('method')->pluck('total', 'method');
        $pout = Expense::whereBetween('spent_on', $range)->selectRaw('method, SUM(amount) as total')->groupBy('method')->pluck('total', 'method');

        return collect(Payment::METHODS)->merge($in->keys())->merge($out->keys())->unique()->values()
            ->map(fn ($m) => ['method' => $m, 'in' => (int) ($in[$m] ?? 0), 'out' => (int) ($out[$m] ?? 0), 'balance' => (int) ($in[$m] ?? 0) - (int) ($out[$m] ?? 0),
                'periodIn' => (int) ($pin[$m] ?? 0), 'periodOut' => (int) ($pout[$m] ?? 0)])
            ->filter(fn ($r) => $r['in'] || $r['out'] || in_array($r['method'], Payment::METHODS, true))->values()->all();
    }

    /** Évolution sur les N derniers mois : encaissements, dépenses, résultat. */
    public function months(int $count = 6): array
    {
        return collect(range($count - 1, 0))->map(function ($i) {
            $m = now()->startOfMonth()->subMonths($i);
            $range = [$m->toDateString(), $m->copy()->endOfMonth()->toDateString()];
            $in = (int) Payment::whereBetween('paid_on', $range)->sum('amount');
            $out = (int) Expense::whereBetween('spent_on', $range)->sum('amount');

            return ['label' => ucfirst($m->translatedFormat('M Y')), 'key' => $m->format('Y-m'), 'in' => $in, 'out' => $out, 'result' => $in - $out];
        })->all();
    }

    /** Dépenses de la période par catégorie, de la plus élevée à la plus faible. */
    public function byCategory(Carbon $from, Carbon $to): array
    {
        return Expense::whereBetween('spent_on', [$from->toDateString(), $to->toDateString()])
            ->selectRaw('category, SUM(amount) as total, COUNT(*) as n')->groupBy('category')->orderByDesc('total')->get()
            ->map(fn ($r) => ['category' => $r->category, 'total' => (int) $r->total, 'n' => (int) $r->n])->all();
    }
}
