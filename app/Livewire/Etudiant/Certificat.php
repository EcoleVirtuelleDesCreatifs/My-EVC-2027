<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;

class Certificat extends Page
{
    public function actualiserRegistre(): void
    {
        $requests = \App\Models\CertificateRequest::where('student_id', $this->student()->id)->whereNotNull('registry_document_id')->get();
        foreach ($requests as $request) {
            app(\App\Services\RegistreService::class)->synchroniser($request);
        }
        $this->toast('Le suivi du registre a été actualisé.');
    }

    public function render()
    {
        $s = $this->student()->load('certificates');
        $requests = \App\Models\CertificateRequest::where('student_id', $s->id)->get()->keyBy('formation_id');
        $rows = $s->formations->map(fn ($f) => ['f' => $f, 'checks' => $s->certificationChecks($f), 'cert' => $s->certificates->firstWhere('formation_id', $f->id), 'request' => $requests->get($f->id)]);

        return $this->page('livewire.etudiant.certificat', compact('s', 'rows'), 'Certificat', 'Espace étudiant <span class="sep">›</span> Certificat');
    }
}
