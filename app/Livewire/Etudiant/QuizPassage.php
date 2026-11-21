<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizService;
use Illuminate\Validation\ValidationException;

/** Passage d'un quiz (chronométré, une question à la fois) puis correction. */
class QuizPassage extends Page
{
    public Quiz $quiz;

    public ?int $attemptId = null;

    public array $answers = [];

    public int $index = 0;

    public bool $intro = true;

    public function mount(Quiz $quiz): void
    {
        abort_unless($quiz->published && $this->student()->formations->contains('id', $quiz->formation_id), 403);
        $open = $quiz->attempts()->where('student_id', $this->student()->id)->whereNull('submitted_at')->latest()->first();
        if ($open) {
            $this->attemptId = $open->id;
            $this->answers = $open->answers ?? [];
            $this->intro = false;
        }
    }

    public function commencer(QuizService $svc): void
    {
        try {
            $a = $svc->start($this->quiz, $this->student());
        } catch (ValidationException $e) {
            $this->toast($e->getMessage(), 'warn');

            return;
        }
        $this->attemptId = $a->id;
        $this->answers = $a->answers ?? [];
        $this->index = 0;
        $this->intro = false;
    }

    private function tentative(): ?QuizAttempt
    {
        return $this->attemptId ? QuizAttempt::where('student_id', $this->student()->id)->find($this->attemptId) : null;
    }

    /** Chaque réponse est enregistrée aussitôt (une coupure réseau ne fait rien perdre). */
    public function repondre(int $questionId, int $option): void
    {
        $a = $this->tentative();
        if (! $a || $a->submitted_at || $a->expired()) {
            return;
        }
        $this->answers[$questionId] = $option;
        $a->update(['answers' => $this->answers]);
    }

    public function aller(int $i): void
    {
        $this->index = max(0, min($i, $this->quiz->questions()->count() - 1));
    }

    public function soumettre(QuizService $svc): void
    {
        $a = $this->tentative();
        if ($a && ! $a->submitted_at) {
            $a = $svc->submit($a, $this->answers);
            $this->toast($a->passed() ? 'Bravo, quiz réussi : '.$a->percent.' % !' : 'Quiz terminé : '.$a->percent.' %.', $a->passed() ? 'ok' : 'warn');
        }
    }

    public function render()
    {
        $s = $this->student();
        $q = $this->quiz->load('questions', 'formation');
        $q->load(['attempts' => fn ($x) => $x->where('student_id', $s->id)]);
        $a = $this->tentative();
        if ($a && ! $a->submitted_at && $a->expired()) {
            $a = app(QuizService::class)->submit($a, $a->answers ?? []);   // temps écoulé pendant l'absence
        }
        $last = $a?->submitted_at ? $a : $q->attempts->whereNotNull('submitted_at')->sortByDesc('submitted_at')->first();
        $state = $q->stateFor($s);
        $mode = match (true) {
            $a && ! $a->submitted_at => 'passage',
            $this->intro && $state[2] => 'intro',      // arrivée sur la page : consignes avant de (re)commencer
            (bool) $last => 'resultat',
            default => 'intro',
        };

        return $this->page('livewire.etudiant.quiz-passage', ['q' => $q, 'a' => $a, 'last' => $last, 'mode' => $mode, 'state' => $state], $q->title,
            'Espace étudiant <span class="sep">›</span> <a href="'.route('etudiant.quiz').'" wire:navigate>Quiz</a>');
    }
}
