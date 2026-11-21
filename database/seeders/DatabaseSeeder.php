<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed           → rôles, contenus et jeu de démonstration (hors production)
     * En production : seuls les rôles et les contenus sont créés ; un compte Super Administrateur
     * se crée avec « php artisan evc:admin ».
     */
    public function run(): void
    {
        $this->call([RolesSeeder::class, ContentSeeder::class, LibrarySeeder::class]);
        if (! app()->isProduction()) {
            $this->call(DemoSeeder::class);
        }
    }
}
