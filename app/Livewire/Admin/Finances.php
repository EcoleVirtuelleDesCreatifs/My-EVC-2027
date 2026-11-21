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
    use WithPagination;

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

    public function updated($prop): void
    {
        if (in_array($prop, ['vue', 'q'])) {
            $this->resetPage();
        }
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
        $this->form = false;
    }

    public function render()
    {
        $kpi = ['total' => Payment::sum('amount'), 'mois' => Payment::whereBetween('paid_on', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount'),
            'du' => PaymentSchedule::whereHas('student', fn ($q) => $q->where('status', '!=', 'abandon'))->sum('amount')];
        $payments = $this->vue === 'versements' ? Payment::with('student.user', 'recorder')
            ->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('receipt_number', 'like', "%{$this->q}%")->orWhere('transaction_ref', 'like', "%{$this->q}%")
                ->orWhereHas('student', fn ($s) => $s->where('matricule', 'like', "%{$this->q}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->q}%")))))
            ->latest('paid_on')->latest('id')->paginate(20) : null;
        $retards = Student::with('user', 'schedules', 'payments')->where('status', 'actif')->get()
            ->flatMap(fn ($s) => $s->scheduleRows()->filter(fn ($x) => $x->remaining() > 0 && $x->due_on->endOfDay()->isPast()))->sortBy('due_on')->values();
        $found = $this->recherche ? Student::with('user')->where('matricule', 'like', "%{$this->recherche}%")->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->recherche}%"))->take(6)->get() : collect();
        $chosen = $this->etudiant ? Student::with('user', 'schedules', 'payments')->find($this->etudiant) : null;

        return $this->page('livewire.admin.finances', compact('kpi', 'payments', 'retards', 'found', 'chosen'), 'Finances', 'Administration <span class="sep">›</span> Finances',
            '<button class="btn btn-primary t-cta" wire:click="nouveau"><i class="fa-solid fa-plus"></i>Enregistrer un versement</button>');
    }
}
