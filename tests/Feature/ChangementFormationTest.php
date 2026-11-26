<?php

namespace Tests\Feature;

use App\Livewire\Admin\EtudiantFiche;
use App\Livewire\Admin\Finances;
use App\Models\Formation;
use App\Notifications\Avis;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ChangementFormationTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_premier_versement_est_deduit_et_actualise_dans_l_espace_etudiant(): void
    {
        Notification::fake();
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $total = $s->totalDue();
        $page = Livewire::actingAs($s->user)->test(\App\Livewire\Etudiant\Paiements::class)
            ->assertSeeHtml('wire:poll.15s.visible')->assertViewHas('s', fn ($s) => $s->balance() === $total);
        app(PaymentService::class)->record($s->fresh(), 95000, 'Espèces');
        $page->call('$refresh')->assertSee('Versements déduits, premier versement inclus')
            ->assertSee(fcfa($total - 95000))->assertViewHas('s', fn ($s) => $s->totalPaid() === 95000 && $s->balance() === $total - 95000
                && array_sum($s->allocation()) === 95000);
        app(PaymentService::class)->record($s->fresh(), $total - 95000, 'Espèces');
        $page->call('$refresh')->assertSee('Formation soldée')
            ->assertViewHas('s', fn ($s) => $s->balance() === 0 && $s->totalPaid() === $total);
    }

    public function test_ajouter_remplacer_et_retirer_une_formation_recalcule_les_montants(): void
    {
        Notification::fake();
        $this->seedBase();
        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin);
        $dg = Formation::where('code', 'DG')->firstOrFail();
        $cm = Formation::where('code', 'CM')->firstOrFail();
        $gia = Formation::where('code', 'GIA')->firstOrFail();
        $s = $this->makeStudent(['DG']);
        app(PaymentService::class)->record($s->fresh(), 95000, 'Espèces');

        $page = Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->set('onglet', 'paiements')
            ->assertSee('Formations et montants')->call('gererFormations', 'ajouter')->set('nouvelle', $cm->id)
            ->assertSee('Nouveau total global')->assertSee(fcfa($dg->price + $cm->price))->assertSee(fcfa($dg->price + $cm->price - 95000));
        $page->call('confirmerInscription')->assertHasNoErrors()->assertSet('inscription', false);

        $s = $s->fresh(['formations', 'schedules', 'payments']);
        $this->assertSame(['DG', 'CM'], $s->formations->pluck('code')->all());
        $this->assertSame($dg->price + $cm->price, $s->totalDue());
        $this->assertSame(95000, $s->totalPaid());
        $this->assertSame($dg->price + $cm->price - 95000, $s->balance());
        $this->assertSame(4, $s->schedules->count());
        Notification::assertSentTo($s->user, Avis::class, fn ($n, $c) => $n->title === 'Votre inscription a été mise à jour' && in_array('mail', $c, true));
        $page->assertSee('Total global')->assertSee(fcfa($s->balance()));

        /* Remplacer DG par GIA : l'acompte déjà versé reste acquis et est reporté */
        $page->call('gererFormations', 'remplacer', $dg->id)->set('nouvelle', $gia->id)->call('confirmerInscription')->assertHasNoErrors();
        $s = $s->fresh(['formations', 'schedules', 'payments']);
        $this->assertEqualsCanonicalizing(['CM', 'GIA'], $s->formations->pluck('code')->all());
        $this->assertSame($cm->price + $gia->price, $s->totalDue());
        $this->assertSame($cm->price + $gia->price - 95000, $s->balance());
        $this->assertSame(95000, array_sum($s->allocation()));

        /* Retirer CM ; impossible de retirer la dernière formation ; pas de doublon */
        $page->call('gererFormations', 'retirer', $cm->id)->call('confirmerInscription')->assertHasNoErrors();
        $this->assertSame(['GIA'], $s->fresh('formations')->formations->pluck('code')->all());
        $page->call('gererFormations', 'retirer', $gia->id)->call('confirmerInscription')->assertHasErrors('ancienne');
        $page->call('gererFormations', 'ajouter')->set('nouvelle', $gia->id)->call('confirmerInscription')->assertHasErrors('nouvelle');
        $this->assertSame($gia->price - 95000, $s->fresh(['schedules', 'payments'])->balance());

        $this->actingAs($this->makeStaff('formateur', 'DG'));
        Livewire::test(EtudiantFiche::class, ['student' => $s])->call('gererFormations')->assertForbidden();
    }

    public function test_le_bouton_ajouter_une_depense_est_visible_dans_les_onglets_depenses_et_caisse(): void
    {
        $this->seedBase();
        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin)->get(route('admin.finances'))->assertOk()->assertSee('Ajouter une dépense');
        Livewire::actingAs($admin)->test(Finances::class)->set('vue', 'depenses')->assertSeeHtml('wire:click="nouvelleDepense"')
            ->set('vue', 'caisse')->assertSeeHtml('wire:click="nouvelleDepense"')->call('nouvelleDepense')->assertSet('depenseForm', true)->assertSee('Enregistrer une dépense');
    }
}
