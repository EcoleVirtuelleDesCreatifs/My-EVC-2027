<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/** Crée (ou promeut) un compte Super Administrateur : php artisan evc:admin */
class CreerAdmin extends Command
{
    protected $signature = 'evc:admin {email?} {--name=} {--password=}';

    protected $description = 'Créer le compte Super Administrateur de MY EVC';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('E-mail');
        $name = $this->option('name') ?: $this->ask('Nom complet', 'Administrateur EVC');
        $password = $this->option('password') ?: $this->secret('Mot de passe (8 caractères minimum)');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8) {
            $this->error('E-mail invalide ou mot de passe trop court.');

            return self::FAILURE;
        }
        $user = User::updateOrCreate(['email' => strtolower($email)], ['name' => $name, 'password' => $password, 'status' => 'actif']);
        $user->syncRoles(['Super Administrateur']);
        $this->info("Compte Super Administrateur prêt : {$user->email}");

        return self::SUCCESS;
    }
}
