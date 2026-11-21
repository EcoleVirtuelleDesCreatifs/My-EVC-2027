<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Url;

class Messages extends Page
{
    #[Url(except: 'messages')]
    public string $onglet = 'messages';

    public function traiter(int $id): void
    {
        $m = ContactMessage::findOrFail($id);
        $m->update(['handled_at' => $m->handled_at ? null : now()]);
        $this->toast($m->handled_at ? 'Message marqué comme traité.' : 'Message rouvert.');
    }

    public function exporter()
    {
        $csv = "email;inscrit_le\n".NewsletterSubscriber::orderBy('created_at')->get()->map(fn ($s) => $s->email.';'.$s->created_at->format('d/m/Y'))->implode("\n");

        return response()->streamDownload(fn () => print ("\xEF\xBB\xBF".$csv), 'newsletter-evc-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        return $this->page('livewire.admin.messages', [
            'messages' => ContactMessage::orderByRaw('handled_at is not null')->latest()->take(100)->get(),
            'subscribers' => NewsletterSubscriber::latest()->take(200)->get(), 'nbSubs' => NewsletterSubscriber::count(),
        ], 'Messages du site', 'Administration <span class="sep">›</span> Contenus <span class="sep">›</span> Messages');
    }
}
