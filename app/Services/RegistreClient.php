<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class RegistreClient
{
    public function configured(): bool
    {
        return filled(config('services.registre.url')) && strlen((string) config('services.registre.token')) >= 32;
    }

    public function request(string $method, string $path, array $data = []): Response
    {
        if (! $this->configured()) {
            throw ValidationException::withMessages(['registre' => 'La connexion au registre officiel n’est pas configurée.']);
        }
        $url = rtrim((string) config('services.registre.url'), '/');
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host || parse_url($url, PHP_URL_USER) || parse_url($url, PHP_URL_PASS)
            || ! in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true)
            || (parse_url($url, PHP_URL_SCHEME) !== 'https' && ! in_array($host, ['localhost', '127.0.0.1', '[::1]'], true))) {
            throw ValidationException::withMessages(['registre' => 'Le registre nécessite une URL HTTPS, sauf en développement local.']);
        }
        try {
            $client = Http::baseUrl($url.'/api/v1')->withToken(config('services.registre.token'))->acceptJson()
                ->connectTimeout(3)->timeout((int) config('services.registre.timeout', 10))->withoutRedirecting();
            $response = $method === 'post' ? $client->post($path, $data) : $client->get($path, $data);
        } catch (ConnectionException) {
            throw ValidationException::withMessages(['registre' => 'Le registre est temporairement inaccessible. Réessayez plus tard.']);
        }
        if (! $response->successful()) {
            $message = match ($response->status()) {
                401, 403 => 'La connexion au registre a été refusée. Vérifiez le jeton partagé.',
                404 => 'Le dossier ou le document n’est plus accessible dans le registre.',
                409 => 'Le document officiel n’est pas disponible ou son intégrité doit être vérifiée dans le registre.',
                default => 'Le registre n’a pas pu traiter la demande. Aucun certificat local n’a été délivré.',
            };
            throw ValidationException::withMessages(['registre' => $message]);
        }

        return $response;
    }

    public function json(string $method, string $path, array $data = []): array
    {
        $json = $this->request($method, $path, $data)->json();
        if (! is_array($json)) {
            throw ValidationException::withMessages(['registre' => 'La réponse du registre est invalide.']);
        }

        return $json;
    }
}
