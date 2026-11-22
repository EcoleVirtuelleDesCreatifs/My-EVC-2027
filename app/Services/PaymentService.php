<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Payment;
use App\Models\Student;
use App\Notifications\Avis;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function record(Student $student, int $amount, string $method, ?string $reference = null, ?Carbon $paidOn = null, ?string $note = null): Payment
    {
        $payment = DB::transaction(function () use ($student, $amount, $method, $reference, $paidOn, $note) {
            $schedule = $student->nextSchedule();

            return Payment::create([
                'receipt_number' => Payment::nextReceiptNumber(), 'student_id' => $student->id,
                'payment_schedule_id' => $schedule?->id, 'amount' => $amount, 'method' => $method,
                'transaction_ref' => $reference, 'paid_on' => ($paidOn ?? now())->toDateString(),
                'recorded_by' => auth()->id(), 'note' => $note,
            ]);
        });

        $student->refreshAllocation();
        AuditLog::record('Paiement enregistré', $payment->receipt_number.' — '.$student->name.' — '.number_format($amount, 0, ',', ' ').' FCFA');
        $student->user->notify(new Avis('Paiement reçu', number_format($amount, 0, ',', ' ').' FCFA reçus par '.$method.'. Reçu '.$payment->receipt_number.' disponible.',
            route('etudiant.paiements'), 'fa-receipt', 'ok', true, mailData: [
                'titre' => 'Votre paiement a bien été enregistré', 'categorie' => 'CONFIRMATION DE PAIEMENT',
                'introduction' => 'Merci pour votre versement. L’administration de l’EVC a enregistré votre paiement. Retrouvez son récapitulatif ci-dessous et votre reçu dans votre espace étudiant.',
                'libelleMiseEnAvant' => 'MONTANT DU VERSEMENT', 'miseEnAvant' => number_format($amount, 0, ',', ' ').' FCFA',
                'details' => array_filter([
                    'Référence du reçu' => $payment->receipt_number, 'Matricule étudiant' => $student->matricule,
                    'Date du versement' => $payment->paid_on->locale('fr')->translatedFormat('j F Y'),
                    'Moyen de paiement' => $method, 'Référence de transaction' => $reference,
                ], fn ($value) => $value !== null && $value !== ''),
                'actionLabel' => 'Consulter mon reçu',
                'etapes' => ['Connectez-vous à votre compte MY EVC.', 'Ouvrez la rubrique Paiements pour consulter votre échéancier et télécharger le reçu PDF correspondant.'],
                'conseil' => 'Conservez votre reçu. Pour toute question ou correction, contactez l’administration via la messagerie MY EVC en précisant la référence du reçu.',
            ]));

        return $payment;
    }
}
