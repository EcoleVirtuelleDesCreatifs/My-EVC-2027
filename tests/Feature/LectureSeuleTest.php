<?php

namespace Tests\Feature;

use App\Livewire\Admin\EtudiantFiche;
use App\Livewire\Etudiant\Rapport;
use App\Livewire\Shared\Profil;
use App\Models\FinalReport;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class LectureSeuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_apres_la_formation_le_compte_passe_en_lecture_seule_messagerie_autorisee(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $this->assertFalse($s->isReadOnly());
        $this->actingAs($s->user)->get(route('etudiant.projets'))->assertOk();

        $old = Promotion::create(['name' => 'Session terminée', 'starts_on' => now()->subMonths(5), 'ends_on' => now()->subDays(2), 'status' => 'terminee']);
        $s->update(['promotion_id' => $old->id]);
        $s->enrollments()->update(['promotion_id' => $old->id]);
        $s = $s->fresh();
        $this->assertTrue($s->isReadOnly());
        $u = $s->user;

        $home = $this->actingAs($u)->get(route('etudiant.accueil'))->assertOk()->assertSee('compte en lecture seule')->assertSee('Écrire à l’équipe');
        foreach (['etudiant.projets', 'etudiant.bibliotheque', 'etudiant.quiz', 'etudiant.demarrage'] as $r) {
            $this->actingAs($u)->get(route($r))->assertRedirect(route('etudiant.accueil'));
            $home->assertDontSee('href="'.route($r).'"', false);
        }
        foreach (['etudiant.portfolio', 'etudiant.paiements', 'etudiant.certificat', 'etudiant.rapport', 'myevc.messagerie', 'myevc.profil'] as $r) {
            $this->actingAs($u)->get(route($r))->assertOk();
        }

        Livewire::actingAs($u)->test(Rapport::class)->set('fichier', UploadedFile::fake()->create('r.pdf', 10, 'application/pdf'))->call('deposer')
            ->assertDispatched('toast', kind: 'warn');
        $this->assertSame(0, FinalReport::count());
        Livewire::actingAs($u)->test(Profil::class)->set('phone', '0102030405')->call('enregistrer')->assertDispatched('toast', kind: 'warn');
        $this->assertNotSame('0102030405', $u->fresh()->phone);

        $staff = $this->makeStaff('Super Administrateur');
        Livewire::actingAs($u)->test(\App\Livewire\Shared\Messagerie::class)->assertOk();

        /* L'administration prolonge l'accès complet */
        Livewire::actingAs($staff)->test(EtudiantFiche::class, ['student' => $s])->set('onglet', 'compte')->assertSee('Lecture seule depuis')
            ->set('accesJusquau', now()->addMonth()->toDateString())->call('enregistrerAcces')->assertHasNoErrors()->assertSee('Complet jusqu’au');
        $this->assertFalse($s->fresh()->isReadOnly());
        $this->actingAs($u->fresh())->get(route('etudiant.projets'))->assertOk();
    }
}
