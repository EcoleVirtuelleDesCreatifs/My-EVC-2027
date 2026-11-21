<?php

namespace App\Livewire\Admin;

use App\Livewire\Page;
use App\Models\AuditLog;
use App\Models\Formation;
use App\Models\Trainer;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class Utilisateurs extends Page
{
    #[Url(except: 'equipe')]
    public string $onglet = 'equipe';

    public bool $form = false;

    public array $u = [];

    public array $matrice = [];

    public function mount(): void
    {
        $this->chargerMatrice();
    }

    private function chargerMatrice(): void
    {
        foreach (User::ADMIN_ROLES as $r) {
            $role = Role::findByName($r);
            foreach (array_keys(RolesSeeder::MODULES) as $m) {
                $this->matrice[$r][$m] = $role->hasPermissionTo('admin.'.$m);
            }
        }
    }

    public function creer(): void
    {
        $this->u = ['name' => '', 'email' => '', 'phone' => '', 'role' => 'formateur', 'formation_id' => Formation::active()->value('id')];
        $this->resetErrorBag();
        $this->form = true;
    }

    public function enregistrer(): void
    {
        $roles = array_merge(['formateur'], User::ADMIN_ROLES);
        $this->validate(['u.name' => 'required|string|max:100', 'u.email' => 'required|email|max:150|unique:users,email', 'u.phone' => 'nullable|string|max:30',
            'u.role' => 'required|in:'.implode(',', $roles), 'u.formation_id' => 'required_if:u.role,formateur|nullable|exists:formations,id'],
            [], ['u.name' => 'nom', 'u.email' => 'e-mail', 'u.formation_id' => 'formation']);
        $user = User::create(['name' => $this->u['name'], 'email' => strtolower($this->u['email']), 'phone' => $this->u['phone'] ?: null, 'password' => Str::random(40)]);
        $user->assignRole($this->u['role']);
        if ($this->u['role'] === 'formateur') {
            Trainer::create(['user_id' => $user->id, 'formation_id' => $this->u['formation_id']]);
        }
        Password::broker('activations')->sendResetLink(['email' => $user->email]);
        AuditLog::record('Utilisateur créé', $user->name.' — '.$this->u['role']);
        $this->form = false;
        $this->toast('Compte créé. Un lien pour choisir le mot de passe est envoyé à '.$user->email.'.');
    }

    public function basculer(int $id): void
    {
        $user = User::findOrFail($id);
        abort_if($user->id === auth()->id(), 422, 'Vous ne pouvez pas suspendre votre propre compte.');
        $user->update(['status' => $user->status === 'suspendu' ? 'actif' : 'suspendu']);
        AuditLog::record($user->status === 'suspendu' ? 'Compte suspendu' : 'Compte réactivé', $user->name);
        $this->toast($user->status === 'suspendu' ? 'Compte suspendu.' : 'Compte réactivé.');
    }

    public function enregistrerRoles(): void
    {
        foreach ($this->matrice as $r => $mods) {
            if ($r === 'Super Administrateur') {
                continue;   // toujours tous les droits
            }
            Role::findByName($r)->syncPermissions(collect($mods)->filter()->keys()->map(fn ($m) => 'admin.'.$m)->all());
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AuditLog::record('Droits des rôles modifiés');
        $this->toast('Droits enregistrés.');
    }

    public function closeModal(): void
    {
        $this->form = false;
    }

    public function render()
    {
        $team = User::with('roles', 'trainerProfiles.formation')->whereDoesntHave('roles', fn ($q) => $q->where('name', 'etudiant'))->orderBy('name')->get();

        return $this->page('livewire.admin.utilisateurs', ['team' => $team, 'formations' => Formation::active()->get(), 'modules' => RolesSeeder::MODULES],
            'Utilisateurs et rôles', 'Administration <span class="sep">›</span> Système',
            '<button class="btn btn-primary t-cta" wire:click="creer"><i class="fa-solid fa-user-plus"></i>Nouvel utilisateur</button>');
    }
}
