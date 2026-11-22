<?php

namespace App\Livewire\Shared;

use App\Models\Conversation;
use Livewire\Attributes\On;
use Livewire\Component;

class MenuCompteurs extends Component
{
    #[On('menu-actualiser')]
    public function actualiser(): void {}

    public function render()
    {
        $user = auth()->user();
        abort_unless($user, 403);
        $this->dispatch('menu-compteurs', messages: Conversation::unreadCount($user), notifications: $user->unreadNotifications()->count());

        return view('livewire.shared.menu-compteurs');
    }
}
