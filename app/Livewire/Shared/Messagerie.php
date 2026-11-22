<?php

namespace App\Livewire\Shared;

use App\Livewire\Page;
use App\Models\Conversation;
use App\Services\MessagingService;
use Livewire\Attributes\Url;
use Livewire\WithFileUploads;

/** Messagerie interne : étudiants, formateurs et équipe de l'école. */
class Messagerie extends Page
{
    use WithFileUploads;

    #[Url(as: 'c', except: null)]
    public ?int $conversation = null;

    #[\Livewire\Attributes\Locked]
    public int $dernierMessageVu = 0;

    public string $recherche = '';

    public string $texte = '';

    public $piece;

    public bool $nouveau = false;

    public array $destinataires = [];

    public string $objet = '';

    public string $premier = '';

    public string $filtreDest = '';

    public function mount(): void
    {
        if ($this->conversation && ! $this->courante()) {
            $this->conversation = null;
        }
        $this->dernierMessageVu = (int) $this->messagesAccessibles()->max('id');
        $this->actualiser();
    }

    private function messagesAccessibles()
    {
        return \App\Models\Message::whereIn('conversation_id', Conversation::forUser(auth()->user())->reorder()->select('conversations.id'));
    }

    public function actualiser(bool $visible = true): void
    {
        $dernier = (int) $this->messagesAccessibles()->max('id');
        if ($dernier > $this->dernierMessageVu && $this->messagesAccessibles()->where('id', '>', $this->dernierMessageVu)
            ->where('id', '<=', $dernier)->where('user_id', '!=', auth()->id())->exists()) {
            $this->dispatch('message-recu');
        }
        $this->dernierMessageVu = max($this->dernierMessageVu, $dernier);
        if ($visible && ($c = $this->courante())) {
            app(MessagingService::class)->markRead($c, auth()->user());
        }
        $this->dispatch('menu-actualiser');
    }

    private function courante(): ?Conversation
    {
        return $this->conversation ? Conversation::forUser(auth()->user())->find($this->conversation) : null;
    }

    public function ouvrir(int $id): void
    {
        $this->conversation = $id;
        $this->actualiser();
        $this->reset('texte', 'piece');
        $this->resetErrorBag();
    }

    public function fermer(): void
    {
        $this->conversation = null;
    }

    public function envoyer(MessagingService $svc): void
    {
        $c = $this->courante();
        abort_unless($c, 404);
        $this->validate(['texte' => 'required_without:piece|nullable|string|max:5000', 'piece' => 'nullable|file|max:20480'], ['texte.required_without' => 'Écrivez un message ou joignez un fichier.']);
        $svc->send($c, auth()->user(), trim($this->texte) ?: '📎 '.$this->piece->getClientOriginalName(), $this->piece);
        $this->reset('texte', 'piece');
        $this->dispatch('message-envoye');
        $this->dispatch('menu-actualiser');
    }

    public function ouvrirNouveau(?int $userId = null): void
    {
        $this->reset('destinataires', 'objet', 'premier', 'filtreDest');
        if ($userId) {
            $this->destinataires = [$userId];
        }
        $this->resetErrorBag();
        $this->nouveau = true;
    }

    public function demarrer(MessagingService $svc): void
    {
        $this->validate(['destinataires' => 'required|array|min:1', 'objet' => 'required|string|max:150', 'premier' => 'required|string|max:5000'],
            ['destinataires.required' => 'Choisissez au moins un destinataire.'], ['objet' => 'objet', 'premier' => 'message']);
        $c = $svc->start(auth()->user(), $this->destinataires, trim($this->objet), trim($this->premier));
        $this->nouveau = false;
        $this->conversation = $c->id;
        $this->dispatch('message-envoye');
        $this->dispatch('menu-actualiser');
        $this->toast('Message envoyé.');
    }

    public function closeModal(): void
    {
        $this->nouveau = false;
    }

    public function render(MessagingService $svc)
    {
        $u = auth()->user();
        $list = Conversation::forUser($u)->with(['participants', 'lastMessage.user'])
            ->when($this->recherche, fn ($q) => $q->where(fn ($w) => $w->where('subject', 'like', "%{$this->recherche}%")
                ->orWhereHas('participants', fn ($p) => $p->where('name', 'like', "%{$this->recherche}%"))))->take(60)->get();
        $c = $this->courante()?->load(['messages.user', 'participants']);
        $recipients = $this->nouveau ? $svc->recipients($u)->filter(fn ($r) => ! $this->filtreDest || str_contains(mb_strtolower($r->name.' '.$r->roleLabel()), mb_strtolower($this->filtreDest)))->take(40) : collect();

        return $this->page('livewire.shared.messagerie', compact('list', 'c', 'u', 'recipients'), 'Messagerie', 'MY EVC <span class="sep">›</span> Messagerie',
            '<button class="btn btn-primary t-cta" wire:click="ouvrirNouveau"><i class="fa-solid fa-pen"></i>Nouveau message</button>');
    }
}
