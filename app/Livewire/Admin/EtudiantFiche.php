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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;

class EtudiantFiche extends Page
{
    use CorrigeProjets;

    public Student $student;

    #[Url(except: 'projets')]
    public string $onglet = 'projets';

    /* Formations : ajout, remplacement, retrait avec recalcul de l'échéancier */
    public bool $inscription = false;

    public string $action = 'ajouter';

    public ?int $ancienne = null;

    public ?int $nouvelle = null;

    public ?int $sessionId = null;

    public string $modeInscription = 'En ligne';

    public function gererFormations(string $action = 'ajouter', ?int $formationId = null): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        $this->resetErrorBag();
        $this->action = in_array($action, ['ajouter', 'remplacer', 'retirer'], true) ? $action : 'ajouter';
        $this->ancienne = $formationId ?? $this->student->formations->first()?->id;
        $this->nouvelle = null;
        $this->sessionId = $this->student->promotion_id;
        $this->modeInscription = $this->student->formations->first()?->pivot->mode ?? 'En ligne';
        $this->inscription = true;
    }

    public function confirmerInscription(\App\Services\EnrollmentService $svc): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        $this->validate([
            'nouvelle' => $this->action === 'retirer' ? 'nullable' : 'required|exists:formations,id',
            'ancienne' => $this->action === 'ajouter' ? 'nullable' : 'required|exists:formations,id',
            'sessionId' => 'nullable|exists:promotions,id', 'modeInscription' => 'required|in:En ligne,Présentiel',
        ], ['nouvelle.required' => 'Choisissez la nouvelle formation.', 'ancienne.required' => 'Choisissez la formation concernée.']);
        try {
            $r = $svc->change($this->student, $this->action === 'retirer' ? null : \App\Models\Formation::findOrFail($this->nouvelle),
                $this->action === 'ajouter' ? null : \App\Models\Formation::findOrFail($this->ancienne),
                $this->sessionId ? \App\Models\Promotion::find($this->sessionId) : null, $this->modeInscription);
        } catch (\Illuminate\Validation\ValidationException $e) {
            foreach ($e->errors() as $k => $m) {
                $this->addError($k, $m[0]);
            }

            return;
        }
        $this->inscription = false;
        $this->student->refresh();
        $this->toast('Inscription mise à jour. Nouveau total : '.fcfa($r['newTotal']).' · reste à payer : '.fcfa($r['newBalance']).'. L’étudiant est prévenu.');
    }

    /* Rapport de fin de formation : retour de l'équipe */
    public array $retour = [];

    public function mount(): void
    {
        $this->accesJusquau = $this->student->access_until?->toDateString();
    }

    public function relireRapport(int $reportId, bool $valide, \App\Services\FinalReportService $svc): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        $report = $this->student->finalReports()->findOrFail($reportId);
        $this->validate(["retour.$reportId" => 'nullable|string|max:2000']);
        try {
            $svc->review($report, $valide, trim((string) ($this->retour[$reportId] ?? '')) ?: null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError("retour.$reportId", $e->getMessage());

            return;
        }
        unset($this->retour[$reportId]);
        $this->toast($valide ? 'Rapport validé. L’étudiant est prévenu par e-mail.' : 'Demande de compléments envoyée à l’étudiant.', $valide ? 'ok' : 'info');
    }

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

    public bool $editionProfil = false;

    public array $profil = [];

    public function modifierProfil(): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        $s = $this->student->fresh('user');
        $this->profil = ['name' => $s->user->name, 'email' => $s->user->email, 'phone' => $s->user->phone,
            'birth_date' => $s->birth_date?->toDateString(), 'gender' => $s->gender, 'country' => $s->country, 'city' => $s->city, 'level' => $s->level];
        $this->resetErrorBag();
        $this->editionProfil = true;
    }

    public function enregistrerProfil(): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        foreach ($this->profil as $field => $value) {
            if (is_string($value)) {
                $this->profil[$field] = trim($value);
            }
        }
        if (is_string($this->profil['email'] ?? null)) {
            $this->profil['email'] = mb_strtolower($this->profil['email']);
        }
        foreach (['phone', 'birth_date', 'gender', 'country', 'city'] as $field) {
            if (($this->profil[$field] ?? null) === '') {
                $this->profil[$field] = null;
            }
        }
        $data = $this->validate([
            'profil.name' => 'required|string|max:255',
            'profil.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->student->user_id)],
            'profil.phone' => 'nullable|string|max:30', 'profil.birth_date' => 'nullable|date|before_or_equal:today',
            'profil.gender' => 'nullable|string|max:20', 'profil.country' => 'nullable|string|max:100',
            'profil.city' => 'nullable|string|max:150', 'profil.level' => 'required|in:Débutant,Intermédiaire,Avancé',
        ], [], ['profil.name' => 'nom complet', 'profil.email' => 'adresse e-mail', 'profil.birth_date' => 'date de naissance'])['profil'];
        DB::transaction(function () use ($data) {
            $s = Student::lockForUpdate()->findOrFail($this->student->id);
            $u = $s->user()->lockForUpdate()->firstOrFail();
            $identity = Arr::only($data, ['name', 'email', 'phone']);
            if ($u->email !== $data['email']) {
                $u->email_verified_at = null;
                $u->remember_token = null;
            }
            $u->fill($identity)->save();
            $s->update(Arr::only($data, ['birth_date', 'gender', 'country', 'city', 'level']));
            AuditLog::record('Profil étudiant modifié', $s->matricule);
        });
        $this->student->refresh();
        $this->reset('editionProfil', 'profil');
        $this->toast('Profil étudiant mis à jour.');
    }

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
        $this->toast($n ? 'Projet attribué. L\'étudiant est prévenu.' : 'Ce projet a déjà été attribué à cet étudiant ou ne correspond pas à sa formation.', $n ? 'ok' : 'warn');
    }

    public function closeModal(): void
    {
        $this->versement = $this->attribution = $this->editionProfil = $this->inscription = false;
        $this->reset('profil');
        $this->resetErrorBag();
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

    /** Prolongation de l'accès complet (date), ou retour à la fin de session (vide). */
    public ?string $accesJusquau = null;

    public function enregistrerAcces(): void
    {
        abort_unless(auth()->user()->can('admin.etudiants'), 403);
        $this->validate(['accesJusquau' => 'nullable|date'], [], ['accesJusquau' => 'date de fin d’accès']);
        $this->student->update(['access_until' => $this->accesJusquau ?: null]);
        $this->student = $this->student->fresh();
        AuditLog::record('Accès étudiant modifié', $this->student->matricule.' — '.($this->accesJusquau ? 'accès complet jusqu’au '.\Illuminate\Support\Carbon::parse($this->accesJusquau)->format('d/m/Y') : 'fin de session'));
        $this->toast($this->student->isReadOnly() ? 'Compte en lecture seule.' : 'Accès complet jusqu’au '.$this->student->accessEndsOn()?->translatedFormat('j F Y').'.');
    }

    public function renvoyerActivation(): void
    {
        Password::broker('activations')->sendResetLink(['email' => $this->student->user->email]);
        $this->toast('Lien de connexion envoyé à '.$this->student->user->email.'.');
    }

    public function render(AvailabilityService $dispo)
    {
        $s = $this->student->load(['user', 'formations', 'promotion', 'assignments' => fn ($q) => $q->with('project.formation')->withCount('submissions'),
            'payments.recorder', 'schedules.formation', 'certificates', 'finalReports.reviewer']);
        /* Données chargées seulement quand elles sont affichées (la fiche s'actualise toutes les 15 s) */
        $presences = $this->onglet === 'presences' ? $s->presences()->with('seance.formation')->latest('created_at')->take(30)->get() : collect();
        $projects = $this->attribution ? Project::whereIn('formation_id', $s->formations->pluck('id'))->where('published', true)
            ->whereDoesntHave('assignments', fn ($q) => $q->where('student_id', $s->id))->orderBy('formation_id')->orderBy('title')->get() : collect();

        $simulation = null;
        if ($this->inscription) {
            $add = $this->action !== 'retirer' && $this->nouvelle ? \App\Models\Formation::find($this->nouvelle) : null;
            $remove = $this->action !== 'ajouter' && $this->ancienne ? \App\Models\Formation::find($this->ancienne) : null;
            $simulation = app(\App\Services\EnrollmentService::class)->preview($s, $add, $remove) + ['add' => $add, 'remove' => $remove];
        }
        $alloc = $s->allocation();
        $parFormation = $s->formations->map(fn ($f) => ['f' => $f, 'due' => (int) $s->schedules->where('formation_id', $f->id)->sum('amount'),
            'paid' => (int) $s->schedules->where('formation_id', $f->id)->sum(fn ($x) => $alloc[$x->id] ?? 0)]);

        return $this->page('livewire.admin.etudiant', ['s' => $s, 'etat' => $dispo->row($s), 'presences' => $presences, 'projects' => $projects, 'schedules' => $s->scheduleRows(),
            'simulation' => $simulation, 'parFormation' => $parFormation, 'catalogue' => $this->inscription ? \App\Models\Formation::active()->orderBy('position')->get() : collect(),
            'sessions' => $this->inscription ? \App\Models\Promotion::orderByDesc('starts_on')->get() : collect()],
            $s->user->name, 'Administration <span class="sep">›</span> <a href="'.route('admin.etudiants').'" wire:navigate>Étudiants</a> <span class="sep">›</span> '.$s->matricule);
    }
}
