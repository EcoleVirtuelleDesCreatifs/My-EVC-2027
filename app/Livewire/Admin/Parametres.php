<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Setting;

class Parametres extends Page
{
    public array $values = [];

    public function mount(): void
    {
        foreach (array_keys(Setting::DEFAULTS) as $k) {
            $this->values[$k] = (string) Setting::get($k);
        }
    }

    public function enregistrer(): void
    {
        $this->validate(['values.school_email' => 'required|email', 'values.whatsapp_ci' => 'required|digits_between:8,15', 'values.whatsapp_fr' => 'nullable|digits_between:8,15', 'values.*' => 'nullable|string|max:255'],
            [], ['values.whatsapp_ci' => 'WhatsApp Côte d\'Ivoire', 'values.whatsapp_fr' => 'WhatsApp France', 'values.school_email' => 'e-mail']);
        foreach ($this->values as $k => $v) {
            Setting::put($k, $v);
        }
        AuditLog::record('Paramètres modifiés');
        $this->toast('Paramètres enregistrés.');
    }

    public function render()
    {
        return $this->page('livewire.admin.parametres', ['labels' => Setting::DEFAULTS], 'Paramètres', 'Administration <span class="sep">›</span> Système');
    }
}
