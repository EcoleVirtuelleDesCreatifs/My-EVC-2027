<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Mémorise en session la provenance d'un visiteur du site (paramètres UTM, identifiant de clic, site référent). */
class CaptureCampaign
{
    public const KEY = 'evc_attribution';

    private const PARAMS = ['utm_source' => 'source', 'utm_medium' => 'medium', 'utm_campaign' => 'campaign', 'utm_content' => 'content', 'utm_term' => 'term'];

    private const CLICK_IDS = ['gclid' => 'Google Ads', 'fbclid' => 'Meta', 'ttclid' => 'TikTok', 'msclkid' => 'Microsoft Ads'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('GET') && ! $request->ajax() && ! $request->is('myevc', 'myevc/*', 'livewire/*') && $request->hasSession()) {
            $data = [];
            foreach (self::PARAMS as $param => $key) {
                if (is_string($value = $request->query($param)) && trim($value) !== '') {
                    $data[$key] = mb_substr(trim($value), 0, 120);
                }
            }
            foreach (self::CLICK_IDS as $param => $network) {
                if ($request->filled($param)) {
                    $data['click'] = $network;
                    $data['source'] ??= $network;
                    $data['medium'] ??= 'cpc';
                    break;
                }
            }
            $referrer = (string) parse_url((string) $request->headers->get('referer'), PHP_URL_HOST);
            if (! $data && $referrer !== '' && $referrer !== $request->getHost() && ! $request->session()->has(self::KEY)) {
                $data = ['source' => mb_substr($referrer, 0, 120), 'medium' => 'referral'];
            }
            if ($data) {
                $request->session()->put(self::KEY, $data + ['landing' => '/'.ltrim($request->path(), '/'), 'at' => now()->toAtomString()]);
            }
        }

        return $next($request);
    }
}
