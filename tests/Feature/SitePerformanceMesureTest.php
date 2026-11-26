<?php

namespace Tests\Feature;

use App\Models\Candidature;
use App\Models\News;
use App\Support\ResponsiveImages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitePerformanceMesureTest extends TestCase
{
    use RefreshDatabase;

    private const PROD = 'https://www.ecolevirtuelledescreatifs.com';

    public function test_sitemap_canonical_et_og_image_utilisent_app_url_et_non_l_hote_local(): void
    {
        $this->seedBase();
        config(['app.url' => self::PROD]);
        $news = News::published()->firstOrFail();

        $sitemap = $this->get('http://127.0.0.1:8000/sitemap.xml')->assertOk()->getContent();
        $this->assertStringNotContainsString('127.0.0.1', $sitemap);
        $this->assertStringContainsString('<loc>'.self::PROD.'/formation-design</loc>', $sitemap);
        $this->assertStringContainsString('<loc>'.self::PROD.'/actualites/'.$news->slug.'</loc>', $sitemap);

        $html = $this->get('http://127.0.0.1:8000/actualites/'.$news->slug)->assertOk()->getContent();
        $head = explode('</head>', $html)[0];
        $this->assertStringNotContainsString('127.0.0.1', $head);
        $this->assertStringContainsString('<link rel="canonical" href="'.self::PROD.'/actualites/'.$news->slug.'">', $head);
        $this->assertStringContainsString('<meta property="og:image" content="'.self::PROD.$news->cover.'">', $head);
    }

    public function test_bandeau_cookies_et_mesure_seulement_si_configures(): void
    {
        $this->get('/')->assertOk()->assertDontSee('id="ckBanner"', false)->assertDontSee('EVC_ANALYTICS', false)
            ->assertDontSee('googletagmanager.com', false)->assertSee('assets/site/js/consent.js', false);

        config(['services.analytics.ga4' => 'G-TEST1234', 'services.analytics.meta_pixel' => '123456789']);
        $html = $this->get('/')->assertOk()->assertSee('id="ckBanner"', false)->assertSee('Tout refuser')->assertSee('Tout accepter')
            ->assertSee('data-ck-open', false)->getContent();
        $this->assertStringContainsString('"ga4":"G-TEST1234"', $html);
        $this->assertStringNotContainsString('googletagmanager.com/gtag', $html, 'GA4 ne doit être chargé qu’après consentement, par le script.');
    }

    public function test_la_provenance_de_campagne_est_enregistree_avec_la_preinscription(): void
    {
        $this->seedBase();
        $this->get('/formation-design?utm_source=facebook&utm_medium=paid_social&utm_campaign=rentree-2027&fbclid=abc')->assertOk();
        $this->get('/preinscription')->assertOk();
        $this->postJson('/preinscription', ['answers' => ['Nom' => ['Kouassi'], 'Prénoms' => ['Awa'], 'E-mail' => ['awa@example.test'],
            'Téléphone' => ['0700000000'], 'Date souhaitée d’inscription et de paiement' => [now()->toDateString()], 'Formations' => ['Infographie & design graphique']]])->assertOk()->assertJsonPath('formations', ['DG']);

        $c = Candidature::firstOrFail();
        $this->assertSame(['facebook', 'paid_social', 'rentree-2027', 'Meta', '/formation-design'],
            [$c->attribution['source'], $c->attribution['medium'], $c->attribution['campaign'], $c->attribution['click'], $c->attribution['landing']]);
        $this->assertSame('facebook / paid_social · rentree-2027', $c->attributionLabel());
        $this->actingAs($this->makeStaff('Super Administrateur'))->get(route('admin.candidature', $c))->assertOk()->assertSee('facebook / paid_social · rentree-2027');
    }

    public function test_sans_campagne_la_provenance_reste_vide(): void
    {
        $this->seedBase();
        $this->postJson('/preinscription', ['answers' => ['Nom' => ['Kouassi'], 'Prénoms' => ['Awa'], 'E-mail' => ['awa@example.test'],
            'Téléphone' => ['0700000000'], 'Date souhaitée d’inscription et de paiement' => [now()->toDateString()], 'Formations' => ['Infographie & design graphique']]])->assertOk();
        $this->assertNull(Candidature::firstOrFail()->attribution);
        $this->assertSame('Accès direct ou non renseigné', Candidature::firstOrFail()->attributionLabel());
    }

    public function test_les_images_du_site_sont_servies_en_webp_avec_dimensions_et_srcset(): void
    {
        $this->assertFileExists(ResponsiveImages::manifestPath(), 'Lancer php artisan evc:images');
        $html = $this->get('/')->assertOk()->assertSee('assets/site/css/bootstrap.site.css', false)
            ->assertDontSee('bootswatch-cerulean/bootstrap.min.css', false)->getContent();
        preg_match_all('/<img\b[^>]*>/i', $html, $images);
        $this->assertNotEmpty($images[0]);
        foreach ($images[0] as $tag) {
            if (str_contains($tag, 'src=""')) {
                continue;
            }
            $this->assertMatchesRegularExpression('/\swidth="\d+" height="\d+"/', $tag);
            if (! str_contains($tag, '.svg')) {
                $this->assertMatchesRegularExpression('/src="\/assets\/site\/img\/opt\/[^"]+\.webp\?v=\d+"/', $tag);
                $this->assertStringContainsString(' srcset="', $tag);
                $this->assertStringContainsString(' sizes="', $tag);
            }
        }
        foreach (ResponsiveImages::manifest() as $image) {
            foreach ($image['variants'] ?? [] as $url) {
                $this->assertFileExists(public_path(strtok(ltrim($url, '/'), '?')));
            }
        }
        $this->assertStringContainsString('<meta property="og:image" content="'.rtrim(config('app.url'), '/').'/assets/site/img/ceremonie.jpg">', $html);
    }

    public function test_le_portail_myevc_n_est_pas_reecrit(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('assets/site/img/opt/', false);
    }

    public function test_un_fichier_refuse_par_php_donne_un_message_clair(): void
    {
        $this->seedBase();
        $this->get('/preinscription')->assertOk()->assertSee('data-max-file="'.\App\Http\Controllers\Site\FormController::uploadLimits()['file'].'"', false);
        $path = tempnam(sys_get_temp_dir(), 'evc');
        file_put_contents($path, 'x');
        $file = new \Illuminate\Http\UploadedFile($path, 'photo.jpg', 'image/jpeg', UPLOAD_ERR_INI_SIZE, true);
        $response = $this->post('/preinscription', ['answers' => ['Nom' => ['Kouassi'], 'Prénoms' => ['Awa'], 'E-mail' => ['awa@example.test'],
            'Téléphone' => ['0700000000'], 'Date souhaitée d’inscription et de paiement' => [now()->toDateString()], 'Formations' => ['Infographie & design graphique']], 'fichiers' => ['Photo' => [$file]]], ['Accept' => 'application/json']);
        $response->assertStatus(422);
        $message = collect($response->json('errors'))->flatten()->implode(' ');
        $this->assertStringContainsString('Un document joint n’a pas pu être reçu', $message);
        $this->assertStringNotContainsString('fichier fichier', $message);
        $this->assertSame(0, Candidature::count());
    }
}
