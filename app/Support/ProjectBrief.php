<?php

namespace App\Support;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class ProjectBrief
{
    private const SECTIONS = [
        'ANNONCEUR', 'PROBLÉMATIQUE', 'OBJECTIF', 'INDICATEURS DE RÉUSSITE', 'CIBLE', 'PLATEFORMES',
        'PROMESSE', 'TON', 'TEXTES FOURNIS', 'ACCROCHE', 'DONNÉES FOURNIES', 'CONTENU À INTÉGRER',
        'BRIEF CRÉATIF', 'CONTRAINTES', 'MISSION', 'LIVRABLES', 'OUTILS', 'RESSOURCES', 'COMPÉTENCES', 'VALIDATION',
    ];

    public static function sanitize(string $html): string
    {
        $config = (new HtmlSanitizerConfig())->allowLinkSchemes(['https', 'http', 'mailto'])->withMaxInputLength(100000);
        foreach (['p', 'div', 'br', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'ul', 'ol', 'li', 'blockquote', 'table', 'tbody', 'thead', 'td', 'details', 'summary'] as $tag) {
            $config = $config->allowElement($tag);
        }
        $config = $config->allowElement('a', ['href', 'title'])->allowElement('tr', ['data-section'])->allowElement('th', ['scope']);
        foreach (['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math', 'template'] as $tag) {
            $config = $config->dropElement($tag);
        }

        return (new HtmlSanitizer($config))->sanitize($html);
    }

    public static function fromText(string $text): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        foreach (['INDICATEURS DE RÉUSSITE', 'CONTENU À INTÉGRER', 'DONNÉES FOURNIES'] as $label) {
            $pattern = str_replace(' ', '\s+', preg_quote($label, '/'));
            $normalized = preg_replace('/^'.$pattern.'/mu', $label, $normalized);
        }
        $parts = explode('RÈGLES DE PRODUCTION COMMUNES', $normalized, 2);
        $pattern = '/^('.implode('|', array_map(fn ($label) => preg_quote($label, '/'), self::SECTIONS)).')(?=\s|$)[\t ]*/mu';
        preg_match_all($pattern, $parts[0], $matches, PREG_OFFSET_CAPTURE);
        if (count($matches[0]) < 3 || $matches[1][0][0] !== 'ANNONCEUR') {
            return nl2br(e($text));
        }
        $rows = [];
        foreach ($matches[0] as $i => [$match, $offset]) {
            $start = $offset + strlen($match);
            $end = $matches[0][$i + 1][1] ?? strlen($parts[0]);
            $rows[] = self::row($matches[1][$i][0], nl2br(e(trim(substr($parts[0], $start, $end - $start)))));
        }
        $html = '<table><tbody>'.implode('', $rows).'</tbody></table>';
        if (isset($parts[1])) {
            $html .= '<details><summary>Règles de production et grille d’évaluation</summary><div>'.nl2br(e('RÈGLES DE PRODUCTION COMMUNES'.$parts[1])).'</div></details>';
        }
        $metadata = trim(substr($parts[0], 0, $matches[0][0][1]));
        if ($metadata !== '') {
            $html .= '<details><summary>Informations du catalogue</summary><div>'.nl2br(e($metadata)).'</div></details>';
        }

        return $html;
    }

    public static function fromHtml(string $html): string
    {
        $safe = self::sanitize($html);
        if (preg_match('/<(?:table|h2|h3|strong|b|em|i|u|ul|ol|li|a|blockquote|details|summary)\b/i', $safe)) {
            return $safe;
        }
        $text = preg_replace('/<br\s*\/?\s*>\r?\n?/i', "\n", $safe);
        $text = preg_replace('/<\/(?:p|div)>/i', "\n", $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $formatted = self::fromText($text);

        return str_starts_with($formatted, '<table>') ? $formatted : $safe;
    }

    public static function template(): string
    {
        $labels = array_values(array_diff(self::SECTIONS, ['ACCROCHE', 'DONNÉES FOURNIES', 'BRIEF CRÉATIF']));

        return '<table><tbody>'.implode('', array_map(fn ($label) => self::row($label, '<p><br></p>'), $labels)).'</tbody></table>';
    }

    private static function row(string $label, string $html): string
    {
        $section = match ($label) {
            'PROBLÉMATIQUE' => 'problematique', 'TEXTES FOURNIS' => 'textes-fournis', 'ACCROCHE' => 'accroche', default => 'rubrique',
        };

        return '<tr data-section="'.$section.'"><th scope="row">'.e($label).'</th><td>'.$html.'</td></tr>';
    }

    public static function heading(string $title): array
    {
        if (preg_match('/^((?:[A-Z]{2,5}-[0-9]{2,})|(?:PM-[A-C]))\s*(?:[—–-]\s*)?(.*)$/u', $title, $matches)) {
            return ['code' => $matches[1], 'title' => trim($matches[2])];
        }

        return ['code' => 'PROJET', 'title' => $title];
    }

    public static function text(string $html): string
    {
        $html = preg_replace('/<th\b[^>]*>.*?<\/th>/is', '', $html);
        $html = preg_replace('/<br\s*\/?\s*>|<\/(?:p|div|h2|h3|li|ul|ol|blockquote|td|tr|summary)>/i', ' ', $html);

        return trim(preg_replace('/[\s\x{00A0}\x{200B}-\x{200D}\x{FEFF}]+/u', ' ', html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
