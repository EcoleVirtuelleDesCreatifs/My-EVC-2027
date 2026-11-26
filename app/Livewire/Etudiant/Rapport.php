<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\FinalReport;
use App\Services\FinalReportService;
use Livewire\WithFileUploads;

/** Rapport de fin de formation : un fichier par formation, relu par l'équipe pédagogique. */
class Rapport extends Page
{
    use WithFileUploads;

    public ?int $formationId = null;

    public $fichier = null;

    public string $message = '';

    public function mount(): void
    {
        $s = $this->student();
        $valid = $s->finalReports()->where('status', 'valide')->pluck('formation_id');
        $this->formationId = $s->formations->first(fn ($f) => ! $valid->contains($f->id))?->id ?? $s->formations->first()?->id;
    }

    public function deposer(FinalReportService $svc): void
    {
        $s = $this->student();
        $this->validate([
            'formationId' => 'required|in:'.$s->formations->pluck('id')->implode(','),
            'fichier' => 'required|file|max:'.FinalReport::MAX_KB.'|mimes:'.FinalReport::MIMES,
            'message' => 'nullable|string|max:2000',
        ], ['fichier.required' => 'Joignez votre rapport.', 'fichier.mimes' => 'Formats acceptés : PDF, Word, OpenDocument ou PowerPoint.',
            'fichier.max' => 'Le rapport ne doit pas dépasser 20 Mo.'], ['fichier' => 'rapport']);
        $svc->submit($s, $s->formations->firstWhere('id', $this->formationId), $this->fichier, trim($this->message) ?: null);
        $this->reset('fichier', 'message');
        $this->toast('Rapport déposé. L’équipe pédagogique est prévenue et va le relire.');
    }

    public function render()
    {
        $s = $this->student()->load('promotion');
        $reports = $s->finalReports()->with('reviewer')->get()->keyBy('formation_id');

        return $this->page('livewire.etudiant.rapport', ['s' => $s, 'reports' => $reports], 'Rapport de fin de formation', 'Espace étudiant <span class="sep">›</span> Rapport de fin de formation');
    }
}
