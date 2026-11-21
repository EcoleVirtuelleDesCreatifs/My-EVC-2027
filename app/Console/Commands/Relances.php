<?php

namespace App\Console\Commands;

use App\Models\PaymentSchedule;
use App\Models\Seance;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Console\Command;

/**
 * Relances automatiques (planifiées chaque jour, voir routes/console.php) :
 *   - échéances de paiement dans 3 jours, ou en retard et non relancées depuis 7 jours ;
 *   - rappel des séances du lendemain.
 */
class Relances extends Command
{
    protected $signature = 'evc:relances {--dry-run : Afficher sans envoyer}';

    protected $description = 'Envoyer les rappels de paiement et de séances';

    public function handle(): int
    {
        $dry = $this->option('dry-run');
        $paiements = 0;
        $schedules = PaymentSchedule::with('student.user', 'student.schedules', 'student.payments', 'formation')
            ->whereHas('student', fn ($q) => $q->where('status', 'actif'))
            ->where('due_on', '<=', now()->addDays(3)->toDateString())
            ->where(fn ($q) => $q->whereNull('reminded_at')->orWhere('reminded_at', '<', now()->subDays(7)))->get();
        foreach ($schedules as $s) {
            $s->setRelation('student', $s->student);
            $reste = $s->remaining();
            if ($reste <= 0) {
                continue;
            }
            $late = $s->due_on->endOfDay()->isPast();
            $msg = ($late ? 'Échéance dépassée : ' : 'Échéance le '.$s->due_on->translatedFormat('j F').' : ').fcfa($reste).' à régler ('.$s->label.').';
            $this->line(($dry ? '[test] ' : '').$s->student->user->name.' — '.$msg);
            if (! $dry) {
                $s->student->user->notify(new Avis($late ? 'Paiement en retard' : 'Paiement à venir', $msg, route('etudiant.paiements'), 'fa-wallet', $late ? 'bad' : 'warn', true));
                $s->update(['reminded_at' => now()]);
            }
            $paiements++;
        }

        $cours = 0;
        foreach (Seance::with('formation')->where('cancelled', false)->whereBetween('starts_at', [now()->addDay()->startOfDay(), now()->addDay()->endOfDay()])->get() as $se) {
            $users = User::whereIn('id', $se->expectedStudents()->pluck('user_id'))->get();
            if (! $dry) {
                $users->each->notify(new Avis('Cours demain', $se->title.' — '.$se->hours().' · '.$se->modeLabel().'.', route('etudiant.cours'), 'fa-calendar-days', 'info'));
            }
            $cours += $users->count();
        }
        $this->info("$paiements relance(s) de paiement, $cours rappel(s) de cours".($dry ? ' (test, rien envoyé)' : '').'.');

        return self::SUCCESS;
    }
}
