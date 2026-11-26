<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Candidature;
use App\Models\Promotion;
use App\Services\AdmissionService;
use App\Notifications\CandidatureMiseAJour;
use Illuminate\Support\Facades\Notification;

class CandidatureFiche extends Page
{
    public Candidature $candidature;

    public ?int $score = null;

    public string $notes = '';

    public ?int $promotion = null;

    public string $mode = 'En ligne';

    public bool $activer = false;

    public bool $validerVersement = false;

    public array $premier = [];

    public string $typeVersement = 'nouveau';

    public ?int $paiementExistant = null;

    public function ouvrirPremierVersement(): void
    {
        abort_unless(auth()->user()->can('admin.candidatures'), 403);
        $c = $this->candidature->refresh();
        $this->premier = ['amount' => (int) \App\Models\Formation::whereIn('code', $c->formations)->sum('deposit'),
            'method' => '', 'paid_on' => now()->toDateString(), 'transaction_ref' => ''];
        $this->typeVersement = $c->student?->payments()->exists() ? 'existant' : 'nouveau';
        $this->paiementExistant = null;
        $this->activer = false;
        $this->resetErrorBag();
        $this->validerVersement = true;
    }

    public function confirmerPremierVersement(\App\Services\AdmissionPaymentService $service): void
    {
        $this->validate(['typeVersement' => 'required|in:nouveau,existant']);
        if ($this->typeVersement === 'existant') {
            $this->validate(['paiementExistant' => 'required|integer|min:1']);
        } else {
            $this->validate(['premier.amount' => 'required|integer|min:1|max:1000000000',
                'premier.method' => 'required|in:'.implode(',', \App\Models\Payment::METHODS),
                'premier.paid_on' => 'required|date_format:Y-m-d|before_or_equal:today', 'premier.transaction_ref' => 'nullable|string|max:150'],
                [], ['premier.amount' => 'montant reçu', 'premier.method' => 'moyen de paiement', 'premier.paid_on' => 'date du paiement']);
        }
        $stage = $this->candidature->fresh()->stage;
        $service->confirm($this->candidature, $this->premier, $this->typeVersement === 'existant' ? $this->paiementExistant : null);
        if (! in_array($stage, ['activee', 'paiement'], true)) {
            Notification::route('mail', $this->candidature->email)->notify(new CandidatureMiseAJour($this->candidature, 'paiement'));
        }
        $this->validerVersement = false;
        $this->toast($this->candidature->student_id ? 'Premier versement rattaché. Le solde étudiant est mis à jour.' : 'Premier versement validé. Il sera déduit du solde dès l’activation du compte.');
    }

    public function mount(): void
    {
        $this->score = $this->candidature->test_score;
        $this->notes = (string) $this->candidature->notes;
        $this->promotion = Promotion::current()?->id;
        $this->mode = str_contains((string) $this->candidature->mode, 'résentiel') ? 'Présentiel' : 'En ligne';
    }

    public function etape(string $stage): void
    {
        abort_unless(array_key_exists($stage, Candidature::STAGES) && $stage !== 'activee', 422);
        $this->candidature->refresh();
        if ($stage === 'paiement') {
            $this->ouvrirPremierVersement();

            return;
        }
        if ($this->candidature->stage === $stage) {
            return;
        }
        if ($stage === 'test' && ! $this->candidature->saopClosed()) {
            $this->toast('Le questionnaire doit être soumis ou arrivé à échéance avant son évaluation.', 'warn');
            return;
        }
        $this->candidature->update(['stage' => $stage]);
        Notification::route('mail', $this->candidature->email)->notify(new CandidatureMiseAJour($this->candidature, $stage));
        AuditLog::record('Candidature : '.Candidature::STAGES[$stage][0], $this->candidature->reference);
        $this->toast('Étape mise à jour : '.Candidature::STAGES[$stage][0].'.');
    }

    public function envoyerQuestionnaire(): void
    {
        $this->candidature->refresh();
        abort_unless($this->candidature->stage === 'eligible' && ! $this->candidature->saopClosed(), 422);
        Notification::route('mail', $this->candidature->email)->notify(new CandidatureMiseAJour($this->candidature, 'eligible'));
        $this->toast('Le lien du questionnaire a été envoyé au candidat.');
    }

    public function enregistrer(): void
    {
        $this->validate(['score' => 'nullable|integer|min:0|max:20', 'notes' => 'nullable|string|max:5000']);
        $this->candidature->update(['test_score' => $this->score, 'notes' => $this->notes ?: null]);
        $this->toast('Dossier enregistré.');
    }

    public function closeModal(): void
    {
        $this->activer = false;
        $this->validerVersement = false;
    }

    public function confirmerActivation(AdmissionService $svc)
    {
        $this->validate(['promotion' => 'required|exists:promotions,id', 'mode' => 'required|in:En ligne,Présentiel']);
        $student = $svc->activate($this->candidature, Promotion::findOrFail($this->promotion), $this->mode);
        session()->flash('toast', 'Compte activé : '.$student->matricule.'. L\'e-mail d\'activation est envoyé.');

        return $this->redirect(route('admin.etudiant', $student), navigate: true);
    }

    public function render()
    {
        $c = $this->candidature->load('files', 'student', 'firstPayment');
        $versementsExistants = $this->validerVersement && $c->student ? $c->student->payments()->get() : collect();
        $groups = [
            'Identité' => ['Genre', 'Date de naissance', 'Nationalité', 'Pays', 'Ville', 'Commune'],
            'Profil' => ['Situation', 'Niveau d\'études', 'Établissement', 'Poste', 'Niveau dans le domaine', 'Outils', 'Ordinateur', 'Connexion', 'Objectif', 'Source', 'Motivation', 'Disponibilités', 'Lien portfolio'],
            'Inscription et paiement' => ['Date souhaitée d’inscription et de paiement'],
            'Personne à contacter' => ['Contact - Nom', 'Contact - Lien', 'Contact - Téléphone', 'Contact - E-mail', 'Contact - Fonction', 'Contact - Résidence', 'Contact - Prend en charge les frais'],
        ];

        return $this->page('livewire.admin.candidature', ['c' => $c, 'groups' => $groups, 'versementsExistants' => $versementsExistants, 'promotions' => Promotion::orderByDesc('starts_on')->get()],
            $c->full_name, 'Administration <span class="sep">›</span> <a href="'.route('admin.candidatures').'" wire:navigate>Préinscriptions</a> <span class="sep">›</span> '.$c->reference);
    }
}
