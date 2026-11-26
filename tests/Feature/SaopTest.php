<?php

namespace Tests\Feature;

use App\Livewire\Admin\CandidatureFiche;
use App\Models\Candidature;
use App\Notifications\CandidatureMiseAJour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class SaopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    private function candidate(array $attributes = []): Candidature
    {
        return Candidature::create($attributes + ['reference' => Candidature::nextReference(), 'first_name' => 'Awa',
            'last_name' => 'Kouassi', 'email' => 'awa@example.test', 'phone' => '0700', 'formations' => ['DG'], 'stage' => 'eligible']);
    }

    public function test_acces_signe_et_eligibilite_obligatoires(): void
    {
        $c = $this->candidate();
        $this->get(route('candidature.questionnaire', $c))->assertForbidden();
        $this->postJson(route('candidature.questionnaire', $c), ['action' => 'start'])->assertForbidden();
        $other = $this->candidate();
        $this->get(str_replace('/'.$c->id.'/questionnaire', '/'.$other->id.'/questionnaire', $c->saopUrl()))->assertForbidden();
        $c->update(['stage' => 'recue']);
        $this->get($c->saopUrl())->assertForbidden();
    }

    public function test_demarrage_unique_et_rechargement_sans_remise_a_zero(): void
    {
        $c = $this->candidate();
        $url = $c->saopUrl();
        $this->get($url)->assertOk()->assertSee('Avant de commencer');
        $this->assertNull($c->fresh()->saop_started_at);
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => 'Réponse']])->assertStatus(409);
        $this->postJson($url, ['action' => 'start'])->assertOk()->assertJsonPath('remaining', 1800);
        $start = $c->fresh()->saop_started_at;
        $this->travel(5)->minutes();
        $this->postJson($url, ['action' => 'start'])->assertOk()->assertJsonPath('remaining', 1500);
        $this->assertTrue($start->equalTo($c->fresh()->saop_started_at));
        $this->get($url)->assertOk()->assertSee('Déclaration d’engagement');
    }

    public function test_sauvegarde_progressive_et_blocage_a_exactement_30_minutes(): void
    {
        $c = $this->candidate();
        $url = $c->saopUrl();
        $this->postJson($url, ['action' => 'start'])->assertOk();
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => 'Mon ordinateur personnel']])->assertOk();
        $this->postJson($url, ['action' => 'save', 'answers' => [2 => 'Connexion mobile']])->assertOk();
        $this->assertSame('Mon ordinateur personnel', $c->fresh()->saop_answers[1]);
        $this->travel(30)->minutes();
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => 'Modification tardive']])->assertOk()->assertJsonPath('closed', true);
        $this->assertSame('Mon ordinateur personnel', $c->fresh()->saop_answers[1]);
        $this->postJson($url, ['action' => 'start'])->assertJsonPath('closed', true);
        $this->get($url)->assertOk()->assertSee('Les 30 minutes sont écoulées');
        $this->assertTrue($c->fresh()->saopClosed());
    }

    public function test_soumission_exige_les_11_reponses_puis_verrouille(): void
    {
        $c = $this->candidate();
        $url = $c->saopUrl();
        $this->postJson($url, ['action' => 'start'])->assertOk();
        $this->postJson($url, ['action' => 'submit', 'answers' => [1 => 'Réponse']])->assertUnprocessable()->assertJsonValidationErrors('answers.11');
        $answers = array_fill(1, 11, 'Ma réponse personnelle');
        $this->postJson($url, ['action' => 'submit', 'answers' => $answers])->assertOk()->assertJsonPath('closed', true);
        $this->assertNotNull($c->fresh()->saop_submitted_at);
        $this->get($url)->assertOk()->assertSee('Questionnaire clôturé')->assertSee('Vous pouvez maintenant fermer cette page.')
            ->assertDontSee('id="questionnaire"', false)->assertDontSee('<textarea', false);
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => 'Modification']])->assertOk()->assertJsonPath('closed', true);
        $this->assertSame('Ma réponse personnelle', $c->fresh()->saop_answers[1]);
        $this->assertSame('eligible', $c->fresh()->stage);
    }

    public function test_validation_des_reponses_et_echappement_html(): void
    {
        $c = $this->candidate(['saop_started_at' => now()]);
        $url = $c->saopUrl();
        $this->postJson($url, ['action' => 'save', 'answers' => [99 => 'Invalide']])->assertUnprocessable();
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => ['Invalide']]])->assertUnprocessable();
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => str_repeat('a', 10001)]])->assertUnprocessable();
        $this->postJson($url, ['action' => 'save', 'answers' => [1 => '</textarea><script>alert(1)</script>']])->assertOk();
        $this->get($url)->assertSee('&lt;/textarea&gt;&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_mail_eligible_contient_le_lien_du_questionnaire(): void
    {
        $c = $this->candidate();
        $mail = (new CandidatureMiseAJour($c, 'eligible'))->toMail(new AnonymousNotifiable);
        $this->assertStringContainsString('Accéder à mon questionnaire SAOP', (string) $mail->render());
        $this->get($mail->viewData['questionnaireUrl'])->assertOk();
        $this->assertStringContainsString($mail->viewData['questionnaireUrl'], view($mail->view['text'], $mail->viewData)->render());
    }

    public function test_administration_consulte_et_valide_apres_cloture_uniquement(): void
    {
        Notification::fake();
        $admin = $this->makeStaff('Super Administrateur');
        $c = $this->candidate();
        $page = Livewire::actingAs($admin)->test(CandidatureFiche::class, ['candidature' => $c]);
        $page->call('etape', 'test');
        $this->assertSame('eligible', $c->fresh()->stage);
        Notification::assertNothingSent();
        $c->update(['saop_started_at' => now()->subMinutes(30), 'saop_answers' => [1 => 'Ordinateur partagé, besoin d’accompagnement']]);
        $page = Livewire::actingAs($admin)->test(CandidatureFiche::class, ['candidature' => $c]);
        $page->assertSee('Ordinateur partagé, besoin d’accompagnement')->assertSee('Temps écoulé');
        $page->call('etape', 'test');
        $this->assertSame('test', $c->fresh()->stage);
        Notification::assertSentOnDemand(CandidatureMiseAJour::class);
    }
}
