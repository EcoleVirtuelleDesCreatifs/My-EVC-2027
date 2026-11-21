<?php

namespace App\Livewire\Shared;

use App\Livewire\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Livewire\WithFileUploads;

class Profil extends Page
{
    use WithFileUploads;

    public string $phone = '';

    public $photo;

    public string $actuel = '';

    public string $nouveau = '';

    public string $nouveau_confirmation = '';

    public function mount(): void
    {
        $this->phone = (string) auth()->user()->phone;
    }

    public function enregistrer(): void
    {
        $this->validate(['phone' => 'nullable|string|max:30', 'photo' => 'nullable|image|max:4096']);
        $u = auth()->user();
        if ($this->photo) {
            if ($u->avatar_path) {
                Storage::disk('public')->delete($u->avatar_path);
            }
            $u->avatar_path = $this->photo->store('avatars', 'public');
        }
        $u->phone = $this->phone ?: null;
        $u->save();
        $this->reset('photo');
        $this->toast('Profil mis à jour.');
    }

    public function changerMotDePasse(): void
    {
        $this->validate(['actuel' => 'required', 'nouveau' => ['required', 'confirmed', Password::min(8)->letters()->numbers()]], [], ['actuel' => 'mot de passe actuel', 'nouveau' => 'nouveau mot de passe']);
        if (! Hash::check($this->actuel, auth()->user()->password)) {
            $this->addError('actuel', 'Le mot de passe actuel est incorrect.');

            return;
        }
        auth()->user()->update(['password' => $this->nouveau]);
        $this->reset('actuel', 'nouveau', 'nouveau_confirmation');
        $this->toast('Mot de passe modifié.');
    }

    public function render()
    {
        return $this->page('livewire.shared.profil', ['u' => auth()->user()->load('student.formations', 'student.promotion')], 'Mon profil', 'MY EVC <span class="sep">›</span> Mon profil');
    }
}
