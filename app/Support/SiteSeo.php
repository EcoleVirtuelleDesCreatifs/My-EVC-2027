<?php

namespace App\Support;

use App\Models\Formation;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Support\Str;

class SiteSeo
{
    public const COURSES = ['formation-design' => 'DG', 'formation-community-management' => 'CM', 'formation-bureautique' => 'GIA'];

    public static function make(string $rawTitle, string $description, string $content, ?News $news = null, ?Formation $formation = null): array
    {
        $page = trim(request()->path(), '/');
        $label = self::text(preg_replace('/ · EVC — École Virtuelle des Créatifs$/u', '', $rawTitle));
        $formation ??= isset(self::COURSES[$page]) ? Formation::where('code', self::COURSES[$page])->first() : null;
        $baseTitle = match ($page) {
            '' => 'École des métiers du numérique',
            'formation-design' => 'Formation infographie & design graphique',
            'formation-community-management' => 'Formation community management'.($formation ? ' ('.$formation->months.' mois)' : ''),
            'formation-bureautique' => 'Formation bureautique'.($formation ? ' ('.$formation->months.' mois)' : ''),
            'conditions-generales-vente' => 'Conditions d’inscription et de vente',
            default => $label,
        };
        $title = Str::limit($baseTitle, 45, '…').' | EVC Abidjan';
        $url = self::absolute(request()->path());
        $organization = self::absolute('/').'#organization';
        $graph = [[
            '@type' => ['EducationalOrganization', 'LocalBusiness'], '@id' => $organization,
            'name' => 'École Virtuelle des Créatifs', 'alternateName' => 'EVC', 'url' => self::absolute('/'),
            'logo' => self::absolute('/assets/site/img/logo.png'), 'email' => Setting::get('school_email'),
            'telephone' => Setting::get('phone'),
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Abidjan', 'addressCountry' => 'CI'],
        ]];
        $crumbs = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => self::absolute('/')]];
        if ($news || $formation) {
            $crumbs[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $news ? 'Actualités' : 'Formations', 'item' => self::absolute($news ? '/actualites' : '/formations')];
        }
        if ($page !== '') {
            $crumbs[] = ['@type' => 'ListItem', 'position' => count($crumbs) + 1, 'name' => $news?->title ?? $label, 'item' => $url];
        }
        $webPage = ['@type' => 'WebPage', '@id' => $url.'#webpage', 'url' => $url, 'name' => $title, 'description' => self::text($description), 'inLanguage' => 'fr', 'publisher' => ['@id' => $organization]];
        if (count($crumbs) > 1) {
            $graph[] = ['@type' => 'BreadcrumbList', '@id' => $url.'#breadcrumb', 'itemListElement' => $crumbs];
            $webPage['breadcrumb'] = ['@id' => $url.'#breadcrumb'];
        }
        $graph[] = $webPage;
        if ($formation) {
            $graph[] = ['@type' => 'Course', '@id' => $url.'#course', 'url' => $url, 'name' => $formation->name,
                'description' => $formation->pitch, 'provider' => ['@id' => $organization], 'inLanguage' => 'fr',
                'timeRequired' => 'P'.(int) $formation->months.'M',
                'offers' => ['@type' => 'Offer', 'price' => (int) $formation->price, 'priceCurrency' => 'XOF', 'url' => $url,
                    'availability' => 'https://schema.org/'.($formation->active ? 'InStock' : 'OutOfStock')]];
        }
        if ($news) {
            $graph[] = ['@type' => 'NewsArticle', '@id' => $url.'#article', 'headline' => $news->title, 'description' => $news->excerpt,
                'image' => [self::absolute($news->cover)], 'datePublished' => $news->published_at->toAtomString(),
                'dateModified' => ($news->updated_at ?? $news->published_at)->max($news->published_at)->toAtomString(),
                'author' => ['@type' => 'Organization', 'name' => 'Rédaction EVC', 'url' => self::absolute('/ecole')],
                'publisher' => ['@id' => $organization], 'mainEntityOfPage' => ['@id' => $url.'#webpage'], 'inLanguage' => 'fr'];
        }
        $dom = new \DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>'.$content.'</body></html>', LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new \DOMXPath($dom);
        $questions = [];
        foreach ($xpath->query('//*[starts-with(@id,"faq")]//*[contains(concat(" ",normalize-space(@class)," ")," accordion-item ")]') as $item) {
            $question = $xpath->query('.//button[contains(@class,"accordion-button")]', $item)->item(0);
            $answer = $xpath->query('.//*[contains(concat(" ",normalize-space(@class)," ")," accordion-body ")]', $item)->item(0);
            if ($question && $answer && self::text($answer->textContent) !== '') {
                $questions[] = ['@type' => 'Question', 'name' => self::text($question->textContent), 'acceptedAnswer' => ['@type' => 'Answer', 'text' => self::text($answer->textContent)]];
            }
        }
        if ($questions) {
            $graph[] = ['@type' => 'FAQPage', '@id' => $url.'#faq', 'mainEntity' => $questions];
        }

        return ['title' => $title, 'description' => self::text($description), 'graph' => ['@context' => 'https://schema.org', '@graph' => $graph]];
    }

    /** URL publique absolue, construite depuis APP_URL et non depuis l'hôte de la requête. */
    public static function absolute(?string $path = '/'): string
    {
        if (preg_match('#^https?://#i', (string) $path)) {
            return $path;
        }
        $path = '/'.ltrim((string) parse_url((string) $path, PHP_URL_PATH), '/');

        return rtrim((string) config('app.url'), '/').$path;
    }

    private static function text(string $text): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
