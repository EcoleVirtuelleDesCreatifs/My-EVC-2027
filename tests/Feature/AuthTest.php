<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_connexion_par_matricule_ou_par_email(): void
    {
        $s = $this->makeStudent();
        $this->post('/myevc/connexion', ['login' => strtolower($s->matricule), 'password' => 'password'])->assertRedirect(route('etudiant.accueil'));
        $this->post('/myevc/deconnexion');
        $this->post('/myevc/connexion', ['login' => $s->user->email, 'password' => 'password'])->assertRedirect(route('etudiant.accueil'));
    }

    public function test_mauvais_mot_de_passe_et_limitation(): void
    {
        $s = $this->makeStudent();
        foreach (range(1, 5) as $i) {
            $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'faux'])->assertSessionHasErrors('login');
        }
        $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_compte_suspendu_refuse(): void
    {
        $s = $this->makeStudent();
        $s->user->update(['status' => 'suspendu']);
        $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_chaque_espace_est_protege(): void
    {
        $this->get('/myevc/etudiant')->assertRedirect(route('login'));
        $s = $this->makeStudent();
        $this->actingAs($s->user)->get('/myevc/admin')->assertForbidden();
        $this->actingAs($s->user)->get('/myevc/formateur')->assertForbidden();

        $trainer = $this->makeStaff('formateur', 'DG');
        $this->actingAs($trainer)->get('/myevc/formateur')->assertOk();
        $this->actingAs($trainer)->get('/myevc/etudiant')->assertForbidden();

        $finance = $this->makeStaff('Responsable financier');
        $this->actingAs($finance)->get('/myevc/admin/finances')->assertOk();
        $this->actingAs($finance)->get('/myevc/admin/utilisateurs')->assertForbidden();
        $this->actingAs($finance)->get('/myevc/admin/candidatures')->assertForbidden();

        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin)->get('/myevc/admin/utilisateurs')->assertOk();
    }

    public function test_tous_les_ecrans_s_affichent_pour_chaque_role(): void
    {
        $s = $this->makeStudent(['DG', 'CM']);
        foreach (['etudiant.accueil', 'etudiant.cours', 'etudiant.projets', 'etudiant.bibliotheque', 'etudiant.paiements', 'etudiant.certificat', 'myevc.notifications', 'myevc.profil'] as $r) {
            $this->actingAs($s->user)->get(route($r))->assertOk();
        }
        $t = $this->makeStaff('formateur', 'DG');
        foreach (['formateur.accueil', 'formateur.seances', 'formateur.corrections', 'formateur.bibliotheque'] as $r) {
            $this->actingAs($t)->get(route($r))->assertOk();
        }
        $a = $this->makeStaff('Super Administrateur');
        foreach (['dashboard', 'candidatures', 'etudiants', 'formations', 'seances', 'projets', 'disponibilite', 'finances', 'bibliotheque', 'actualites', 'messages', 'certificats', 'utilisateurs', 'audit', 'parametres'] as $r) {
            $this->actingAs($a)->get(route('admin.'.$r))->assertOk();
        }
        $this->actingAs($a)->get(route('admin.etudiant', $s))->assertOk()->assertSee($s->matricule);
    }
}
