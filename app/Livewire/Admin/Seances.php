<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Promotion;
use App\Models\Seance;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Seances extends Page
{
    use WithPagination;

    #[Url(except: '')]
    public string $formation = '';

    #[Url(except: 'avenir')]
    public string $vue = 'avenir';

    public bool $form = false;

    public ?int $editId = null;

    public array $s = [];

    public function updated($prop): void
    {
        if (in_array($prop, ['formation', 'vue'])) {
            $this->resetPage();
        }
    }

    public function creer(): void
    {
        $f = Formation::active()->first();
        $this->s = ['formation_id' => $f?->id, 'promotion_id' => Promotion::current()?->id, 'trainer_id' => Trainer::where('formation_id', $f?->id)->value('user_id'),
            'title' => '', 'module' => '', 'date' => now()->addDay()->toDateString(), 'heure' => '18:00', 'duree' => 2, 'mode' => 'virtuel', 'meet_url' => '', 'room' => ''];
        $this->editId = null;
        $this->form = true;
    }

    public function modifier(int $id): void
    {
        $x = Seance::findOrFail($id);
        $this->s = ['formation_id' => $x->formation_id, 'promotion_id' => $x->promotion_id, 'trainer_id' => $x->trainer_id, 'title' => $x->title, 'module' => $x->module,
            'date' => $x->starts_at->toDateString(), 'heure' => $x->starts_at->format('H:i'), 'duree' => round($x->starts_at->diffInMinutes($x->ends_at) / 60, 1),
            'mode' => $x->mode, 'meet_url' => $x->meet_url, 'room' => $x->room];
        $this->editId = $id;
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $this->validate([
            's.formation_id' => 'required|exists:formations,id', 's.promotion_id' => 'nullable|exists:promotions,id', 's.trainer_id' => 'nullable|exists:users,id',
            's.title' => 'required|string|max:150', 's.module' => 'nullable|string|max:120', 's.date' => 'required|date', 's.heure' => 'required|date_format:H:i',
            's.duree' => 'required|numeric|min:0.5|max:8', 's.mode' => 'required|in:virtuel,presentiel', 's.meet_url' => 'nullable|url|max:255', 's.room' => 'nullable|string|max:120',
        ], [], ['s.title' => 'titre', 's.meet_url' => 'lien Google Meet']);
        $start = Carbon::parse($this->s['date'].' '.$this->s['heure']);
        $data = collect($this->s)->only(['formation_id', 'promotion_id', 'trainer_id', 'title', 'module', 'mode', 'meet_url', 'room'])->map(fn ($v) => $v === '' ? null : $v)->all()
            + ['starts_at' => $start, 'ends_at' => $start->copy()->addMinutes((int) round($this->s['duree'] * 60))];
        $x = Seance::updateOrCreate(['id' => $this->editId], $data);
        AuditLog::record($this->editId ? 'Séance modifiée' : 'Séance créée', $x->title);
        $this->form = false;
        $this->toast('Séance enregistrée.');
    }

    public function annuler(int $id): void
    {
        $x = Seance::findOrFail($id);
        $x->update(['cancelled' => ! $x->cancelled]);
        if ($x->cancelled) {
            $users = User::whereIn('id', $x->expectedStudents()->pluck('user_id'))->get();
            $users->each->notify(new Avis('Séance annulée', $x->title.' du '.$x->starts_at->translatedFormat('l j F à H\hi').' est annulée.', route('etudiant.cours'), 'fa-calendar-xmark', 'bad', true));
        }
        AuditLog::record($x->cancelled ? 'Séance annulée' : 'Séance rétablie', $x->title);
        $this->toast($x->cancelled ? 'Séance annulée : les étudiants sont prévenus.' : 'Séance rétablie.', $x->cancelled ? 'warn' : 'ok');
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        $items = Seance::with('formation', 'trainer', 'promotion')->withCount('presences')
            ->when($this->formation, fn ($q) => $q->where('formation_id', $this->formation))
            ->when($this->vue === 'avenir', fn ($q) => $q->where('ends_at', '>=', now())->orderBy('starts_at'), fn ($q) => $q->where('ends_at', '<', now())->orderByDesc('starts_at'))
            ->paginate(20);
        $trainers = User::role('formateur')->orderBy('name')->get();

        return $this->page('livewire.admin.seances', ['items' => $items, 'formations' => Formation::active()->get(), 'promotions' => Promotion::orderByDesc('starts_on')->get(), 'trainers' => $trainers],
            'Séances', 'Administration <span class="sep">›</span> Scolarité <span class="sep">›</span> Séances',
            '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouvelle séance</button>');
    }
}
