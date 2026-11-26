<?php

namespace Tests\Feature;

use App\Livewire\Admin\CandidatureFiche;
use App\Models\Candidature;
use App\Models\Payment;
use App\Models\Promotion;
use App\Notifications\CompteActive;
use App\Services\AdmissionPaymentService;
use App\Services\AdmissionService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class PremierVersementAdmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Notification::fake();
        $this->actingAs($this->makeStaff('Super Administrateur'));
    }

    private function candidature(array $attributes = []): Candidature
    {
        return Candidature::create($attributes + ['reference' => Candidature::nextReference(), 'first_name' => 'Awa', 'last_name' => 'Koné',
            'email' => 'awa@example.test', 'phone' => '0700000000', 'formations' => ['DG'], 'answers' => [], 'stage' => 'test']);
    }

    private function details(): array
    {
        return ['amount' => 95000, 'method' => 'Wave', 'paid_on' => now()->toDateString(), 'transaction_ref' => 'WAVE-123'];
    }

    public function test_le_premier_versement_valide_est_deduit_a_l_activation_sans_doublon(): void
    {
        $c = $this->candidature();
        $page = Livewire::test(CandidatureFiche::class, ['candidature' => $c])->call('etape', 'paiement')
            ->assertSet('validerVersement', true)->set('premier', $this->details())->call('confirmerPremierVersement')->assertHasNoErrors();
        $this->assertSame('paiement', $c->fresh()->stage);
        $this->assertSame(0, Payment::count());
        $page->set('promotion', Promotion::current()->id)->call('confirmerActivation')->assertRedirect();
        $c->refresh();
        $s = $c->student;
        $this->assertSame(95000, $s->totalPaid());
        $this->assertSame(90000, $s->balance());
        $this->assertSame('WAVE-123', $c->firstPayment->transaction_ref);
        $this->assertSame($s->id, $c->firstPayment->student_id);
        $this->assertSame(95000, array_sum($s->allocation()));
        app(AdmissionService::class)->activate($c, Promotion::current());
        app(AdmissionPaymentService::class)->sync($c, $s);
        $this->assertSame(1, Payment::count());
        Notification::assertSentToTimes($s->user, CompteActive::class, 1);
        $this->actingAs($s->user)->get(route('etudiant.paiements'))->assertOk()->assertSee(fcfa(95000))->assertSee(fcfa(90000));
    }

    public function test_une_candidature_ancienne_ne_doit_pas_inventer_un_encaissement(): void
    {
        $c = $this->candidature(['stage' => 'paiement']);
        Livewire::test(CandidatureFiche::class, ['candidature' => $c])->call('confirmerActivation')->assertHasErrors('premierVersement');
        $this->assertSame(0, Payment::count());
    }

    public function test_un_compte_deja_active_peut_etre_regularise_sans_recreer_le_compte(): void
    {
        $s = $this->makeStudent(['DG']);
        $c = $this->candidature(['stage' => 'activee', 'student_id' => $s->id, 'email' => $s->user->email]);
        $service = app(AdmissionPaymentService::class);
        $service->confirm($c, $this->details());
        $service->confirm($c, $this->details());
        $this->assertSame(1, $s->payments()->count());
        $this->assertSame(90000, $s->fresh()->balance());
        $this->assertSame('activee', $c->fresh()->stage);
        $this->assertSame($s->id, $c->fresh()->student_id);
        Notification::assertNotSentTo($s->user, CompteActive::class);
    }

    public function test_rattacher_un_recu_existant_ne_compte_pas_deux_fois_le_paiement(): void
    {
        $s = $this->makeStudent(['DG']);
        $p = app(PaymentService::class)->record($s, 95000, 'Espèces');
        $c = $this->candidature(['stage' => 'activee', 'student_id' => $s->id, 'email' => $s->user->email]);
        Livewire::test(CandidatureFiche::class, ['candidature' => $c])->call('ouvrirPremierVersement')->assertSet('typeVersement', 'existant')
            ->set('paiementExistant', $p->id)->call('confirmerPremierVersement')->assertHasNoErrors();
        $this->assertSame($p->id, $c->fresh()->first_payment_id);
        $this->assertSame(1, $s->payments()->count());
        $this->assertSame(95000, $s->fresh()->totalPaid());
    }

    public function test_un_recu_d_un_autre_etudiant_ne_peut_pas_etre_rattache(): void
    {
        $s = $this->makeStudent(['DG']);
        $other = $this->makeStudent(['DG']);
        $payment = app(PaymentService::class)->record($other, 95000, 'Wave');
        $c = $this->candidature(['stage' => 'activee', 'student_id' => $s->id, 'email' => $s->user->email]);
        try {
            app(AdmissionPaymentService::class)->confirm($c, [], $payment->id);
            $this->fail('Le reçu d’un autre étudiant doit être refusé.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertNull($c->fresh()->first_payment_id);
            $this->assertSame(0, $s->payments()->count());
        }
    }

    public function test_un_formateur_ne_peut_pas_valider_un_encaissement(): void
    {
        $c = $this->candidature();
        $this->actingAs($this->makeStaff('formateur', 'DG'));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(AdmissionPaymentService::class)->confirm($c, $this->details());
    }

    public function test_le_montant_et_le_moyen_de_paiement_sont_obligatoires(): void
    {
        $c = $this->candidature();
        Livewire::test(CandidatureFiche::class, ['candidature' => $c])->call('ouvrirPremierVersement')
            ->set('premier.amount', 0)->set('premier.method', '')->call('confirmerPremierVersement')
            ->assertHasErrors(['premier.amount', 'premier.method']);
        $this->assertNull($c->fresh()->first_payment);
        $this->assertSame('test', $c->fresh()->stage);
    }
}
