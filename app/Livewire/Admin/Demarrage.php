<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Promotion;
use App\Models\StarterTool;
use App\Models\VirtualClass;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

/** Classes virtuelles (liens WhatsApp…) et outils de démarrage (logiciels, supports, programmes) affichés aux étudiants. */
class Demarrage extends Page
{
    use WithFileUploads;

    #[Url(except: 'classes')]
    public string $onglet = 'classes';

    public bool $classeForm = false;

    public ?int $classeId = null;

    public array $c = [];

    public bool $outilForm = false;

    public ?int $outilId = null;

    public array $o = [];

    public $fichier = null;

    public function mount(): void
    {
        abort_unless(auth()->user()->can('admin.bibliotheque'), 403);
    }

    /* ---------- Classes virtuelles ---------- */
    public function nouvelleClasse(): void
    {
        $this->reset('classeId');
        $this->resetErrorBag();
        $this->c = ['formation_id' => Formation::active()->value('id'), 'promotion_id' => '', 'name' => '', 'url' => '', 'schedule' => '', 'description' => '', 'active' => true];
        $this->classeForm = true;
    }

    public function modifierClasse(int $id): void
    {
        $v = VirtualClass::findOrFail($id);
        $this->resetErrorBag();
        $this->classeId = $v->id;
        $this->c = ['formation_id' => $v->formation_id, 'promotion_id' => (string) $v->promotion_id, 'name' => $v->name, 'url' => $v->url,
            'schedule' => (string) $v->schedule, 'description' => (string) $v->description, 'active' => $v->active];
        $this->classeForm = true;
    }

    public function enregistrerClasse(): void
    {
        $data = $this->validate([
            'c.formation_id' => 'required|exists:formations,id', 'c.promotion_id' => 'nullable|exists:promotions,id',
            'c.name' => 'required|string|max:120', 'c.url' => ['required', 'url:https', 'max:500'],
            'c.schedule' => 'nullable|string|max:160', 'c.description' => 'nullable|string|max:1000', 'c.active' => 'boolean',
        ], ['c.url.url' => 'Indiquez un lien complet en https (ex. https://chat.whatsapp.com/…).'],
            ['c.formation_id' => 'formation', 'c.name' => 'nom', 'c.url' => 'lien'])['c'];
        $data['promotion_id'] = $data['promotion_id'] ?: null;
        $v = $this->classeId ? VirtualClass::findOrFail($this->classeId) : new VirtualClass(['position' => (int) VirtualClass::max('position') + 1]);
        $v->fill($data)->save();
        AuditLog::record($this->classeId ? 'Classe virtuelle modifiée' : 'Classe virtuelle ajoutée', $v->name);
        $this->classeForm = false;
        $this->toast('Classe virtuelle enregistrée.');
    }

    public function basculerClasse(int $id): void
    {
        $v = VirtualClass::findOrFail($id);
        $v->update(['active' => ! $v->active]);
        $this->toast($v->active ? 'Classe visible par les étudiants.' : 'Classe masquée.');
    }

    public function supprimerClasse(int $id): void
    {
        $v = VirtualClass::findOrFail($id);
        AuditLog::record('Classe virtuelle supprimée', $v->name);
        $v->delete();
        $this->toast('Classe virtuelle supprimée.', 'info');
    }

    /* ---------- Outils de démarrage ---------- */
    public function nouvelOutil(string $kind = 'logiciel'): void
    {
        $this->reset('outilId', 'fichier');
        $this->resetErrorBag();
        $this->o = ['kind' => array_key_exists($kind, StarterTool::KINDS) ? $kind : 'logiciel', 'formation_id' => '', 'title' => '', 'description' => '', 'version' => '', 'url' => '', 'published' => true];
        $this->outilForm = true;
    }

    public function modifierOutil(int $id): void
    {
        $t = StarterTool::findOrFail($id);
        $this->reset('fichier');
        $this->resetErrorBag();
        $this->outilId = $t->id;
        $this->o = ['kind' => $t->kind, 'formation_id' => (string) $t->formation_id, 'title' => $t->title, 'description' => (string) $t->description,
            'version' => (string) $t->version, 'url' => (string) $t->url, 'published' => $t->published];
        $this->outilForm = true;
    }

