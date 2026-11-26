<?php

namespace Tests\Feature;

use App\Http\Controllers\Site\PageController;
use App\Models\Formation;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_toutes_les_pages_publiques_ont_un_titre_court_unique_et_un_graphe_valide(): void
    {
        $this->seedBase();
        $paths = array_merge(['/'], array_map(fn ($p) => '/'.$p, PageController::PAGES), ['/actualites'], News::published()->get()->map(fn ($n) => '/actualites/'.$n->slug)->all());
        $titles = [];
        foreach ($paths as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            $dom = $this->document($html);
            $title = $dom->getElementsByTagName('title')->item(0)->textContent;
            $this->assertLessThanOrEqual(60, mb_strlen($title), $path);
            $this->assertStringEndsWith(' | EVC Abidjan', $title);
            $titles[] = $title;
            $graph = $this->graph($dom);
            $this->assertContains('EducationalOrganization', $graph[0]['@type']);
            $this->assertContains('LocalBusiness', $graph[0]['@type']);
            $this->assertSame('Abidjan', $graph[0]['address']['addressLocality']);
            $breadcrumb = collect($graph)->firstWhere('@type', 'BreadcrumbList');
            if ($path === '/') {
                $this->assertNull($breadcrumb);
            } else {
                $this->assertGreaterThanOrEqual(2, count($breadcrumb['itemListElement']));
            }
            $this->assertStringNotContainsString('<h2', explode('<main', $html)[0]);
        }
        $this->assertCount(count($titles), array_unique($titles));
    }

    public function test_schema_cours_utilise_tarifs_et_durees_de_la_base(): void
    {
        $this->seedBase();
        foreach (\App\Support\SiteSeo::COURSES as $path => $code) {
            $f = Formation::where('code', $code)->firstOrFail();
            $f->update(['price' => 219000, 'months' => 5]);
            $html = $this->get('/'.$path)->assertOk()->getContent();
            $this->assertStringContainsString('219 000', $html);
            $this->assertStringContainsString('5 mois', $html);
            $dom = $this->document($html);
            $course = collect($this->graph($dom))->firstWhere('@type', 'Course');
            $this->assertSame('XOF', $course['offers']['priceCurrency']);
            $this->assertSame(219000, $course['offers']['price']);
            $this->assertSame('P5M', $course['timeRequired']);
        }
    }

    public function test_faq_reprend_les_questions_visibles_sans_confondre_les_modules(): void
    {
        $this->seedBase();
        $dom = $this->document($this->get('/formation-design')->assertOk()->getContent());
        $faq = collect($this->graph($dom))->firstWhere('@type', 'FAQPage');
        $this->assertCount(4, $faq['mainEntity']);
        $this->assertSame('Je pars de zéro, est-ce pour moi ?', $faq['mainEntity'][0]['name']);
        $this->assertNotEmpty($faq['mainEntity'][0]['acceptedAnswer']['text']);
        $graph = $this->graph($this->document($this->get('/mentions-legales')->assertOk()->getContent()));
        $this->assertNull(collect($graph)->firstWhere('@type', 'FAQPage'));
    }

    public function test_article_expose_auteur_dates_et_open_graph_sans_injection_json(): void
    {
        $this->seedBase();
        $n = News::firstOrFail();
        $n->update(['title' => 'Une annonce </script><script>alert(1)</script>']);
        $html = $this->get(route('site.actualite', $n))->assertOk()->getContent();
        $dom = $this->document($html);
        $article = collect($this->graph($dom))->firstWhere('@type', 'NewsArticle');
        $this->assertSame($n->title, $article['headline']);
        $this->assertSame('Rédaction EVC', $article['author']['name']);
        $this->assertSame($n->published_at->toAtomString(), $article['datePublished']);
        $this->assertStringContainsString('property="og:type" content="article"', $html);
        $this->assertStringContainsString('<time datetime="'.$n->published_at->toAtomString().'">', $html);
        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
    }

    public function test_sitemap_date_toutes_les_urls_et_exclut_les_brouillons(): void
    {
        $this->seedBase();
        $n = News::firstOrFail();
        $n->update(['published_at' => null]);
        $response = $this->get('/sitemap.xml')->assertOk();
        $xml = simplexml_load_string($response->getContent());
        $this->assertCount(29, $xml->url);
        foreach ($xml->url as $entry) {
            $this->assertNotEmpty((string) $entry->lastmod);
            $this->assertNotSame(route('site.actualite', $n), (string) $entry->loc);
        }
        $this->assertSame($response->getContent(), $this->get('/sitemap.xml')->getContent());
        $n->update(['published_at' => now()->subDay(), 'updated_at' => now()->subMonth()]);
        $xml = simplexml_load_string($this->get('/sitemap.xml')->assertOk()->getContent());
        $lastmod = null;
        foreach ($xml->url as $entry) {
            if ((string) $entry->loc === route('site.actualite', $n)) {
                $lastmod = (string) $entry->lastmod;
            }
        }
        $this->assertSame($n->published_at->toAtomString(), $lastmod);
        $n->update(['published_at' => now()->addDay()]);
        $this->get('/sitemap.xml')->assertDontSee(route('site.actualite', $n), false);
    }

    private function document(string $html): \DOMDocument
    {
        $previous = libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $dom;
    }

    private function graph(\DOMDocument $dom): array
    {
        $nodes = (new \DOMXPath($dom))->query('//script[@type="application/ld+json"]');
        $this->assertSame(1, $nodes->length);

        return json_decode($nodes->item(0)->textContent, true, 512, JSON_THROW_ON_ERROR)['@graph'];
    }
}
