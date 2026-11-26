<?php

namespace Tests\Feature;

use App\Livewire\Admin\Formations;
use App\Livewire\Admin\RessourceEditeur;
use App\Models\Candidature;
use App\Models\Promotion;
use App\Models\Resource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Notification::fake();
        Storage::fake('local');
        $this->actingAs($this->makeStaff('Super Administrateur'));
    }

    public function test_session_vide_supprimable_et_session_utilisee_protegee(): void
    {
        $p = Promotion::create(['name' => 'Session temporaire', 'starts_on' => '2027-01-01', 'ends_on' => '2027-05-01', 'status' => 'ouverte']);
        Livewire::test(Formations::class)->call('supprimerSession', $p->id)->assertHasNoErrors();
        $this->assertDatabaseMissing('promotions', ['id' => $p->id]);
        $s = $this->makeStudent();
        Livewire::test(Formations::class)->call('supprimerSession', $s->promotion_id);
        $this->assertDatabaseHas('promotions', ['id' => $s->promotion_id]);
        $this->assertDatabaseHas('students', ['id' => $s->id]);
    }

    public function test_suppression_session_reservee_aux_administrateurs_habilites(): void
    {
        $p = Promotion::firstOrFail();
        Livewire::actingAs($this->makeStaff('Responsable financier'))->test(Formations::class)->call('supprimerSession', $p->id)->assertForbidden();
        $this->assertDatabaseHas('promotions', ['id' => $p->id]);
    }

    public function test_page_dediee_ressource_enregistre_le_fichier_prive(): void
    {
        $this->get(route('admin.ressource.nouvelle'))->assertOk()->assertSee('Nouvelle ressource')->assertSee('Description');
        Livewire::test(RessourceEditeur::class)->set('r.title', 'Guide de mise en page')->set('r.description', 'Un guide pratique pour les apprenants.')
            ->set('notifier', false)->set('fichier', UploadedFile::fake()->create('guide.pdf', 400, 'application/pdf'))
            ->call('enregistrer')->assertHasNoErrors()->assertRedirect(route('admin.bibliotheque'));
        $r = Resource::where('title', 'Guide de mise en page')->firstOrFail();
        Storage::disk('local')->assertExists($r->path);
        $this->assertNull($r->formation_id);
        $this->get(route('admin.ressource.modifier', $r))->assertOk()->assertSee('Modifier la ressource');
    }

    public function test_formateur_ne_peut_pas_modifier_une_ressource_d_une_autre_formation(): void
    {
        $r = Resource::create(['title' => 'Ressource CM', 'category' => 'ebooks', 'format' => 'PDF', 'size' => '1 Mo', 'description' => 'Support CM', 'licence' => 'Réservé EVC', 'formation_id' => \App\Models\Formation::where('code', 'CM')->value('id')]);
        $this->actingAs($this->makeStaff('formateur', 'DG'))->get(route('formateur.ressource.modifier', $r))->assertNotFound();
        $this->actingAs($this->makeStudent()->user)->get(route('admin.ressource.nouvelle'))->assertForbidden();
    }

    public function test_edition_conserve_fichier_existant_si_stockage_echoue(): void
    {
        $r = Resource::create(['title' => 'Ressource existante', 'category' => 'ebooks', 'format' => 'PDF', 'size' => '1 Mo', 'description' => 'Support existant', 'licence' => 'Réservé EVC']);
        Storage::disk('local')->put('library/original.pdf', 'Original');
        $r->update(['path' => 'library/original.pdf', 'url' => null]);
        $editor = Livewire::test(RessourceEditeur::class, ['resource' => $r])->set('fichier', UploadedFile::fake()->create('nouveau.pdf', 50, 'application/pdf'));
        $disk = \Mockery::mock(Storage::disk('local'));
        $disk->shouldReceive('put')->andReturn(false);
        Storage::set('local', $disk);
        $editor->call('enregistrer')->assertHasErrors('fichier');
        $this->assertSame('library/original.pdf', $r->fresh()->path);
    }

    public function test_un_lien_ne_peut_pas_devenir_un_fichier_vide(): void
    {
        $r = Resource::create(['title' => 'Site officiel', 'category' => 'ebooks', 'format' => 'Site', 'size' => '—', 'description' => 'Documentation', 'licence' => 'Gratuit', 'url' => 'https://example.test/documentation']);
        Livewire::test(RessourceEditeur::class, ['resource' => $r])->set('r.source', 'fichier')->call('enregistrer')->assertHasErrors('fichier');
        $this->assertSame('https://example.test/documentation', $r->fresh()->url);
        Livewire::test(RessourceEditeur::class, ['resource' => $r])->set('r.url', 'javascript:alert(1)')->call('enregistrer')->assertHasErrors('r.url');
    }

    public function test_formateur_sans_formation_ne_peut_pas_ajouter_de_ressource(): void
    {
        $this->actingAs($this->makeStaff('formateur'))->get(route('formateur.ressource.nouvelle'))->assertForbidden();
    }

    public function test_preinscription_sans_modalite_ni_disponibilites(): void
    {
        $this->get('/preinscription')->assertOk()->assertDontSee('Modalité souhaitée')->assertDontSee('name="Disponibilités"', false);
        $this->postJson('/preinscription', ['answers' => ['Nom' => ['Kouassi'], 'Prénoms' => ['Awa'], 'E-mail' => ['awa@example.test'], 'Téléphone' => ['0700000000'], 'Date souhaitée d’inscription et de paiement' => [now()->toDateString()], 'Formations' => ['Infographie & design graphique']]])->assertOk();
        $this->assertNull(Candidature::firstOrFail()->mode);
    }
}
