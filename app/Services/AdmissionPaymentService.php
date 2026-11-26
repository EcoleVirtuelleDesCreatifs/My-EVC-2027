<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Candidature;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdmissionPaymentService
{
    public function confirm(Candidature $candidature, array $details, ?int $existingPayment = null): void
    {
        abort_unless(auth()->user()?->can('admin.candidatures'), 403);
        DB::transaction(function () use ($candidature, $details, $existingPayment) {
            $c = Candidature::lockForUpdate()->findOrFail($candidature->id);
            abort_unless(in_array($c->stage, ['test', 'paiement', 'activee'], true), 422);
            if ($c->first_payment_id) {
                return;
            }
            if ($existingPayment) {
                abort_unless($c->student, 422);
                $payment = $c->student->payments()->findOrFail($existingPayment);
                abort_if(Candidature::where('first_payment_id', $payment->id)->whereKeyNot($c->id)->exists(), 422);
                $details = $payment->only('amount', 'method', 'transaction_ref');
                $details['paid_on'] = $payment->paid_on->toDateString();
                $c->first_payment_id = $payment->id;
            } else {
                $details = validator($details, [
                    'amount' => 'required|integer|min:1|max:1000000000', 'method' => ['required', Rule::in(Payment::METHODS)],
                    'paid_on' => 'required|date_format:Y-m-d|before_or_equal:today', 'transaction_ref' => 'nullable|string|max:150',
                ], [], ['amount' => 'montant reçu', 'method' => 'moyen de paiement', 'paid_on' => 'date du paiement'])->validate();
            }
            $c->first_payment = $details + ['confirmed_by' => auth()->id(), 'confirmed_at' => now()->toAtomString()];
            if ($c->stage !== 'activee') {
                $c->stage = 'paiement';
            }
            $c->save();
            if ($c->student) {
                $this->sync($c, $c->student);
            }
            AuditLog::record('Premier versement de candidature validé', $c->reference.' — '.$details['amount'].' FCFA'.($existingPayment ? ' — reçu existant rattaché' : ''));
        });
        $candidature->refresh();
    }

    public function sync(Candidature $candidature, Student $student): ?Payment
    {
        return DB::transaction(function () use ($candidature, $student) {
            $c = Candidature::lockForUpdate()->findOrFail($candidature->id);
            abort_unless($c->student_id === $student->id, 422);
            if ($c->first_payment_id) {
                return $student->payments()->findOrFail($c->first_payment_id);
            }
            if (! $c->first_payment) {
                return null;
            }
            $data = $c->first_payment;
            $payment = app(PaymentService::class)->record($student, (int) $data['amount'], $data['method'],
                $data['transaction_ref'] ?? null, Carbon::parse($data['paid_on']), 'Premier versement validé dans la candidature '.$c->reference);
            $c->update(['first_payment_id' => $payment->id]);

            return $payment;
        });
    }
}
