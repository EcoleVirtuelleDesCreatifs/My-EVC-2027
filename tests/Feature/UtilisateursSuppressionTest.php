<?php

namespace Tests\Feature;

use App\Livewire\Admin\Utilisateurs;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class UtilisateursSuppressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_admin_supprime_un_membre_de_l_equipe_sans_perdre_l_historique(): void
    {
        Notification::fake();
        $this->seedBase();
        $admin = $this->makeStaff('Super Administrateur');
        $caissier = $this->makeStaff('Super Administrateur');
        $formateur = $this->makeStaff('formateur', 'DG');
        $s = $this->makeStudent(['DG']);
        $this->actingAs($caissier);
        $p = app(PaymentService::class)->record($s->fresh(), 50000, 'Espèces');

        $page = Livewire::actingAs($admin)->test(Utilisateurs::class)->assertSeeHtml('wire:click="supprimer('.$formateur->id.')"')
            ->assertDontSeeHtml('wire:click="supprimer('.$admin->id.')"');
        $page->call('supprimer', $formateur->id);
        $this->assertNull(User::find($formateur->id));
        $page->call('supprimer', $caissier->id);
        $this->assertNull(User::find($caissier->id));
        $this->assertNull($p->fresh()->recorded_by, 'Le versement reste, sans auteur.');
        $this->assertSame(50000, $p->fresh()->amount);
        $this->assertSame(2, AuditLog::where('action', 'Utilisateur supprimé')->count());

        $page->call('supprimer', $admin->id)->assertDispatched('toast', kind: 'warn');
        $page->call('supprimer', $s->user_id)->assertDispatched('toast', kind: 'warn');
        $this->assertNotNull(User::find($admin->id));
        $this->assertNotNull(User::find($s->user_id));

        $autre = $this->makeStaff('Super Administrateur');
        $admin->update(['status' => 'suspendu']);
        Livewire::actingAs($autre)->test(Utilisateurs::class)->call('supprimer', $admin->id);
        $this->assertNull(User::find($admin->id), 'Un autre Super Administrateur actif existe.');
    }

    public function test_la_page_404_est_personnalisee(): void
    {
        $this->seedBase();
        $this->get('/une-page-qui-nexiste-pas')->assertNotFound()->assertSee('Page introuvable')->assertSee('Oups, cette page s’est envolée.')
            ->assertSee('/une-page-qui-nexiste-pas')->assertSee('Nos formations')->assertSee('Préinscription')->assertSee('noindex', false);
        $s = $this->makeStudent(['DG']);
        $this->actingAs($s->user)->get('/myevc/inexistant')->assertNotFound()->assertSee('Cette page n’existe pas dans votre espace MY EVC.')->assertSee('Revenir à mon espace');
        $this->get('/actualites/article-inconnu')->assertNotFound()->assertSee('Page introuvable');
    }
}
