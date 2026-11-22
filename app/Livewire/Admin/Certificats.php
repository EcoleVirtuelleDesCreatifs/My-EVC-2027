<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Formation;
use App\Models\Student;
use App\Models\CertificateRequest;
use App\Services\RegistreService;
use Livewire\Attributes\Url;

class Certificats extends Page
{
    #[Url(except: 'eligibles')]
    public string $onglet = 'eligibles';

    #[Url(except: '')]
    public string $formation = '';

    public ?string $delivrer = null;   // « studentId-formationId »

    public string $appreciation = 'Très bien';

    public function ouvrir(int $studentId, int $formationId): void
    {
        $s = Student::with('formations')->findOrFail($studentId);
        abort_unless($s->formations->contains('id', $formationId), 422);
        $this->resetErrorBag();
        $this->delivrer = $studentId.'-'.$formationId;
        $this->appreciation = 'Très bien';
    }

    public function confirmer(): void
    {
        $this->validate([
            'delivrer' => ['required', 'regex:/^[1-9][0-9]*-[1-9][0-9]*$/'],
            'appreciation' => 'required|in:Excellent,Très bien,Bien,Acquis',
        ], ['delivrer.required' => 'Sélectionnez un dossier avec « Décision du jury » ou « Délivrer » avant de confirmer.',
            'delivrer.regex' => 'La sélection du dossier est invalide. Veuillez sélectionner à nouveau l’étudiant.']);
        [$sid, $fid] = array_map('intval', explode('-', (string) $this->delivrer));
        $s = Student::with('user', 'formations', 'certificates')->findOrFail($sid);
        $f = Formation::findOrFail($fid);
        abort_unless($s->formations->contains('id', $fid), 422);
        $request = app(RegistreService::class)->transmettre($s, $f, $this->appreciation);
        AuditLog::record('Dossier transmis au registre', $s->matricule.' — '.$f->code.' — dossier '.$request->registry_document_id);
        $this->delivrer = null;
        $this->toast('Dossier transmis au registre. La délivrance et la publication restent soumises aux validations officielles.');
    }

    public function synchroniser(): void
    {
        $counts = app(RegistreService::class)->synchroniserTout();
        $this->toast($counts['ok'].' dossier(s) synchronisé(s).'.($counts['echecs'] ? ' '.$counts['echecs'].' échec(s), vérifiez la connexion au registre.' : ''), $counts['echecs'] ? 'warn' : 'ok');
    }

    public function closeModal(): void
    {
        $this->delivrer = null;
    }

    public function render()
    {
        $requests = CertificateRequest::with('formation')->get()->keyBy(fn ($r) => $r->student_id.'-'.$r->formation_id);
        $eligibles = collect();
        if ($this->onglet === 'eligibles') {
            $students = Student::with(['user', 'formations', 'certificates', 'schedules', 'payments'])->whereIn('status', ['actif', 'certifie'])
                ->when($this->formation, fn ($q) => $q->whereHas('enrollments', fn ($e) => $e->where('formation_id', $this->formation)))->get();
            foreach ($students as $s) {
                foreach ($s->formations as $f) {
                    if (($this->formation && $f->id != $this->formation) || $s->certificates->contains('formation_id', $f->id)) {
                        continue;
                    }
                    $checks = $s->certificationChecks($f);
                    $eligibles->push(['s' => $s, 'f' => $f, 'checks' => $checks, 'ok' => collect($checks)->filter()->count(), 'request' => $requests->get($s->id.'-'.$f->id)]);
                }
            }
            $eligibles = $eligibles->sortByDesc('ok')->values();
        }
        $issued = Certificate::with('student.user', 'formation', 'registryRequest')->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->latest('issued_on')->get();

        return $this->page('livewire.admin.certificats', ['eligibles' => $eligibles, 'issued' => $issued, 'formations' => Formation::active()->get()], 'Certificats', 'Administration <span class="sep">›</span> Scolarité <span class="sep">›</span> Certificats');
    }
}
