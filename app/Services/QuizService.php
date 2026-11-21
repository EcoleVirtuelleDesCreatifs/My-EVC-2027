<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class QuizService
{
    /** Reprend la tentative en cours, ou en démarre une nouvelle si c'est permis. */
    public function start(Quiz $quiz, Student $s): QuizAttempt
    {
        abort_unless($s->formations->contains('id', $quiz->formation_id), 403);
        $open = $quiz->attempts()->where('student_id', $s->id)->whereNull('submitted_at')->latest()->first();
        if ($open) {
            if (! $open->expired()) {
                return $open;
            }
            $this->submit($open, $open->answers ?? []);   // temps écoulé : corrigée avec les réponses enregistrées
        }
        $quiz->load('attempts');
        [, , $canStart] = $quiz->stateFor($s);
        if (! $canStart) {
            throw ValidationException::withMessages(['quiz' => 'Ce quiz n\'est pas disponible (déjà réussi, tentatives épuisées ou clôturé).']);
        }

        return $quiz->attempts()->create(['student_id' => $s->id, 'started_at' => now(), 'answers' => [], 'total' => $quiz->questions()->count()]);
    }

    /** Corrige la tentative. Les réponses envoyées après le délai (+ grâce) sont ignorées : seules celles déjà enregistrées comptent. */
    public function submit(QuizAttempt $a, array $answers): QuizAttempt
    {
        if ($a->submitted_at) {
            return $a;
        }
        $late = $a->expired();
        $answers = $late ? ($a->answers ?? []) : $answers;
        $questions = $a->quiz->questions;
        $score = $questions->filter(fn ($q) => isset($answers[$q->id]) && (int) $answers[$q->id] === (int) $q->answer)->count();
        $a->update(['answers' => $answers, 'score' => $score, 'total' => $questions->count(),
            'percent' => $questions->count() ? (int) round($score / $questions->count() * 100) : 0, 'submitted_at' => now()]);

        return $a;
    }
}
