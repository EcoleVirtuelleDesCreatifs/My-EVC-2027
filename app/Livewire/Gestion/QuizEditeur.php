<?php

namespace App\Livewire\Gestion;

use App\Livewire\Concerns\PorteeFormations;
use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Student;
use App\Models\User;
use App\Notifications\Avis;
use Livewire\Attributes\Url;

class QuizEditeur extends Page
{
    use PorteeFormations;

    public Quiz $quiz;

    #[Url(except: 'questions')]
    public string $onglet = 'questions';

    public array $p = [];

    public bool $editQ = false;

    public ?int $qId = null;

    public array $qq = [];

    public function mount(): void
    {
        $this->verifierFormation($this->quiz->formation_id);
        $this->chargerParametres();
    }

    private function chargerParametres(): void
    {
        $q = $this->quiz;
        $this->p = ['title' => $q->title, 'description' => (string) $q->description, 'duration_min' => $q->duration_min, 'attempts_allowed' => $q->attempts_allowed,
            'pass_percent' => $q->pass_percent, 'required' => $q->required, 'opens_at' => $q->opens_at?->format('Y-m-d\TH:i') ?? '', 'closes_at' => $q->closes_at?->format('Y-m-d\TH:i') ?? ''];
    }

    public function enregistrerParametres(): void
    {
        $this->validate(['p.title' => 'required|string|max:150', 'p.description' => 'nullable|string|max:2000', 'p.duration_min' => 'required|integer|min:1|max:240',
            'p.attempts_allowed' => 'required|integer|min:1|max:10', 'p.pass_percent' => 'required|integer|min:1|max:100', 'p.required' => 'boolean',
            'p.opens_at' => 'nullable|date', 'p.closes_at' => 'nullable|date|after:p.opens_at'], [], ['p.closes_at' => 'date de clôture']);
        $this->quiz->update(collect($this->p)->map(fn ($v) => $v === '' ? null : $v)->all());
        $this->toast('Paramètres enregistrés.');
    }

    public function publier(): void
    {
        if (! $this->quiz->published && $this->quiz->questions()->count() === 0) {
            $this->toast('Ajoutez au moins une question avant de publier.', 'warn');

            return;
        }
        $this->quiz->update(['published' => ! $this->quiz->published]);
        if ($this->quiz->published) {
            User::whereIn('id', Student::whereHas('enrollments', fn ($q) => $q->where('formation_id', $this->quiz->formation_id))->where('status', 'actif')->pluck('user_id'))->get()
                ->each->notify(new Avis('Nouveau quiz', '« '.$this->quiz->title.' » est disponible ('.$this->quiz->duration_min.' min).', route('etudiant.quiz.passer', $this->quiz), 'fa-list-check', 'cor'));
        }
        AuditLog::record($this->quiz->published ? 'Quiz publié' : 'Quiz dépublié', $this->quiz->title);
        $this->toast($this->quiz->published ? 'Quiz publié : les étudiants sont prévenus.' : 'Quiz repassé en brouillon.', $this->quiz->published ? 'ok' : 'info');
    }

    public function nouvelleQuestion(): void
    {
        $this->qq = ['text' => '', 'options' => ['', '', '', ''], 'answer' => 0, 'explanation' => ''];
        $this->qId = null;
        $this->resetErrorBag();
        $this->editQ = true;
    }

    public function modifierQuestion(int $id): void
    {
        $x = $this->quiz->questions()->findOrFail($id);
        $this->qq = ['text' => $x->text, 'options' => $x->options, 'answer' => $x->answer, 'explanation' => (string) $x->explanation];
        $this->qId = $id;
        $this->resetErrorBag();
        $this->editQ = true;
    }

    public function ajouterOption(): void
    {
        if (count($this->qq['options']) < 6) {
            $this->qq['options'][] = '';
        }
    }

    public function retirerOption(int $i): void
    {
        if (count($this->qq['options']) <= 2) {
            return;
        }
        array_splice($this->qq['options'], $i, 1);
        if ($this->qq['answer'] >= count($this->qq['options'])) {
            $this->qq['answer'] = 0;
        }
    }

    public function enregistrerQuestion(): void
    {
        $this->validate(['qq.text' => 'required|string|max:1000', 'qq.options' => 'required|array|min:2|max:6', 'qq.options.*' => 'required|string|max:300',
            'qq.answer' => 'required|integer|min:0|max:'.(count($this->qq['options']) - 1), 'qq.explanation' => 'nullable|string|max:1000'],
            [], ['qq.text' => 'question', 'qq.options.*' => 'réponse']);
        $data = ['text' => trim($this->qq['text']), 'options' => array_values(array_map('trim', $this->qq['options'])), 'answer' => (int) $this->qq['answer'], 'explanation' => trim($this->qq['explanation']) ?: null];
        if ($this->qId) {
            $this->quiz->questions()->findOrFail($this->qId)->update($data);
        } else {
            $this->quiz->questions()->create($data + ['position' => (int) $this->quiz->questions()->max('position') + 1]);
        }
        $this->editQ = false;
        $this->toast('Question enregistrée.');
    }

    public function supprimerQuestion(int $id): void
    {
        $this->quiz->questions()->findOrFail($id)->delete();
        $this->toast('Question supprimée.');
    }

    public function deplacer(int $id, int $dir): void
    {
        $list = $this->quiz->questions()->get()->values();
        $i = $list->search(fn ($x) => $x->id === $id);
        $j = $i + $dir;
        if ($i === false || $j < 0 || $j >= $list->count()) {
            return;
        }
        [$a, $b] = [$list[$i], $list[$j]];
        $list->each(fn ($x, $k) => $x->update(['position' => $k]));
        $a->update(['position' => $j]);
        $b->update(['position' => $i]);
    }

    public function autoriserNouvelEssai(int $studentId): void
    {
        $last = QuizAttempt::where('quiz_id', $this->quiz->id)->where('student_id', $studentId)->whereNotNull('submitted_at')->latest('submitted_at')->first();
        $last?->delete();
        $this->toast('Dernière tentative annulée : l\'étudiant peut repasser le quiz.', 'info');
    }

    public function closeModal(): void
    {
        $this->editQ = false;
    }

    public function render()
    {
        $q = $this->quiz->load('formation', 'questions');
        $results = collect();
        $stats = [];
        if ($this->onglet === 'resultats') {
            $attempts = QuizAttempt::where('quiz_id', $q->id)->whereNotNull('submitted_at')->get()->groupBy('student_id');
            $results = Student::with('user')->whereHas('enrollments', fn ($e) => $e->where('formation_id', $q->formation_id))->where('status', 'actif')->get()
                ->map(fn ($s) => ['s' => $s, 'tries' => $attempts->get($s->id, collect()), 'best' => $attempts->get($s->id)?->max('percent')])->sortByDesc('best')->values();
            $all = $attempts->flatten();
            foreach ($q->questions as $qq) {
                $stats[$qq->id] = $all->count() ? round($all->filter(fn ($a) => ($a->answers[$qq->id] ?? null) === $qq->answer)->count() / $all->count() * 100) : null;
            }
        }

        return $this->page('livewire.gestion.quiz-editeur', compact('q', 'results', 'stats'), $q->title,
            $this->filAriane('<a href="'.gestion_route('quiz').'" wire:navigate>Quiz</a>'),
            '<button class="btn t-cta '.($q->published ? 'btn-ghost' : 'btn-primary').'" wire:click="publier"><i class="fa-solid '.($q->published ? 'fa-eye-slash' : 'fa-paper-plane').'"></i>'.($q->published ? 'Dépublier' : 'Publier').'</button>');
    }
}
