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
            route('etudiant.paiements'), 'fa-receipt', 'ok', true));

        return $payment;
    }
}
