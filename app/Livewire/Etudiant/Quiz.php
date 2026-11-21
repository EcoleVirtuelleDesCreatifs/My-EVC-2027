<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Quiz as QuizModel;

class Quiz extends Page
{
    use ChoixFormation;

    public function render()
    {
        $s = $this->student();
        $quizzes = QuizModel::visibleFor($this->formationIds())->with(['formation', 'attempts' => fn ($q) => $q->where('student_id', $s->id)])->withCount('questions')
            ->orderByRaw('closes_at is null')->orderBy('closes_at')->latest()->get()
            ->map(fn ($q) => ['q' => $q, 'state' => $q->stateFor($s)]);
        $todo = $quizzes->filter(fn ($x) => $x['state'][2])->count();
        $passed = $quizzes->filter(fn ($x) => $x['state'][1] === 'ok')->count();
        $done = $quizzes->filter(fn ($x) => $x['state'][3]);
        $avg = $done->count() ? round($done->avg(fn ($x) => $x['state'][3]->percent)) : null;

        return $this->page('livewire.etudiant.quiz', compact('s', 'quizzes', 'todo', 'passed', 'avg'), 'Quiz et évaluations', 'Espace étudiant <span class="sep">›</span> Quiz');
    }
}
