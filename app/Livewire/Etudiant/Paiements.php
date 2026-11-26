<?php

namespace App\Livewire\Etudiant;

use App\Livewire\Page;
use App\Models\Setting;

class Paiements extends Page
{
    public function render()
    {
        $s = $this->student();
        $s->refreshAllocation();
        $s->load('payments', 'schedules.formation');
        $schedules = $s->scheduleRows();
        $wa = Setting::get('whatsapp_ci');

        return $this->page('livewire.etudiant.paiements', compact('s', 'schedules', 'wa'), 'Paiements', 'Espace étudiant <span class="sep">›</span> Paiements');
    }
}
