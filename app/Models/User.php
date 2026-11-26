<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    /** Rôles d'administration (en plus de « etudiant » et « formateur »). */
    public const ADMIN_ROLES = [
        'Super Administrateur', 'Direction Générale', 'Responsable pédagogique', 'Responsable administratif',
        'Responsable financier', 'Responsable certification', 'Responsable communication', 'Support technique',
    ];

    protected $fillable = ['name', 'email', 'phone', 'password', 'avatar_path', 'status', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'last_login_at' => 'datetime', 'password' => 'hashed'];
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token): void
    {
        $this->notify(new \App\Notifications\ReinitialisationMotDePasse($token));
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function trainerProfiles(): HasMany
    {
        return $this->hasMany(Trainer::class);
    }

    /** Espace de l'utilisateur : etudiant, formateur ou admin. */
    public function space(): string
    {
        if ($this->hasRole('etudiant')) {
            return 'etudiant';
        }
        if ($this->hasRole('formateur')) {
            return 'formateur';
        }

        return 'admin';
    }

    public function homeRoute(): string
    {
        return match ($this->space()) {
            'etudiant' => 'etudiant.accueil',
            'formateur' => 'formateur.accueil',
            default => 'admin.dashboard',
        };
    }

    /** Identifiants des formations enseignées (formateur). */
    public function taughtFormationIds(): array
    {
        return $this->trainerProfiles()->pluck('formation_id')->all();
    }

    public function initials(): string
    {
        return collect(preg_split('/[\s-]+/', trim($this->name)))->filter()->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path && Storage::disk('public')->exists($this->avatar_path) ? '/storage/'.ltrim($this->avatar_path, '/') : null;
    }

    public function roleLabel(): string
    {
        return match ($this->space()) {
            'etudiant' => 'Étudiant · '.($this->student?->formations->pluck('code')->implode(' + ') ?? ''),
            'formateur' => 'Formateur · '.Formation::whereIn('id', $this->taughtFormationIds())->pluck('code')->implode(' + '),
            default => $this->getRoleNames()->first() ?? 'Administration',
        };
    }
}
