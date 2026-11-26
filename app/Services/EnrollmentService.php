<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Promotion;
use App\Models\Student;
use App\Notifications\Avis;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Inscriptions d'un étudiant à ses formations : ajout, remplacement, retrait, avec recalcul de l'échéancier.
 * Les versements déjà reçus restent acquis : ils sont réimputés sur les échéances restantes, de la plus ancienne à la plus récente.
 */
class EnrollmentService
{
    /** Crée l'inscription et ses deux échéances (acompte immédiat, solde N jours après le début de la session). */
    public function enroll(Student $student, Formation $formation, Promotion $promotion, string $mode = 'En ligne'): void
    {
        if ($student->enrollments()->where('formation_id', $formation->id)->exists()) {
            return;
        }
        $debut = $promotion->starts_on->copy()->max(now()->startOfDay());
        $student->enrollments()->create(['formation_id' => $formation->id, 'promotion_id' => $promotion->id, 'mode' => $mode]);
        $student->schedules()->create(['formation_id' => $formation->id, 'label' => 'Acompte '.$formation->code, 'amount' => $formation->deposit, 'due_on' => now()->toDateString()]);
        $student->schedules()->create(['formation_id' => $formation->id, 'label' => 'Solde '.$formation->code, 'amount' => $formation->balance(),
            'due_on' => $debut->copy()->addDays($formation->balance_after_days)->toDateString()]);
    }

    /**
     * Simulation d'un changement : [action, déjà payé, total actuel, reste actuel, prix ajouté, prix retiré, nouveau total, nouveau reste, avoir].
     * $add / $remove : formations ajoutée et retirée (remplacement = les deux).
     */
    public function preview(Student $student, ?Formation $add, ?Formation $remove): array
    {
        $student->loadMissing('schedules', 'payments');
        $paid = $student->totalPaid();
        $total = $student->totalDue();
        $removed = $remove ? (int) $student->schedules->where('formation_id', $remove->id)->sum('amount') : 0;
        $added = $add ? (int) $add->price : 0;
        $newTotal = $total - $removed + $added;

        return ['paid' => $paid, 'total' => $total, 'balance' => max(0, $total - $paid), 'added' => $added, 'removed' => $removed,
            'newTotal' => $newTotal, 'newBalance' => max(0, $newTotal - $paid), 'credit' => max(0, $paid - $newTotal)];
    }

    /** Ajoute et/ou retire une formation en une seule opération, puis prévient l'étudiant. */
    public function change(Student $student, ?Formation $add, ?Formation $remove, ?Promotion $promotion = null, string $mode = 'En ligne'): array
    {
        $student->loadMissing('formations', 'user');
        if (! $add && ! $remove) {
            throw ValidationException::withMessages(['inscription' => 'Choisissez une formation à ajouter ou à retirer.']);
        }
        if ($add && $student->formations->contains('id', $add->id)) {
            throw ValidationException::withMessages(['nouvelle' => 'L’étudiant est déjà inscrit à cette formation.']);
        }
        if ($remove && ! $student->formations->contains('id', $remove->id)) {
            throw ValidationException::withMessages(['ancienne' => 'L’étudiant n’est pas inscrit à cette formation.']);
        }
        if ($remove && ! $add && $student->formations->count() <= 1) {
            throw ValidationException::withMessages(['ancienne' => 'Un étudiant doit rester inscrit à au moins une formation : utilisez « Remplacer ».']);
        }
        $before = $this->preview($student, $add, $remove);
        $promotion ??= $student->promotion ?? Promotion::orderByDesc('starts_on')->firstOrFail();

        DB::transaction(function () use ($student, $add, $remove, $promotion, $mode) {
            if ($remove) {
                $student->schedules()->where('formation_id', $remove->id)->delete();
                $student->enrollments()->where('formation_id', $remove->id)->delete();
            }
            if ($add) {
                $this->enroll($student, $add, $promotion, $mode);
            }
        });
        $student->refreshAllocation();
        $student->unsetRelation('formations');
        $student->unsetRelation('enrollments');

        $label = match (true) {
            $add && $remove => 'Formation remplacée : '.$remove->code.' → '.$add->code,
            (bool) $add => 'Formation ajoutée : '.$add->code,
            default => 'Formation retirée : '.$remove->code,
        };
        AuditLog::record($label, $student->matricule.' — nouveau total '.fcfa($before['newTotal']).', déjà réglé '.fcfa($before['paid']).', reste '.fcfa($before['newBalance']));

        $student->user->notify(new Avis('Votre inscription a été mise à jour', $label.'. Reste à régler : '.fcfa($before['newBalance']).'.', route('etudiant.paiements'),
            'fa-graduation-cap', 'info', true, mailData: [
                'titre' => 'Votre inscription a été mise à jour', 'categorie' => 'VOTRE INSCRIPTION',
                'introduction' => match (true) {
                    $add && $remove => 'Votre inscription a été transférée de « '.$remove->name.' » vers « '.$add->name.' ». Les sommes déjà versées restent acquises et sont reportées sur votre nouvel échéancier.',
                    (bool) $add => 'Vous êtes désormais également inscrit(e) à la formation « '.$add->name.' ». Votre échéancier a été complété.',
                    default => 'Votre inscription à la formation « '.$remove->name.' » a été retirée. Votre échéancier a été ajusté.',
                },
                'libelleMiseEnAvant' => 'RESTE À RÉGLER', 'miseEnAvant' => fcfa($before['newBalance']),
                'details' => array_filter(['Matricule' => $student->matricule, 'Formation ajoutée' => $add ? $add->name.' — '.fcfa($add->price) : null,
                    'Formation retirée' => $remove?->name, 'Total de vos formations' => fcfa($before['newTotal']), 'Déjà réglé' => fcfa($before['paid']),
                    'Avoir en votre faveur' => $before['credit'] ? fcfa($before['credit']) : null]),
                'actionLabel' => 'Voir mon échéancier',
            ]));
        if ($add) {
            app(ProjectService::class)->assignNextAuto($student->fresh(['formations', 'user']), $add->id);
        }

        return $before;
    }
}
