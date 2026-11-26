<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * php artisan db:seed           → rôles, formations et liens officiels de la bibliothèque
     * Aucune donnée de démonstration n’est créée ; un compte Super Administrateur
     * se crée avec « php artisan evc:admin ».
     */
    public function run(): void
    {
        $this->call([RolesSeeder::class, FormationSeeder::class, LibrarySeeder::class]);
    }
}
