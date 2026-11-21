<?php

namespace App\Livewire\Admin;

use App\Livewire\Concerns\CorrigeProjets;
use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Student;
use App\Services\AvailabilityService;
use App\Services\PaymentService;
use App\Services\ProjectService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Url;

class EtudiantFiche extends Page
{
    use CorrigeProjets;

    public Student $student;

    #[Url(except: 'projets')]
    public string $onglet = 'projets';

    /* Versement */
    public bool $versement = false;

    public ?int $montant = null;

    public string $methode = 'Orange Money';

    public string $reference = '';

    public string $date = '';

    /* Attribution d'un projet */
    public bool $attribution = false;

    public ?int $projet = null;

    public string $echeance = '';

    public function ouvrirVersement(): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $this->montant = $this->student->nextSchedule()?->remaining();
        $this->date = now()->toDateString();
        $this->versement = true;
    }

    public function enregistrerVersement(PaymentService $svc): void
    {
        abort_unless(auth()->user()->can('admin.finances'), 403);
        $this->validate(['montant' => 'required|integer|min:500', 'methode' => 'required|in:'.implode(',', Payment::METHODS), 'reference' => 'nullable|string|max:60', 'date' => 'required|date|before_or_equal:today']);
        $p = $svc->record($this->student, $this->montant, $this->methode, $this->reference ?: null, Carbon::parse($this->date));
        $this->reset('versement', 'montant', 'reference');
        $this->student->refreshAllocation();
        $this->toast('Versement enregistré : reçu '.$p->receipt_number.'.');
    }

    public function ouvrirAttribution(): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $this->projet = null;
        $this->echeance = now()->addDays(10)->toDateString();
        $this->attribution = true;
    }

    public function attribuer(ProjectService $svc): void
    {
        abort_unless(auth()->user()->can('admin.projets'), 403);
        $this->validate(['projet' => 'required|exists:projects,id', 'echeance' => 'required|date|after_or_equal:today']);
        [$n] = $svc->assign(Project::findOrFail($this->projet), collect([$this->student->load('formations', 'user')]), Carbon::parse($this->echeance));
        $this->attribution = false;
        $this->toast($n ? 'Projet attribué. L\'étudiant est prévenu.' : 'Ce projet est déjà en cours pour cet étudiant.', $n ? 'ok' : 'warn');
    }

    public function closeModal(): void
    {
        $this->versement = $this->attribution = false;
        $this->corrige = null;
    }

    public function statut(string $status): void
    {
        abort_unless(array_key_exists($status, Student::STATUSES), 422);
        $this->student->update(['status' => $status]);
        $this->student->user->update(['status' => $status === 'suspendu' ? 'suspendu' : 'actif']);
        AuditLog::record('Statut étudiant : '.Student::STATUSES[$status], $this->student->matricule);
        $this->toast('Statut mis à jour : '.Student::STATUSES[$status].'.');
    }

    public function renvoyerActivation(): void
    {
        Password::broker('activations')->sendResetLink(['email' => $this->student->user->email]);
        $this->toast('Lien de connexion envoyé à '.$this->student->user->email.'.');
    }

    public function render(AvailabilityService $dispo)
    {
        $s = $this->student->load(['user', 'formations', 'promotion', 'assignments.project.formation', 'payments.recorder', 'schedules.formation', 'certificates']);
        $presences = $s->presences()->with('seance.formation')->latest('created_at')->take(30)->get();
        $projects = Project::whereIn('formation_id', $s->formations->pluck('id'))->where('published', true)->orderBy('formation_id')->get();

        return $this->page('livewire.admin.etudiant', ['s' => $s, 'etat' => $dispo->row($s), 'presences' => $presences, 'projects' => $projects, 'schedules' => $s->scheduleRows()],
            $s->user->name, 'Administration <span class="sep">›</span> <a href="'.route('admin.etudiants').'" wire:navigate>Étudiants</a> <span class="sep">›</span> '.$s->matricule);
    }
}
