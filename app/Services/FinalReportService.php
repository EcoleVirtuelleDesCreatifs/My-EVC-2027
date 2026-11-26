<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\FinalReport;
use App\Models\Formation;
use App\Models\Student;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/** Rapport de fin de formation : dépôt par l'étudiant, relecture (validé / à compléter) par l'équipe. */
class FinalReportService
{
    public function submit(Student $student, Formation $formation, UploadedFile $file, ?string $message): FinalReport
    {
        abort_unless($student->formations->contains('id', $formation->id), 403);
        $report = FinalReport::firstWhere(['student_id' => $student->id, 'formation_id' => $formation->id]);
        if ($report?->status === 'valide') {
            throw ValidationException::withMessages(['rapport' => 'Ce rapport est déjà validé : il ne peut plus être remplacé.']);
        }
        $old = $report?->path;
        $data = ['path' => $file->store('rapports/'.$student->id, 'local'), 'original_name' => $file->getClientOriginalName(), 'size' => $file->getSize(),
            'message' => $message, 'status' => 'depose', 'submitted_at' => now(), 'reviewed_at' => null, 'reviewed_by' => null];
        $report = $report ? tap($report)->update($data + ['version' => $report->version + 1])
            : FinalReport::create($data + ['student_id' => $student->id, 'formation_id' => $formation->id]);
        if ($old && $old !== $report->path) {
            Storage::disk('local')->delete($old);
        }
        AuditLog::record('Rapport de fin de formation déposé', $student->matricule.' — '.$formation->code.' (version '.$report->version.')');
        $this->team($formation)->each->notify(new Avis('Rapport de fin de formation', $student->name.' a déposé son rapport ('.$formation->code.').',
            route('admin.etudiant', ['student' => $student, 'onglet' => 'rapport']), 'fa-file-signature', 'info', true, mailData: [
                'titre' => 'Nouveau rapport de fin de formation', 'categorie' => 'SUIVI PÉDAGOGIQUE',
                'introduction' => $student->name.' a déposé son rapport de fin de formation. Il est prêt pour votre relecture.',
                'libelleMiseEnAvant' => 'FORMATION', 'miseEnAvant' => $formation->name,
                'details' => ['Matricule' => $student->matricule, 'Fichier' => $report->original_name, 'Version' => (string) $report->version],
                'actionLabel' => 'Relire le rapport',
            ]));

        return $report;
    }

    public function review(FinalReport $report, bool $valid, ?string $feedback): void
    {
        if (! $valid && trim((string) $feedback) === '') {
            throw ValidationException::withMessages(['retour' => 'Précisez ce que l’étudiant doit compléter.']);
        }
        $report->update(['status' => $valid ? 'valide' : 'a_reprendre', 'feedback' => $feedback ?: null, 'reviewed_at' => now(), 'reviewed_by' => auth()->id()]);
        $report->loadMissing('student.user', 'formation');
        AuditLog::record($valid ? 'Rapport de fin de formation validé' : 'Rapport de fin de formation à compléter', $report->student->matricule.' — '.$report->formation->code);
        $report->student->user->notify(new Avis($valid ? 'Rapport validé' : 'Rapport à compléter',
            $valid ? 'Votre rapport de fin de formation ('.$report->formation->code.') est validé.' : 'Votre rapport de fin de formation ('.$report->formation->code.') est à compléter : '.$feedback,
            route('etudiant.rapport'), $valid ? 'fa-circle-check' : 'fa-rotate', $valid ? 'ok' : 'warn', true, mailData: [
                'titre' => $valid ? 'Votre rapport de fin de formation est validé' : 'Votre rapport de fin de formation est à compléter',
                'categorie' => 'VOTRE PARCOURS PÉDAGOGIQUE',
                'introduction' => $valid ? 'L’équipe pédagogique de l’EVC a relu et validé votre rapport de fin de formation. Félicitations pour ce travail !'
                    : 'L’équipe pédagogique de l’EVC a relu votre rapport de fin de formation et vous demande de le compléter avant de le déposer à nouveau.',
                'libelleMiseEnAvant' => 'FORMATION', 'miseEnAvant' => $report->formation->name,
                'details' => array_filter(['Fichier relu' => $report->original_name, 'Retour de l’équipe' => $feedback]),
                'actionLabel' => $valid ? 'Voir mon rapport' : 'Déposer une nouvelle version',
            ]));
    }

    /** Destinataires d'un dépôt : formateurs de la formation et administrateurs chargés des étudiants. */
    private function team(Formation $formation)
    {
        return User::whereIn('id', Trainer::where('formation_id', $formation->id)->pluck('user_id'))->get()
            ->merge(User::permission('admin.etudiants')->get())->unique('id');
    }
}
