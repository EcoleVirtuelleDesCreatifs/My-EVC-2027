<?php

namespace Tests\Feature;

use App\Livewire\Etudiant\Alertes;
use App\Notifications\Avis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlertesEtudiantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_les_bulles_apparaissent_a_la_reception_et_disparaissent_apres_lecture(): void
    {
        $s = $this->makeStudent(['DG']);
        $user = $s->user;
        $page = Livewire::actingAs($user)->test(Alertes::class)->assertDontSee('Du nouveau pour vous');
        $user->notify(new Avis('Nouveau message sur un projet', 'Voici le format à utiliser.', '/myevc/etudiant/projets/86'));
        $notification = $user->notifications()->firstOrFail();
        $page->call('actualiser')->assertSee('Du nouveau pour vous')->assertSee('Voici le format à utiliser.')
            ->assertSee('Lire la réponse')->assertSee('L’équipe vous a répondu.');
        $this->assertNull($notification->fresh()->read_at);
        $this->actingAs($user)->get(route('myevc.profil'))->assertOk()->assertSee('Voici le format à utiliser.');
        $page->call('marquerLue', $notification->id)->assertDispatched('menu-actualiser')->assertDontSee('Voici le format à utiliser.');
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_ouvrir_marque_la_notification_lue_et_garde_le_domaine_local(): void
    {
        $s = $this->makeStudent(['DG']);
        $s->user->notify(new Avis('Nouveau projet', 'Un brief vous attend.', 'https://www.ecolevirtuelledescreatifs.com/myevc/etudiant/projets/86'));
        $notification = $s->user->notifications()->firstOrFail();
        Livewire::actingAs($s->user)->test(Alertes::class)->assertSee('Découvrir mon projet')
            ->call('ouvrir', $notification->id)->assertRedirect('/myevc/etudiant/projets/86');
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_seules_les_trois_dernieres_notifications_non_lues_sont_affichees(): void
    {
        $s = $this->makeStudent(['DG']);
        for ($i = 1; $i <= 4; $i++) {
            $s->user->notify(new Avis('Information '.$i, 'Message '.$i, '/myevc/etudiant/paiements'));
            $this->travel(1)->seconds();
        }
        Livewire::actingAs($s->user)->test(Alertes::class)->assertViewHas('total', 4)
            ->assertSee('Information 4')->assertSee('Information 3')->assertSee('Information 2')->assertDontSee('Information 1')
            ->assertSee('Toutes mes notifications');
    }

    public function test_les_notifications_d_un_autre_etudiant_ne_sont_pas_accessibles(): void
    {
        $s = $this->makeStudent(['DG']);
        $other = $this->makeStudent(['DG']);
        $other->user->notify(new Avis('Information privée', 'Confidentiel', '/myevc/etudiant/paiements'));
        $id = $other->user->notifications()->firstOrFail()->id;
        Livewire::actingAs($s->user)->test(Alertes::class)->assertDontSee('Confidentiel');
        foreach (['marquerLue', 'ouvrir'] as $method) {
            try {
                (new Alertes)->$method($id);
                $this->fail('Une notification étrangère ne doit pas être accessible.');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
                $this->assertSame(\Illuminate\Notifications\DatabaseNotification::class, $exception->getModel());
            }
        }
        $this->assertNull($other->user->notifications()->firstOrFail()->read_at);
        Livewire::actingAs($this->makeStaff('Super Administrateur'))->test(Alertes::class)->assertForbidden();
    }

    public function test_une_destination_non_autorisee_renvoie_vers_les_notifications(): void
    {
        $s = $this->makeStudent(['DG']);
        $s->user->notify(new Avis('Information', 'Détail', 'https://example.test/ailleurs'));
        $id = $s->user->notifications()->firstOrFail()->id;
        Livewire::actingAs($s->user)->test(Alertes::class)->assertSee('Lire la notification')
            ->call('ouvrir', $id)->assertRedirect('/myevc/notifications');
    }
}
