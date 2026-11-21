<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Certificate;
use App\Models\Formation;
use App\Models\Student;
use App\Notifications\Avis;
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
        $this->delivrer = $studentId.'-'.$formationId;
        $this->appreciation = 'Très bien';
    }

    public function confirmer(): void
    {
        $this->validate(['appreciation' => 'required|in:Excellent,Très bien,Bien,Acquis']);
        [$sid, $fid] = array_map('intval', explode('-', (string) $this->delivrer));
        $s = Student::with('user', 'formations', 'certificates')->findOrFail($sid);
        $f = Formation::findOrFail($fid);
        abort_unless($s->formations->contains('id', $fid), 422);
        $c = Certificate::firstOrCreate(['student_id' => $sid, 'formation_id' => $fid], ['code' => Certificate::nextCode(), 'appreciation' => $this->appreciation, 'issued_on' => now()]);
        $s->enrollments()->where('formation_id', $fid)->update(['status' => 'terminee']);
        if ($s->formations->every(fn ($x) => $s->certificates()->where('formation_id', $x->id)->exists())) {
            $s->update(['status' => 'certifie']);
        }
        $s->user->notify(new Avis('Certificat délivré', 'Félicitations ! Votre certificat « '.$f->name.' » est disponible ('.$c->code.').', route('etudiant.certificat'), 'fa-award', 'ok', true));
        AuditLog::record('Certificat délivré', $c->code.' — '.$s->user->name.' ('.$f->code.')');
        $this->delivrer = null;
        $this->toast('Certificat '.$c->code.' délivré.');
    }

    public function closeModal(): void
    {
        $this->delivrer = null;
    }

    public function render()
    {
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
                    $eligibles->push(['s' => $s, 'f' => $f, 'checks' => $checks, 'ok' => collect($checks)->filter()->count()]);
                }
            }
            $eligibles = $eligibles->sortByDesc('ok')->values();
        }
        $issued = Certificate::with('student.user', 'formation')->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))->latest('issued_on')->get();

        return $this->page('livewire.admin.certificats', ['eligibles' => $eligibles, 'issued' => $issued, 'formations' => Formation::active()->get()], 'Certificats', 'Administration <span class="sep">›</span> Scolarité <span class="sep">›</span> Certificats');
    }
}
