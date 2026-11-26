<?php

namespace Tests\Feature;

use App\Livewire\Etudiant\ProjetDetail;
use App\Livewire\Shared\ProjetEchanges;
use App\Models\Assignment;
use App\Models\Project;
use App\Notifications\Avis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ProjetEchangesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_message_notifie_l_equipe_et_une_reponse_notifie_l_etudiant(): void
    {
        $this->seedBase();
        Notification::fake();
        $s = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');
        $trainer = $this->makeStaff('formateur', 'DG');
        $other = $this->makeStaff('formateur', 'CM');
        $suspended = $this->makeStaff('Super Administrateur');
        $suspended->update(['status' => 'suspendu']);
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Logo créatif', 'brief' => 'Brief', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'status' => 'afaire', 'due_on' => now()->addDays(5)]);

        Livewire::actingAs($s->user)->test(ProjetDetail::class, ['assignment' => $a])
            ->set('commentaire', 'Quel format utiliser ?')->call('commenter')->assertHasNoErrors()->assertSet('commentaire', '');
        foreach ([$admin, $trainer] as $target) {
            Notification::assertSentTo($target, Avis::class, fn ($n, $channels) => $n->title === 'Nouveau message sur un projet'
                && $channels === ['database', 'mail'] && $n->url === route('projet.echanges', $a)
                && str_contains($n->body, 'Quel format utiliser ?'));
        }
        Notification::assertNothingSentTo($other);
        Notification::assertNothingSentTo($suspended);
        Notification::assertNothingSentTo($s->user);
        $this->actingAs($admin)->get(route('projets.echanges'))->assertOk()->assertSee('Échanges projets')
            ->assertSee('Logo créatif')->assertSee('Lire et répondre');
        $this->actingAs($trainer)->get(route('projets.echanges'))->assertOk()->assertSee('Logo créatif');
        $this->actingAs($other)->get(route('projets.echanges'))->assertOk()->assertDontSee('Logo créatif');
        $this->actingAs($s->user)->get(route('projets.echanges'))->assertForbidden();
        $a->log('attribution', 'Projet attribué automatiquement.', null, true);
        $this->actingAs($admin)->get(route('projet.echanges', $a))->assertOk()->assertSee('Quel format utiliser ?')
            ->assertSeeHtml('class="pc-message "')->assertSeeHtml('class="pc-event"')
            ->assertSee('Événement du projet')->assertSee('Conversation avec l’équipe pédagogique');
        Livewire::actingAs($trainer)->test(ProjetEchanges::class, ['assignment' => $a])
            ->set('message', 'Utilisez le format PDF.')->call('envoyer')->assertHasNoErrors()->assertSet('message', '')
            ->assertDispatched('projet-message-envoye')->assertSeeHtml('pc-message pc-mine')->assertSee('Envoyé');
        Notification::assertSentTo($s->user, Avis::class, fn ($n, $channels) => $n->url === route('etudiant.projet', $a)
            && $channels === ['database', 'mail']);
        $this->actingAs($s->user)->get(route('etudiant.projet', $a))->assertOk()->assertSee('Utilisez le format PDF.');
        $this->assertSame(2, $a->comments()->where('kind', 'commentaire')->count());
        $this->actingAs($other)->get(route('projet.echanges', $a))->assertForbidden();
        $this->actingAs($s->user)->get(route('projet.echanges', $a))->assertForbidden();
        $this->actingAs($this->makeStudent(['DG'])->user)->get(route('etudiant.projet', $a))->assertForbidden();
    }

    public function test_un_message_vide_ou_trop_long_ne_cree_pas_de_notification(): void
    {
        $this->seedBase();
        Notification::fake();
        $s = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Logo', 'brief' => 'Brief', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'status' => 'afaire', 'due_on' => now()->addDays(5)]);
        $page = Livewire::actingAs($admin)->test(ProjetEchanges::class, ['assignment' => $a]);
        $page->set('message', '   ')->call('envoyer')->assertHasErrors('message');
        $page->set('message', str_repeat('x', 2001))->call('envoyer')->assertHasErrors('message');
        $this->assertSame(0, $a->comments()->count());
        Notification::assertNothingSent();
    }
}
