<?php

namespace Tests\Feature;

use App\Http\Controllers\Site\PageController;
use App\Models\Candidature;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_toutes_les_pages_du_site_s_affichent(): void
    {
        $this->get('/')->assertOk()->assertSee('EVC');
        foreach (PageController::PAGES as $page) {
            $this->get('/'.$page)->assertOk();
        }
        $this->get('/actualites')->assertOk()->assertSee('Les actualités');
        $this->get('/actualites/promo-2026')->assertOk()->assertSee('promo 2026', false);
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
    }

    public function test_les_anciennes_adresses_html_sont_redirigees(): void
    {
        $this->get('/formations.html')->assertRedirect('/formations');
        $this->get('/index.html')->assertRedirect('/');
        $this->get('/actualite-promo-2026.html')->assertRedirect(route('site.actualite', 'promo-2026'));
    }

    public function test_la_preinscription_cree_une_candidature_avec_ses_pieces(): void
    {
        Storage::fake('local');
        Notification::fake();
        $res = $this->postJson('/preinscription', [
            'answers' => ['Nom' => ['Diabaté'], 'Prénoms' => ['Salimata'], 'E-mail' => ['Salimata@Exemple.ci'], 'Téléphone' => ['+225 07 00 00 00 00'],
                'Formations' => ['Infographie & design graphique', 'Bureautique & informatique (GIA)'], 'Modalité' => ['En ligne']],
            'fichiers' => ['Photo' => [UploadedFile::fake()->image('photo.jpg')]],
        ])->assertOk()->assertJsonStructure(['reference', 'suivi']);

        $c = Candidature::firstOrFail();
        $this->assertSame(['DG', 'GIA'], $c->formations);
        $this->assertSame('salimata@exemple.ci', $c->email);
        $this->assertSame($res['reference'], $c->reference);
        $this->assertCount(1, $c->files);
        Storage::disk('local')->assertExists($c->files[0]->path);
    }

    public function test_la_preinscription_refuse_un_dossier_incomplet(): void
    {
        $this->postJson('/preinscription', ['answers' => ['Nom' => ['X']]])->assertStatus(422)->assertJsonValidationErrors(['answers.E-mail.0', 'answers.Formations']);
    }

    public function test_le_suivi_exige_la_reference_et_l_email(): void
    {
        $c = Candidature::create(['reference' => 'PRE-2026-0099', 'first_name' => 'Awa', 'last_name' => 'K', 'email' => 'awa@exemple.ci', 'phone' => '1', 'formations' => ['CM']]);
        $this->get('/candidature/suivi?reference=PRE-2026-0099&email=autre@exemple.ci')->assertOk()->assertSee('Aucune candidature');
        $this->get('/candidature/suivi?reference=PRE-2026-0099&email=awa@exemple.ci')->assertOk()->assertSee('Bonjour Awa');
        $this->get(\App\Http\Controllers\Site\FormController::suiviUrl($c))->assertOk()->assertSee('Bonjour Awa');
    }

    public function test_contact_newsletter_et_verification_de_certificat(): void
    {
        $this->postJson('/contact', ['Nom' => 'Brice', 'E-mail' => 'brice@exemple.ci', 'Message' => 'Bonjour'])->assertOk();
        $this->assertSame('Brice', ContactMessage::first()->name);
        $this->postJson('/newsletter', ['email' => 'Lecteur@Exemple.ci'])->assertOk();
        $this->postJson('/newsletter', ['email' => 'lecteur@exemple.ci'])->assertOk();
        $this->assertSame(1, NewsletterSubscriber::count());

        $s = $this->makeStudent();
        Certificate::create(['code' => 'EVC-CERT-2026-0451', 'student_id' => $s->id, 'formation_id' => $s->formations[0]->id, 'issued_on' => now()]);
        $this->get('/certificats/verifier?reference=evc cert 2026 0451')->assertOk()->assertSee('Document authentique')->assertSee($s->user->name);
        $this->get('/certificats/verifier?reference=FAUX-123')->assertOk()->assertSee('Aucun document');
    }
}
