<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\Formation;
use App\Models\Student;
use App\Notifications\CertificatDelivre;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegistreService
{
    public function __construct(private RegistreClient $client) {}

    public function transmettre(Student $student, Formation $formation, string $appreciation): CertificateRequest
    {
        abort_unless($student->formations->contains('id', $formation->id), 422);
        if ($student->certificates()->where('formation_id', $formation->id)->exists()) {
            throw ValidationException::withMessages(['registre' => 'Un certificat existe déjà pour cette formation. Aucun ancien certificat ne sera remplacé automatiquement.']);
        }
        $request = CertificateRequest::firstOrCreate(['student_id' => $student->id, 'formation_id' => $formation->id], ['appreciation' => $appreciation]);

        return DB::transaction(function () use ($request, $student, $formation) {
            $request = CertificateRequest::lockForUpdate()->findOrFail($request->id);
            if ($request->registry_document_id) {
                return $this->synchroniser($request);
            }
            $data = $this->client->json('post', 'dossiers', [
                'matricule' => $student->matricule, 'nom' => $student->user->name, 'email' => $student->user->email,
                'promotion' => $student->promotion?->name, 'type' => 'certificat', 'formation' => $formation->code,
                'criteres' => $student->certificationChecks($formation),
                'commentaire' => 'Dossier transmis depuis MY EVC. Les validations pédagogiques et de direction restent à effectuer dans le registre.',
                'details' => ['mention' => $request->appreciation],
            ]);

            return $this->appliquer($request, $data);
        });
    }

    public function synchroniser(CertificateRequest $request): CertificateRequest
    {
        if (! $request->registry_document_id) {
            return $request;
        }
        $data = $this->client->json('get', 'dossiers/'.$request->registry_document_id, ['matricule' => $request->student->matricule]);

        return $this->appliquer($request, $data);
    }

    private function appliquer(CertificateRequest $request, array $data): CertificateRequest
    {
        $valid = Validator::make($data, [
            'id' => 'required|integer|min:1', 'type' => ['required', Rule::in(['certificat'])],
            'formation' => ['required', Rule::in([$request->formation->code])],
            'matricule' => ['required', Rule::in([$request->student->matricule])],
            'statut' => ['required', Rule::in(array_diff(array_keys(CertificateRequest::STATUSES), ['a_transmettre']))],
            'reference' => 'nullable|string|max:30|regex:/^EVC-[A-Z0-9]+-[0-9]{4}-[0-9]{4,}$/',
            'delivre_le' => 'nullable|date_format:Y-m-d', 'mention' => 'nullable|string|max:255',
            'pdf_disponible' => 'required|boolean', 'pdf_sha256' => 'nullable|regex:/^[a-f0-9]{64}$/',
            'url_verification' => 'nullable|url:http,https|max:1000',
        ]);
        if ($valid->fails() || ($request->registry_document_id && $request->registry_document_id !== (int) ($data['id'] ?? 0))) {
            throw ValidationException::withMessages(['registre' => 'Le dossier retourné par le registre ne correspond pas à l’étudiant et à la formation sélectionnés.']);
        }
        if ($data['statut'] === 'publie' && (empty($data['reference']) || empty($data['delivre_le']) || empty($data['pdf_sha256']) || ! $data['pdf_disponible'])) {
            throw ValidationException::withMessages(['registre' => 'Le registre doit publier un PDF officiel complet avant sa mise à disposition.']);
        }
        DB::transaction(function () use ($request, $data) {
            $locked = CertificateRequest::lockForUpdate()->findOrFail($request->id);
            if ($locked->certificate_id && $locked->certificate->code !== ($data['reference'] ?? null)) {
                throw ValidationException::withMessages(['registre' => 'La référence du document a changé. Un contrôle manuel est nécessaire.']);
            }
            $locked->fill([
                'registry_document_id' => $data['id'], 'status' => $data['statut'], 'reference' => $data['reference'] ?? null,
                'pdf_sha256' => $data['pdf_sha256'] ?? null, 'verification_url' => $data['url_verification'] ?? null, 'synced_at' => now(),
            ]);
            if ($data['statut'] === 'publie' && ! $locked->certificate_id) {
                if (Certificate::where('student_id', $locked->student_id)->where('formation_id', $locked->formation_id)->exists()) {
                    throw ValidationException::withMessages(['registre' => 'Un ancien certificat local existe. Un rapprochement manuel est nécessaire.']);
                }
                $certificate = Certificate::create(['student_id' => $locked->student_id, 'formation_id' => $locked->formation_id,
                    'code' => $data['reference'], 'issued_on' => $data['delivre_le'], 'appreciation' => ($data['mention'] ?? null) ?: $locked->appreciation]);
                $locked->certificate_id = $certificate->id;
                $student = $locked->student;
                $student->enrollments()->where('formation_id', $locked->formation_id)->update(['status' => 'terminee']);
                if ($student->formations->every(fn ($f) => $student->certificates()->where('formation_id', $f->id)->exists())) {
                    $student->update(['status' => 'certifie']);
                }
            }
            $locked->save();
        });
        DB::transaction(function () use ($request) {
            $locked = CertificateRequest::lockForUpdate()->findOrFail($request->id);
            if ($locked->status === 'publie' && $locked->certificate_id && ! $locked->notified_at) {
                $locked->student->user->notify(new CertificatDelivre($locked->certificate));
                $locked->update(['notified_at' => now()]);
            }
        });

        return $request->fresh();
    }

    public function pdf(CertificateRequest $request): string
    {
        $request = $this->synchroniser($request);
        abort_unless($request->status === 'publie', 409, 'Ce certificat n’est pas publié ou a été révoqué.');
        $response = $this->client->request('get', 'documents/'.rawurlencode($request->reference).'/pdf', ['matricule' => $request->student->matricule]);
        $bytes = $response->body();
        abort_unless(str_starts_with($bytes, '%PDF') && hash_equals($request->pdf_sha256, hash('sha256', $bytes)), 409, 'L’intégrité du PDF officiel n’a pas pu être vérifiée.');

        return $bytes;
    }

    public function synchroniserTout(): array
    {
        $counts = ['ok' => 0, 'echecs' => 0];
        foreach (CertificateRequest::whereNotNull('registry_document_id')->lazyById() as $request) {
            try {
                $this->synchroniser($request);
                $counts['ok']++;
            } catch (ValidationException|\Symfony\Component\Mailer\Exception\TransportExceptionInterface) {
                $counts['echecs']++;
            }
        }

        return $counts;
    }
}
