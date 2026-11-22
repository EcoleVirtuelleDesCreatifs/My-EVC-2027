<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/** Documents PDF : reçu de paiement, certificat, attestation d'inscription. */
class PdfController extends Controller
{
    public function receipt(Request $request, Payment $payment)
    {
        $this->authorizeStudent($request, $payment->student, 'admin.finances');
        $payment->load('student.user', 'student.formations', 'student.schedules', 'student.payments', 'recorder');

        return $this->pdf('pdf.recu', ['p' => $payment], 'recu-'.$payment->receipt_number);
    }

    public function certificate(Request $request, Certificate $certificate)
    {
        $this->authorizeStudent($request, $certificate->student, 'admin.certificats');
        $certificate->load('student.user', 'formation', 'registryRequest');
        if ($certificate->registryRequest) {
            try {
                $bytes = app(\App\Services\RegistreService::class)->pdf($certificate->registryRequest);
            } catch (\Illuminate\Validation\ValidationException) {
                abort(503, 'Le registre officiel est temporairement indisponible. Réessayez plus tard.');
            }

            return response($bytes, 200, ['Content-Type' => 'application/pdf', 'Cache-Control' => 'no-store, private',
                'Content-Disposition' => 'attachment; filename="certificat-'.$certificate->code.'.pdf"']);
        }

        return $this->pdf('pdf.certificat', ['c' => $certificate], 'certificat-'.$certificate->code, 'landscape');
    }

    public function attestation(Request $request, Student $student)
    {
        $this->authorizeStudent($request, $student, 'admin.etudiants');
        $student->load('user', 'formations', 'promotion');

        return $this->pdf('pdf.attestation', ['s' => $student], 'attestation-'.$student->matricule);
    }

    private function authorizeStudent(Request $request, Student $student, string $permission): void
    {
        $u = $request->user();
        abort_unless($u->id === $student->user_id || $u->can($permission), 403);
    }

    private function pdf(string $view, array $data, string $name, string $orientation = 'portrait')
    {
        $data += ['director' => Setting::get('director'), 'email' => Setting::get('school_email'), 'phone' => Setting::get('phone'), 'address' => Setting::get('address'), 'rccm' => Setting::get('rccm')];

        return Pdf::loadView($view, $data)->setPaper('a4', $orientation)->stream($name.'.pdf');
    }
}
