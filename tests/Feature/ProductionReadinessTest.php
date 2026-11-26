<?php

namespace Tests\Feature;

use App\Models\Formation;
use App\Models\Resource;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\DemoSeeder;
use Database\Seeders\Phase2DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Notification::fake();
    }

    public function test_installation_standard_ne_cree_aucune_donnee_fictive(): void
    {
        $this->seed(DatabaseSeeder::class);
        foreach (['users', 'students', 'promotions', 'projects', 'payments', 'news', 'videos', 'quizzes', 'conversations'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
        $this->assertSame(3, Formation::count());
        $this->assertGreaterThan(0, Resource::count());
        $this->assertSame(0, Resource::whereNotNull('path')->count());
        $this->assertSame(0, (int) Resource::sum('downloads_count'));
        $this->assertSame([], Storage::disk('local')->allFiles());
        $dg = Formation::where('code', 'DG')->firstOrFail();
        $dg->update(['price' => 219000, 'active' => false]);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(219000, $dg->fresh()->price);
        $this->assertFalse($dg->fresh()->active);
    }

    public function test_accueil_affiche_uniquement_les_actualites_reellement_publiees(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->get('/')->assertOk()->assertDontSee('/actualites/rentree-2026', false);
        $n = \App\Models\News::create(['title' => 'Annonce officielle', 'slug' => 'annonce-officielle', 'category' => 'École', 'label' => 'Information', 'excerpt' => 'Information validée.', 'body' => 'Contenu validé.', 'cover' => '/assets/site/img/logo.png', 'published_at' => now()]);
        $this->get('/')->assertOk()->assertSee('Annonce officielle')->assertSee(route('site.actualite', $n), false);
        $n->update(['published_at' => null]);
        $this->get('/')->assertOk()->assertDontSee('Annonce officielle');
        $n->update(['published_at' => now()->addDay()]);
        $this->get('/')->assertOk()->assertDontSee('Annonce officielle');
    }

    public function test_les_liens_et_medias_locaux_du_site_public_sont_accessibles(): void
    {
        $this->seedBase();
        $paths = [];
        foreach (array_merge([''], \App\Http\Controllers\Site\PageController::PAGES) as $page) {
            $dom = new \DOMDocument;
            @$dom->loadHTML($this->get('/'.$page)->assertOk()->getContent());
            foreach ((new \DOMXPath($dom))->query('//*[@href or @src]') as $element) {
                foreach (['href', 'src'] as $attribute) {
                    $value = $element->getAttribute($attribute);
                    $url = parse_url($value);
                    if (is_array($url) && isset($url['path']) && str_starts_with($url['path'], '/') && (! isset($url['host']) || $url['host'] === parse_url(config('app.url'), PHP_URL_HOST))) {
                        $paths[$url['path']] = true;
                    }
                }
            }
        }
        foreach (array_keys($paths) as $path) {
            if (is_file(public_path(rawurldecode($path)))) {
                $this->assertFileIsReadable(public_path(rawurldecode($path)));
            } else {
                $this->assertLessThan(400, $this->get($path)->getStatusCode(), 'Lien ou média inaccessible : '.$path);
            }
        }
    }

    public function test_les_generateurs_de_demo_sont_interdits_en_production(): void
    {
        $this->app['env'] = 'production';
        foreach ([\Database\Seeders\ContentSeeder::class, DemoSeeder::class, Phase2DemoSeeder::class] as $seeder) {
            try {
                app($seeder)->run();
                $this->fail('Le générateur de démonstration doit être interdit en production.');
            } catch (\RuntimeException $e) {
                $this->assertStringContainsString('production', $e->getMessage());
            }
        }
        $this->assertDatabaseCount('users', 0);
    }

    public function test_connexion_et_pedagogie_n_exposent_plus_de_demo(): void
    {
        $this->seedBase();
        $this->get(route('login'))->assertOk()->assertDontSee('Comptes de démonstration')->assertDontSee('admin@evc.test')->assertDontSee('<code>password</code>', false);
        $this->get('/pedagogie')->assertOk()->assertDontSee('données fictives')->assertDontSee('78 %')->assertSee('href="'.route('login').'" class="btn btn-evc w-100"', false);
    }

    public function test_preinscription_affiche_les_tarifs_actuels_et_seulement_les_formations_ouvertes(): void
    {
        $this->seedBase();
        Formation::where('code', 'DG')->update(['price' => 219000]);
        Formation::where('code', 'CM')->update(['active' => false]);
        $this->get('/preinscription')->assertOk()->assertSee('data-price="219 000"', false)->assertDontSee('data-price="185 000"', false)->assertDontSee('data-key="cm"', false);
    }

    public function test_audit_repere_uniquement_les_fichiers_factices_sans_les_supprimer(): void
    {
        $this->seedBase();
        $path = \App\Support\DemoFile::make('Document factice', 'PDF');
        $r = Resource::create(['title' => 'Document factice', 'category' => 'ebooks', 'description' => 'Document de test', 'format' => 'PDF', 'size' => '1 Ko', 'licence' => 'Réservé EVC', 'path' => $path]);
        Storage::disk('local')->put('library/document-reel.pdf', '%PDF-1.4 Document authentique');
        Resource::create(['title' => 'Document réel', 'category' => 'ebooks', 'description' => 'Document validé', 'format' => 'PDF', 'size' => '1 Ko', 'licence' => 'Réservé EVC', 'path' => 'library/document-reel.pdf']);
        $this->assertSame([$r->id], \App\Support\ProductionAudit::demoResourceIds());
        $this->artisan('evc:audit-production')->assertExitCode(1);
        $this->assertDatabaseCount('resources', 2);
        Storage::disk('local')->assertExists($path);
    }

    public function test_documents_juridiques_reposent_sur_les_informations_officielles(): void
    {
        $this->seedBase();
        foreach (['mentions-legales', 'confidentialite', 'conditions-utilisation', 'conditions-generales-vente', 'reglement-interieur'] as $page) {
            $response = $this->get('/'.$page)->assertOk()->assertSee('CI-ABJ-03-2024-B12-00430')
                ->assertSee('info@ecolevirtuelledescreatifs.com')->assertSee('+225 07 47 25 95 07')
                ->assertDontSee('Document de travail')->assertDontSee('[à compléter]');
            $dom = new \DOMDocument;
            @$dom->loadHTML($response->getContent());
            $xpath = new \DOMXPath($dom);
            foreach ($xpath->query('//nav[@class="lg-toc"]/a') as $link) {
                $this->assertNotNull($dom->getElementById(substr($link->getAttribute('href'), 1)), $page);
            }
        }
        $this->get('/mentions-legales')->assertSee('1 000 000 FCFA')->assertSee('LWS')->assertSee('10 rue Penthièvre, 75008 Paris, France');
        $this->get('/confidentialite')->assertSee('13 mois')->assertSee('30 jours')->assertSee('2 ans après le dernier contact');
    }

    public function test_conditions_de_certification_et_frais_reprennent_le_reglement_officiel(): void
    {
        $this->seedBase();
        foreach (['reglement-interieur', 'conditions-generales-vente'] as $page) {
            $this->get('/'.$page)->assertOk()->assertSee('35 000 FCFA')->assertSee('5 350 FCFA')
                ->assertSee('une (1) semaine et trois (3) jours')->assertSee('rattrapage miroir')
                ->assertSee('attestation de fin de formation');
        }
        $this->get('/reglement-interieur')->assertSee('2 mois')->assertSee('lecture seule')->assertSee('rapport de fin de formation', false);
        $this->get('/conditions-generales-vente')->assertDontSee('14 jours')->assertDontSee('acompte non remboursable');
    }

    public function test_tarifs_des_conditions_de_vente_suivent_le_catalogue_actuel(): void
    {
        $this->seedBase();
        $this->travel(2)->days();
        Formation::where('code', 'DG')->update(['price' => 219000, 'months' => 5]);
        $response = $this->get('/conditions-generales-vente')->assertOk()->assertSee('219 000')->assertSee('5 mois')->assertSee('124 000');
        $dom = new \DOMDocument;
        @$dom->loadHTML($response->getContent());
        $this->assertStringNotContainsString('185 000', (new \DOMXPath($dom))->query('//table')->item(0)->textContent);
        $lastmod = null;
        foreach (simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent())->url as $entry) {
            if ((string) $entry->loc === url('/conditions-generales-vente')) {
                $lastmod = (string) $entry->lastmod;
            }
        }
        $this->assertSame(Formation::where('code', 'DG')->firstOrFail()->updated_at->toAtomString(), $lastmod);
    }

    public function test_formation_inconnue_ou_fermee_est_refusee_sans_dossier(): void
    {
        $this->seedBase();
        $data = ['answers' => ['Nom' => ['Kouassi'], 'Prénoms' => ['Awa'], 'E-mail' => ['awa@example.test'], 'Téléphone' => ['0700000000'], 'Date souhaitée d’inscription et de paiement' => [now()->toDateString()], 'Formations' => ['Formation inconnue']]];
        $this->postJson('/preinscription', $data)->assertUnprocessable()->assertJsonValidationErrors('answers.Formations');
        Formation::where('code', 'DG')->update(['active' => false]);
        $data['answers']['Formations'] = ['Infographie & design graphique'];
        $this->postJson('/preinscription', $data)->assertUnprocessable()->assertJsonValidationErrors('answers.Formations');
        $this->assertDatabaseCount('candidatures', 0);
        Notification::assertNothingSent();
    }
}
