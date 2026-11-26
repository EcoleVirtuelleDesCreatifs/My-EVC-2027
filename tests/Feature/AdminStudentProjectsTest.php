<?php

namespace Tests\Feature;

use App\Livewire\Admin\EtudiantFiche;
use App\Livewire\Admin\Projets;
use App\Models\Assignment;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminStudentProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_destinataires_et_recherche_limites_au_projet(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $s = $this->makeStudent(['CM']);
        $other = $this->makeStudent(['CM']);
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Projet CM', 'brief' => 'Brief', 'published' => true]);
        Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(4)]);
        $otherProject = Project::create(['formation_id' => $p->formation_id, 'title' => 'Autre projet', 'brief' => 'Brief']);
        Assignment::create(['project_id' => $otherProject->id, 'student_id' => $other->id, 'due_on' => now()->addDays(4)]);
        $page = Livewire::actingAs($admin)->test(Projets::class)->set('onglet', 'catalogue')->set('formation', (string) $p->formation_id)
            ->call('voirDestinataires', $p->id)->assertSee($s->user->name)->assertSee($s->matricule)->assertDontSee($other->user->name);
        $page->set('rechercheDestinataire', 'ABSENT-XYZ')->assertDontSee($s->matricule)->assertSee('Aucun étudiant destinataire');
        $page->call('closeModal')->assertSet('destinatairesProjet', null);
    }

    public function test_profil_modifiable_sans_changer_les_identifiants_ou_inscriptions(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $s = $this->makeStudent(['CM']);
        $matricule = $s->matricule;
        $enrollments = $s->enrollments()->get()->toJson();
        $password = $s->user->password;
        Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->call('modifierProfil')
            ->set('profil.name', 'Nom Corrigé')->set('profil.email', ' CORRIGE@example.test ')->set('profil.phone', '+225 0700000000')
            ->set('profil.city', 'Abidjan')->set('profil.birth_date', '2000-04-20')->set('profil.level', 'Intermédiaire')
            ->set('profil.status', 'suspendu')->call('enregistrerProfil')->assertHasNoErrors()->assertSet('editionProfil', false)
            ->assertSee('Nom Corrigé');
        $s->refresh();
        $this->assertSame('corrige@example.test', $s->user->email);
        $this->assertSame('Abidjan', $s->city);
        $this->assertSame('2000-04-20', $s->birth_date->toDateString());
        $this->assertSame($matricule, $s->matricule);
        $this->assertSame('actif', $s->status);
        $this->assertSame($password, $s->user->password);
        $this->assertSame($enrollments, $s->enrollments()->get()->toJson());
        $this->assertTrue($s->user->hasRole('etudiant'));
    }

    public function test_email_unique_date_et_annulation(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $s = $this->makeStudent();
        $other = $this->makeStudent();
        $name = $s->user->name;
        $page = Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->call('modifierProfil');
        $page->set('profil.email', $other->user->email)->set('profil.birth_date', now()->addDay()->toDateString())
            ->call('enregistrerProfil')->assertHasErrors(['profil.email', 'profil.birth_date']);
        $page->call('closeModal')->assertSet('editionProfil', false)->assertSet('profil', []);
        $this->assertSame($name, $s->fresh()->user->name);
        $page->call('modifierProfil')->call('enregistrerProfil')->assertHasNoErrors();
    }

    public function test_champs_vides_et_valeurs_non_textuelles_valides_cote_serveur(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $s = $this->makeStudent();
        $s->update(['birth_date' => '2001-01-01']);
        $page = Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->call('modifierProfil');
        $page->set('profil.name', ['intrus'])->set('profil.email', ['intrus'])->call('enregistrerProfil')->assertHasErrors(['profil.name', 'profil.email']);
        $page->call('modifierProfil')->set('profil.birth_date', '')->set('profil.country', '   ')->call('enregistrerProfil')->assertHasNoErrors();
        $this->assertNull($s->fresh()->birth_date);
        $this->assertNull($s->fresh()->country);
    }

    public function test_pagination_et_recherche_dans_tous_les_destinataires(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $p = null;
        for ($i = 0; $i < 16; $i++) {
            $s = $this->makeStudent(['CM']);
            $p ??= Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Projet collectif', 'brief' => 'Brief']);
            Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(4)]);
        }
        Livewire::actingAs($admin)->test(Projets::class)->set('onglet', 'catalogue')->call('voirDestinataires', $p->id)
            ->assertDontSee($s->matricule)->set('rechercheDestinataire', $s->matricule)->assertSee($s->matricule);
    }

    public function test_personnel_sans_permission_ne_peut_pas_modifier_le_profil(): void
    {
        $staff = $this->makeStaff('Responsable communication');
        $s = $this->makeStudent();
        Livewire::actingAs($staff)->test(EtudiantFiche::class, ['student' => $s])->call('modifierProfil')->assertForbidden();
        Livewire::actingAs($staff)->test(EtudiantFiche::class, ['student' => $s])->call('enregistrerProfil')->assertForbidden();
    }

    public function test_la_fiche_etudiant_s_actualise_apres_le_premier_versement(): void
    {
        $s = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');
        $page = Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->set('onglet', 'paiements')
            ->assertSee('Payé : 0 FCFA')->assertSee('Aucun versement')->assertSeeHtml('wire:poll.15s.visible');

        $page->call('ouvrirVersement')->assertDontSeeHtml('wire:poll.15s.visible')
            ->set('methode', \App\Models\Payment::METHODS[0])->call('enregistrerVersement')->assertHasNoErrors()
            ->assertSee('Payé : 95 000 FCFA')->assertSee('réglé 95 000 FCFA')->assertSee('Payée')->assertDontSee('Aucun versement')
            ->assertSeeHtml('wire:poll.15s.visible');

        /* Versement saisi depuis Finances pendant que la fiche est ouverte : visible à l'actualisation suivante */
        app(\App\Services\PaymentService::class)->record($s->fresh(), 90000, \App\Models\Payment::METHODS[0]);
        $page->call('$refresh')->assertSee('Payé : 185 000 FCFA');
    }
}
