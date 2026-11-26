<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\PaymentSchedule;
use App\Models\Student;
use App\Notifications\Avis;
use App\Services\PaymentService;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Finances extends Page
{
    use WithPagination, \Livewire\WithFileUploads;

    #[Url(except: 'versements')]
    public string $vue = 'versements';

    #[Url(except: '')]
    public string $q = '';

    public bool $form = false;

    public ?int $etudiant = null;

    public string $recherche = '';

    public ?int $montant = null;

    public string $methode = 'Orange Money';

    public string $reference = '';

    public string $date = '';

    /** Période analysée (mois « AAAA-MM ») pour la caisse et les dépenses. */
    #[Url(except: '')]
    public string $periode = '';

    #[Url(except: '')]
    public string $categorie = '';

    /* Dépense */
    public bool $depenseForm = false;

    public ?int $depenseId = null;

    public array $d = [];

    public $justificatif = null;

    public function updated($prop): void
    {
        if (in_array($prop, ['vue', 'q', 'periode', 'categorie'])) {
            $this->resetPage();
        }
    }

    /** [début, fin] de la période choisie (mois courant par défaut). */
    private function range(): array
    {
        $m = preg_match('/^\d{4}-\d{2}$/', $this->periode) ? Carbon::createFromFormat('Y-m-d', $this->periode.'-01') : now();

        return [$m->copy()->startOfMonth(), $m->copy()->endOfMonth()];
    }

    public function nouvelleDepense(): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $this->reset('depenseId', 'justificatif');
        $this->resetErrorBag();
        $this->d = ['spent_on' => now()->toDateString(), 'label' => '', 'category' => \App\Models\Expense::CATEGORIES[0], 'amount' => null,
            'method' => 'Espèces', 'beneficiary' => '', 'reference' => '', 'note' => ''];
        $this->depenseForm = true;
    }

    public function modifierDepense(int $id): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $e = \App\Models\Expense::findOrFail($id);
        $this->reset('justificatif');
        $this->resetErrorBag();
        $this->depenseId = $e->id;
        $this->d = ['spent_on' => $e->spent_on->toDateString(), 'label' => $e->label, 'category' => $e->category, 'amount' => $e->amount,
            'method' => $e->method, 'beneficiary' => (string) $e->beneficiary, 'reference' => (string) $e->reference, 'note' => (string) $e->note];
        $this->depenseForm = true;
    }

    public function enregistrerDepense(): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $data = $this->validate([
            'd.spent_on' => 'required|date|before_or_equal:today', 'd.label' => 'required|string|max:160',
            'd.category' => ['required', \Illuminate\Validation\Rule::in(\App\Models\Expense::CATEGORIES)], 'd.amount' => 'required|integer|min:1|max:100000000',
            'd.method' => ['required', \Illuminate\Validation\Rule::in(Payment::METHODS)], 'd.beneficiary' => 'nullable|string|max:160',
            'd.reference' => 'nullable|string|max:60', 'd.note' => 'nullable|string|max:2000',
            'justificatif' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,webp',
        ], [], ['d.spent_on' => 'date', 'd.label' => 'libellé', 'd.category' => 'catégorie', 'd.amount' => 'montant', 'd.method' => 'caisse', 'justificatif' => 'justificatif'])['d'];
        $data = array_map(fn ($v) => is_string($v) ? (trim($v) ?: null) : $v, $data);
        $e = $this->depenseId ? \App\Models\Expense::findOrFail($this->depenseId) : new \App\Models\Expense(['recorded_by' => auth()->id()]);
        if ($this->justificatif) {
            if ($e->receipt_path) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete($e->receipt_path);
            }
            $data['receipt_path'] = $this->justificatif->store('depenses', 'local');
            $data['receipt_name'] = $this->justificatif->getClientOriginalName();
        }
        $e->fill($data)->save();
        AuditLog::record($this->depenseId ? 'Dépense modifiée' : 'Dépense enregistrée', $e->label.' — '.fcfa($e->amount).' ('.$e->method.')');
        $this->depenseForm = false;
        $this->toast($this->depenseId ? 'Dépense modifiée.' : 'Dépense enregistrée : la caisse est mise à jour.');
        $this->reset('depenseId', 'justificatif');
    }

    public function supprimerDepense(int $id): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $e = \App\Models\Expense::findOrFail($id);
        if ($e->receipt_path) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($e->receipt_path);
        }
        AuditLog::record('Dépense supprimée', $e->label.' — '.fcfa($e->amount).' du '.$e->spent_on->format('d/m/Y'));
        $e->delete();
        $this->toast('Dépense supprimée.', 'info');
    }

    public function nouveau(?int $studentId = null): void
    {
        $this->reset('montant', 'reference', 'recherche');
        $this->etudiant = $studentId;
        $this->montant = $studentId ? Student::find($studentId)?->nextSchedule()?->remaining() : null;
        $this->date = now()->toDateString();
        $this->form = true;
    }

    public function choisir(int $id): void
    {
        $this->etudiant = $id;
        $this->montant = Student::find($id)?->nextSchedule()?->remaining();
        $this->recherche = '';
    }

    public function enregistrer(PaymentService $svc): void
    {
        $this->validate(['etudiant' => 'required|exists:students,id', 'montant' => 'required|integer|min:500', 'methode' => 'required|in:'.implode(',', Payment::METHODS),
            'reference' => 'nullable|string|max:60', 'date' => 'required|date|before_or_equal:today'], [], ['etudiant' => 'étudiant']);
        $p = $svc->record(Student::with('user', 'schedules', 'payments')->findOrFail($this->etudiant), $this->montant, $this->methode, $this->reference ?: null, Carbon::parse($this->date));
        $this->form = false;
        $this->toast('Versement enregistré : reçu '.$p->receipt_number.'.');
    }

    public function relancer(int $scheduleId): void
    {
        $x = PaymentSchedule::with('student.user', 'student.schedules', 'student.payments')->findOrFail($scheduleId);
        $x->student->user->notify(new Avis('Rappel de paiement', 'Échéance « '.$x->label.' » du '.$x->due_on->translatedFormat('j F').' : '.fcfa($x->remaining()).' restant à régler.',
            route('etudiant.paiements'), 'fa-bell', 'warn', true));
        $x->update(['reminded_at' => now()]);
        AuditLog::record('Relance de paiement', $x->student->matricule.' — '.$x->label);
        $this->toast('Relance envoyée à '.$x->student->user->name.'.');
    }

    public function closeModal(): void
    {
        $this->form = $this->depenseForm = false;
    }

    public function render()
    {
        [$from, $to] = $this->range();
        $cash = app(\App\Services\CashService::class);
        $kpi = $cash->summary($from, $to);
        $caisse = $this->vue === 'caisse' ? ['methods' => $cash->byMethod($from, $to), 'months' => $cash->months(6), 'categories' => $cash->byCategory($from, $to)] : null;
        $expenseQuery = \App\Models\Expense::whereBetween('spent_on', [$from->toDateString(), $to->toDateString()])
            ->when($this->categorie, fn ($q) => $q->where('category', $this->categorie))
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('label', 'like', "%{$this->q}%")->orWhere('beneficiary', 'like', "%{$this->q}%")->orWhere('reference', 'like', "%{$this->q}%")));
        $expenses = $this->vue === 'depenses' ? (clone $expenseQuery)->with('recorder')->latest('spent_on')->latest('id')->paginate(20) : null;
        $expensesTotal = $this->vue === 'depenses' ? (int) $expenseQuery->sum('amount') : 0;
        $months = collect(range(0, 11))->mapWithKeys(fn ($i) => [now()->startOfMonth()->subMonths($i)->format('Y-m') => ucfirst(now()->startOfMonth()->subMonths($i)->translatedFormat('F Y'))]);
        $payments = $this->vue === 'versements' ? Payment::with('student.user', 'recorder')
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('receipt_number', 'like', "%{$this->q}%")->orWhere('transaction_ref', 'like', "%{$this->q}%")
                ->orWhereHas('student', fn ($s) => $s->where('matricule', 'like', "%{$this->q}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->q}%")))))
            ->latest('paid_on')->latest('id')->paginate(20) : null;
        $retards = Student::with('user', 'schedules', 'payments')->where('status', 'actif')->get()
            ->flatMap(fn ($s) => $s->scheduleRows()->filter(fn ($x) => $x->remaining() > 0 && $x->due_on->endOfDay()->isPast()))->sortBy('due_on')->values();
        $found = $this->recherche ? Student::with('user')->where('matricule', 'like', "%{$this->recherche}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->recherche}%"))->take(6)->get() : collect();
        $chosen = $this->etudiant ? Student::with('user', 'schedules', 'payments')->find($this->etudiant) : null;

        return $this->page('livewire.admin.finances', compact('kpi', 'payments', 'retards', 'found', 'chosen', 'caisse', 'expenses', 'expensesTotal', 'months', 'from', 'to'), 'Finances', 'Administration <span class="sep">›</span> Finances',
            '<button class="btn btn-ghost t-cta me-2" wire:click="nouvelleDepense"><i class="fa-solid fa-receipt"></i>Ajouter une dépense</button><button class="btn btn-primary t-cta" wire:click="nouveau"><i class="fa-solid fa-plus"></i>Enregistrer un versement</button>');
    }
}
