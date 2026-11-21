<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Assignment;
use App\Models\Seance;
use App\Services\PresenceService;

class Accueil extends Page
{
    public function rejoindre(int $seanceId, PresenceService $presences): void
    {
        $seance = Seance::findOrFail($seanceId);
        $presences->checkIn($this->student(), $seance);
        if ($seance->mode === 'virtuel' && $seance->meet_url) {
            $this->dispatch('open-url', url: $seance->meet_url);
        }
        $this->toast('Présence enregistrée. Bon cours !');
    }

    public function render()
    {
        $s = $this->student();
        $fids = $s->formations->pluck('id')->all();
        $next = $s->seancesQuery()->with('formation', 'trainer')->where('ends_at', '>=', now())->orderBy('starts_at')->take(3)->get();
        $open = $s->assignments()->with('project.formation')->whereIn('status', Assignment::OPEN)->orderBy('due_on')->get();
        $schedules = $s->scheduleRows();
        $nextPay = $s->nextSchedule();
        $checked = $s->presences()->whereIn('seance_id', $next->pluck('id'))->pluck('seance_id')->all();

        /* Une seule action principale, par ordre de priorité */
        $live = $next->first(fn (Seance $x) => $x->checkInOpen());
        $late = $open->first(fn (Assignment $a) => $a->isLate());
        $payLate = $schedules->first(fn ($x) => $x->remaining() > 0 && $x->due_on->endOfDay()->isPast());
        $fresh = $open->firstWhere('status', 'afaire');
        $paySoon = $nextPay && $nextPay->due_on->lte(now()->addDays(7)) ? $nextPay : null;
        $quiz = \App\Models\Quiz::visibleFor($fids)->with(['attempts' => fn ($q) => $q->where('student_id', $s->id)])->get()->first(fn ($q) => $q->stateFor($s)[2] && ! $q->stateFor($s)[3]);
        $action = match (true) {
            (bool) $live => ['tone' => 'live', 'k' => 'Aujourd\'hui', 't' => 'Cours en direct : '.$live->title, 's' => $live->hours().' · '.($live->mode === 'virtuel' ? 'en ligne' : 'en salle').' · '.$live->trainer?->name,
                'btn' => in_array($live->id, $checked) && $live->meet_url ? ['url' => $live->meet_url, 'l' => 'Rejoindre le cours', 'ic' => 'fa-video'] : ['wire' => 'rejoindre('.$live->id.')', 'l' => $live->mode === 'virtuel' ? 'Rejoindre le cours' : 'Je suis présent', 'ic' => $live->mode === 'virtuel' ? 'fa-video' : 'fa-check']],
            (bool) $late => ['tone' => 'bad', 'k' => 'En retard', 't' => $late->project->title, 's' => 'Ce projet était à rendre '.mb_strtolower($late->dueText()).'. Rendez-le dès que possible.', 'btn' => ['route' => route('etudiant.projet', $late), 'l' => 'Rendre mon projet', 'ic' => 'fa-upload']],
            (bool) $payLate => ['tone' => 'bad', 'k' => 'Paiement en retard', 't' => fcfa($payLate->remaining()).' à régler', 's' => 'Échéance du '.$payLate->due_on->translatedFormat('j F').' dépassée.', 'btn' => ['route' => route('etudiant.paiements'), 'l' => 'Payer maintenant', 'ic' => 'fa-money-bill-wave']],
            (bool) $fresh => ['tone' => 'new', 'k' => 'Nouveau projet', 't' => $fresh->project->title, 's' => 'À rendre '.mb_strtolower($fresh->dueText()).'. Lisez les consignes puis démarrez.', 'btn' => ['route' => route('etudiant.projet', $fresh), 'l' => 'Voir et démarrer', 'ic' => 'fa-play']],
            (bool) $quiz => ['tone' => 'new', 'k' => 'Quiz disponible', 't' => $quiz->title, 's' => $quiz->questions()->count().' questions · '.$quiz->duration_min.' min'.($quiz->closes_at ? ' · jusqu\'au '.$quiz->closes_at->translatedFormat('j F') : '').'.', 'btn' => ['route' => route('etudiant.quiz.passer', $quiz), 'l' => 'Passer le quiz', 'ic' => 'fa-list-check']],
            (bool) $paySoon => ['tone' => 'warn', 'k' => 'Paiement à venir', 't' => fcfa($paySoon->remaining()).' le '.$paySoon->due_on->translatedFormat('j F'), 's' => 'Payez par Mobile Money puis envoyez la preuve : le reçu arrive dans votre espace.', 'btn' => ['route' => route('etudiant.paiements'), 'l' => 'Voir comment payer', 'ic' => 'fa-credit-card']],
            $next->isNotEmpty() => ['tone' => 'calm', 'k' => 'Prochain cours', 't' => $next[0]->title, 's' => ucfirst($next[0]->starts_at->translatedFormat('l j F')).' · '.$next[0]->hours(), 'btn' => ['route' => route('etudiant.cours'), 'l' => 'Voir mes cours', 'ic' => 'fa-calendar-days']],
            default => ['tone' => 'calm', 'k' => 'Tout est à jour', 't' => 'Rien à faire pour le moment', 's' => 'Profitez-en pour explorer la bibliothèque.', 'btn' => ['route' => route('etudiant.bibliotheque'), 'l' => 'Ouvrir la bibliothèque', 'ic' => 'fa-book-bookmark']],
        };

        $progress = $s->formations->map(fn ($f) => ['f' => $f, 'pct' => $s->progress($f->id)]);
        $validated = $s->assignments()->where('status', 'valide')->count();
        $total = $s->assignments()->count();

        return $this->page('livewire.etudiant.accueil', compact('s', 'action', 'next', 'open', 'nextPay', 'progress', 'validated', 'total'),
            'Accueil', 'Espace étudiant · '.ucfirst(now()->translatedFormat('l j F Y')));
    }
}
