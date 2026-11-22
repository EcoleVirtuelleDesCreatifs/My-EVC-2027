<?php

namespace App\Livewire\Shared;

use App\Livewire\Page;
use Livewire\WithPagination;

class Notifications extends Page
{
    use WithPagination;

    public function ouvrir(string $id): void
    {
        $n = auth()->user()->notifications()->findOrFail($id);
        $n->markAsRead();
        $this->dispatch('menu-actualiser');
        if ($url = $n->data['url'] ?? null) {
            $this->redirect($url, navigate: true);
        }
    }

    public function toutLu(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->dispatch('menu-actualiser');
        $this->toast('Toutes les notifications sont lues.');
    }

    public function render()
    {
        $items = auth()->user()->notifications()->paginate(20);

        return $this->page('livewire.shared.notifications', compact('items'), 'Notifications', 'MY EVC <span class="sep">›</span> Notifications',
            auth()->user()->unreadNotifications()->exists() ? '<button class="btn btn-ghost t-cta" wire:click="toutLu"><i class="fa-solid fa-check-double"></i>Tout marquer comme lu</button>' : null);
    }
}