    public function enregistrerOutil(): void
    {
        $existing = $this->outilId ? StarterTool::findOrFail($this->outilId) : null;
        $data = $this->validate([
            'o.kind' => ['required', Rule::in(array_keys(StarterTool::KINDS))], 'o.formation_id' => 'nullable|exists:formations,id',
            'o.title' => 'required|string|max:160', 'o.description' => 'nullable|string|max:1500', 'o.version' => 'nullable|string|max:60',
            'o.url' => ['nullable', 'url:https', 'max:500'], 'o.published' => 'boolean',
            'fichier' => 'nullable|file|max:'.StarterTool::MAX_KB.'|mimes:'.StarterTool::MIMES,
        ], ['o.url.url' => 'Indiquez un lien complet en https.'], ['o.title' => 'titre', 'o.url' => 'lien', 'fichier' => 'fichier'])['o'];
        if (! $this->fichier && ! ($existing?->path) && empty($data['url'])) {
            $this->addError('fichier', 'Ajoutez un fichier ou un lien de téléchargement.');

            return;
        }
        $data = array_map(fn ($v) => is_string($v) ? (trim($v) ?: null) : $v, $data);
        $t = $existing ?? new StarterTool(['position' => (int) StarterTool::max('position') + 1]);
        if ($this->fichier) {
            if ($t->path) {
                Storage::disk('local')->delete($t->path);
            }
            $data += ['path' => $this->fichier->store('demarrage', 'local'), 'original_name' => $this->fichier->getClientOriginalName(), 'size' => $this->fichier->getSize()];
        }
        $t->fill($data)->save();
        AuditLog::record($existing ? 'Outil de démarrage modifié' : 'Outil de démarrage ajouté', $t->title);
        $this->outilForm = false;
        $this->reset('fichier');
        $this->toast('Outil enregistré.');
    }

    public function retirerFichierOutil(): void
    {
        $t = StarterTool::findOrFail($this->outilId);
        if (! $t->url) {
            $this->addError('fichier', 'Ajoutez d’abord un lien : l’outil doit rester téléchargeable.');

            return;
        }
        Storage::disk('local')->delete((string) $t->path);
        $t->update(['path' => null, 'original_name' => null, 'size' => null]);
        $this->toast('Fichier retiré ; le lien de téléchargement est conservé.');
    }

    public function basculerOutil(int $id): void
    {
        $t = StarterTool::findOrFail($id);
        $t->update(['published' => ! $t->published]);
        $this->toast($t->published ? 'Outil publié.' : 'Outil masqué.');
    }

    public function supprimerOutil(int $id): void
    {
        $t = StarterTool::findOrFail($id);
        if ($t->path) {
            Storage::disk('local')->delete($t->path);
        }
        AuditLog::record('Outil de démarrage supprimé', $t->title);
        $t->delete();
        $this->toast('Outil supprimé.', 'info');
    }

    public function closeModal(): void
    {
        $this->classeForm = $this->outilForm = false;
        $this->reset('fichier');
    }

    public function render()
    {
        return $this->page('livewire.admin.demarrage', [
            'classes' => VirtualClass::with('formation', 'promotion')->orderBy('formation_id')->orderBy('position')->get(),
            'tools' => StarterTool::with('formation')->orderBy('position')->orderBy('title')->get()->groupBy('kind'),
            'formations' => Formation::active()->orderBy('position')->get(),
            'promotions' => Promotion::orderByDesc('starts_on')->get(),
        ], 'Démarrage et classes virtuelles', 'Administration <span class="sep">›</span> Contenus <span class="sep">›</span> Démarrage',
            $this->onglet === 'outils' ? '<button class="btn btn-primary t-cta" wire:click="nouvelOutil"><i class="fa-solid fa-plus"></i>Ajouter un outil</button>'
                : '<button class="btn btn-primary t-cta" wire:click="nouvelleClasse"><i class="fa-solid fa-plus"></i>Ajouter une classe</button>');
    }
}
