<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\RecoProject;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

/** RECO 60 : 60 jours pour mener un projet professionnel réel (CM, GIA). */
class Reco extends Page
{
    use WithFileUploads;

    #[Url(as: 'formation', except: '')]
    public string $code = '';

    public array $creation = ['name' => '', 'sector' => '', 'description' => '', 'objectives' => '', 'channels' => ''];

    public bool $journal = false;

    public array $e = [];

    public $preuve;

    public bool $editKpi = false;

    public array $kpis = [];

    public $rapport;

    private function formationsReco()
    {
        return $this->student()->formations->where('has_reco', true)->values();
    }

    private function formation()
    {
        $fs = $this->formationsReco();

        return $fs->firstWhere('code', $this->code) ?? $fs->first();
    }

    private function projet(): ?RecoProject
    {
        $f = $this->formation();

        return $f ? RecoProject::where('student_id', $this->student()->id)->where('formation_id', $f->id)->first() : null;
    }

    public function creer(): void
    {
        $f = $this->formation();
        abort_unless($f && ! $this->projet(), 422);
        $this->validate(['creation.name' => 'required|string|max:120', 'creation.sector' => 'nullable|string|max:80', 'creation.description' => 'required|string|max:2000',
            'creation.objectives' => 'nullable|string|max:2000', 'creation.channels' => 'nullable|string|max:255'],
            [], ['creation.name' => 'nom du projet', 'creation.description' => 'description']);
        $steps = collect(RecoProject::TEMPLATES[$f->code] ?? RecoProject::TEMPLATES['default'])->map(fn ($s, $i) => $s + ['done' => $i === 0])->all();
        RecoProject::create($this->creation + ['student_id' => $this->student()->id, 'formation_id' => $f->id, 'steps' => $steps, 'kpis' => [], 'started_on' => now()->toDateString()]);
        $this->notifierFormateurs($f->id, 'Nouveau RECO 60', $this->student()->name.' a lancé « '.$this->creation['name'].' ».');
        $this->toast('Votre RECO 60 est lancé : jour 1 sur 60 !');
    }

    public function basculerEtape(int $i): void
    {
        $p = $this->projetModifiable();
        $steps = $p->steps;
        $steps[$i]['done'] = ! ($steps[$i]['done'] ?? false);
        $p->update(['steps' => $steps]);
    }

    public function ouvrirJournal(): void
    {
        $this->projetModifiable();
        $this->e = ['entry_date' => now()->toDateString(), 'action' => '', 'result' => '', 'hours' => 1, 'link' => ''];
        $this->reset('preuve');
        $this->resetErrorBag();
        $this->journal = true;
    }

    public function ajouterEntree(): void
    {
        $p = $this->projetModifiable();
        $this->validate(['e.entry_date' => 'required|date|before_or_equal:today|after_or_equal:'.$p->started_on->toDateString(), 'e.action' => 'required|string|max:200',
            'e.result' => 'nullable|string|max:2000', 'e.hours' => 'required|numeric|min:0|max:24', 'e.link' => 'nullable|url|max:255', 'preuve' => 'nullable|file|max:20480'],
            [], ['e.action' => 'action menée', 'e.entry_date' => 'date', 'e.link' => 'lien', 'preuve' => 'preuve']);
        $p->entries()->create($this->e + ['file_path' => $this->preuve?->store('reco/'.$p->id, 'local'), 'file_name' => $this->preuve?->getClientOriginalName()]);
        $this->journal = false;
        $this->toast('Activité ajoutée au journal.');
    }

    public function ouvrirKpi(): void
    {
        $this->kpis = $this->projetModifiable()->kpis ?: [['l' => '', 'v' => '']];
        $this->editKpi = true;
    }

    public function ajouterKpi(): void
    {
        $this->kpis[] = ['l' => '', 'v' => ''];
    }

    public function enregistrerKpi(): void
    {
        $this->validate(['kpis.*.l' => 'required|string|max:40', 'kpis.*.v' => 'required|string|max:30'], [], ['kpis.*.l' => 'indicateur', 'kpis.*.v' => 'valeur']);
        $this->projetModifiable()->update(['kpis' => array_values($this->kpis)]);
        $this->editKpi = false;
        $this->toast('Indicateurs mis à jour.');
    }

    public function rendreRapport(): void
    {
        $p = $this->projetModifiable();
        $this->validate(['rapport' => 'required|file|max:20480|mimes:pdf,doc,docx,ppt,pptx'], [], ['rapport' => 'rapport final']);
        $p->update(['report_path' => $this->rapport->store('reco/'.$p->id, 'local'), 'report_name' => $this->rapport->getClientOriginalName(), 'status' => 'rendu', 'submitted_at' => now(),
            'steps' => collect($p->steps)->map(fn ($s) => array_merge($s, ['done' => true]))->all()]);
        $this->reset('rapport');
        $this->notifierFormateurs($p->formation_id, 'Rapport RECO 60 rendu', $this->student()->name.' a rendu le rapport final de « '.$p->name.' ».');
        $this->toast('Rapport final rendu. Votre formateur va l\'évaluer.');
    }

    public function closeModal(): void
    {
        $this->journal = $this->editKpi = false;
    }

    private function projetModifiable(): RecoProject
    {
        $p = $this->projet();
        abort_unless($p && $p->status === 'en_cours', 422, 'Ce RECO 60 n\'est plus modifiable.');

        return $p;
    }

    private function notifierFormateurs(int $formationId, string $title, string $body): void
    {
        User::whereIn('id', Trainer::where('formation_id', $formationId)->pluck('user_id'))->get()
            ->each->notify(new Avis($title, $body, route('formateur.reco'), 'fa-hourglass-half', 'info'));
    }

    public function render()
    {
        $s = $this->student();
        $fs = $this->formationsReco();
        $f = $this->formation();
        $p = $this->projet()?->load('entries.feedbackAuthor', 'evaluator');

        return $this->page('livewire.etudiant.reco', compact('s', 'fs', 'f', 'p'), 'RECO 60', 'Espace étudiant <span class="sep">›</span> RECO 60');
    }
}
