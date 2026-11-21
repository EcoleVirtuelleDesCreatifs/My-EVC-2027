<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Audit extends Page
{
    use WithPagination;

    #[Url(except: '')]
    public string $q = '';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $items = AuditLog::with('user')->when($this->q, fn ($q) => $q->where(fn ($w) => $w->where('action', 'like', "%{$this->q}%")->orWhere('subject', 'like', "%{$this->q}%")
            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->q}%"))))->latest('created_at')->latest('id')->paginate(30);

        return $this->page('livewire.admin.audit', compact('items'), 'Journal d\'audit', 'Administration <span class="sep">›</span> Système');
    }
}
