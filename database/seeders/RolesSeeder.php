<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/** Rôles et droits d'accès aux modules d'administration. */
class RolesSeeder extends Seeder
{
    /** Module => libellé (affiché dans « Utilisateurs et rôles »). */
    public const MODULES = [
        'dashboard' => 'Tableau de bord', 'candidatures' => 'Préinscriptions', 'etudiants' => 'Étudiants',
        'formations' => 'Formations, sessions et séances', 'projets' => 'Projets', 'finances' => 'Finances',
        'bibliotheque' => 'Bibliothèque', 'webtv' => 'Web TV', 'reco' => 'RECO 60', 'qcm' => 'Quiz (QCM)', 'actualites' => 'Actualités', 'messages' => 'Messages du site',
        'certificats' => 'Certificats', 'utilisateurs' => 'Utilisateurs et rôles', 'audit' => 'Journal d\'audit',
        'parametres' => 'Paramètres',
    ];

    public const ROLES = [
        'Super Administrateur' => '*',
        'Direction Générale' => ['dashboard', 'candidatures', 'etudiants', 'formations', 'projets', 'finances', 'bibliotheque', 'webtv', 'reco', 'qcm', 'actualites', 'messages', 'certificats', 'audit'],
        'Responsable pédagogique' => ['dashboard', 'candidatures', 'etudiants', 'formations', 'projets', 'bibliotheque', 'webtv', 'reco', 'qcm', 'certificats'],
        'Responsable administratif' => ['dashboard', 'candidatures', 'etudiants', 'formations', 'actualites', 'messages'],
        'Responsable financier' => ['dashboard', 'etudiants', 'finances'],
        'Responsable certification' => ['dashboard', 'etudiants', 'certificats'],
        'Responsable communication' => ['dashboard', 'actualites', 'messages', 'bibliotheque', 'webtv'],
        'Support technique' => ['dashboard', 'etudiants', 'audit', 'parametres'],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (array_keys(self::MODULES) as $m) {
            Permission::findOrCreate('admin.'.$m);
        }
        Role::findOrCreate('etudiant');
        Role::findOrCreate('formateur');
        foreach (self::ROLES as $role => $modules) {
            $perms = $modules === '*' ? array_keys(self::MODULES) : $modules;
            Role::findOrCreate($role)->syncPermissions(array_map(fn ($m) => 'admin.'.$m, $perms));
        }
    }
}
