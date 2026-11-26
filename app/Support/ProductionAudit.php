<?php

namespace App\Support;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ProductionAudit
{
    public static function demoResourceIds(): array
    {
        $disk = Storage::disk('local');
        $ids = [];
        foreach (Resource::whereNotNull('path')->get(['id', 'path']) as $resource) {
            if (! $disk->exists($resource->path) || $disk->size($resource->path) > 1048576) {
                continue;
            }
            $extension = strtolower(pathinfo($resource->path, PATHINFO_EXTENSION));
            $demo = false;
            if ($extension === 'pdf') {
                $demo = str_contains($disk->get($resource->path), 'Fichier de demonstration : le document definitif est depose par l\'ecole.');
            } elseif ($extension === 'zip') {
                $zip = new ZipArchive;
                if ($zip->open($disk->path($resource->path), ZipArchive::RDONLY) === true) {
                    $notice = $zip->getFromName('LISEZ-MOI.txt', 4096);
                    $demo = is_string($notice) && str_contains($notice, 'Fichier de démonstration de la bibliothèque MY EVC');
                    $zip->close();
                }
            }
            if ($demo) {
                $ids[] = $resource->id;
            }
        }

        return $ids;
    }

    public static function checks(): array
    {
        $missing = Resource::whereNotNull('path')->get(['path'])->filter(fn ($r) => ! Storage::disk('local')->exists($r->path))->count();
        $legal = collect(['mentions-legales', 'confidentialite', 'conditions-utilisation', 'conditions-generales-vente', 'reglement-interieur'])
            ->filter(fn ($page) => str_contains(file_get_contents(resource_path('views/site/pages/'.$page.'.blade.php')), '[à compléter]'))->count();
        $demoIds = self::demoResourceIds();
        $checks = [
            ['Comptes utilisant evc.test', User::where('email', 'like', '%@evc.test')->count(), 'À remplacer par les comptes officiels, sans perdre l’accès administrateur.'],
            ['Candidatures au domaine de test', \App\Models\Candidature::where('email', 'like', '%@evc.test')->count(), 'Contrôler leur origine avant de retirer ces dossiers.'],
            ['QCM publiés par les comptes de test', \App\Models\Quiz::where('published', true)->whereIn('created_by', User::where('email', 'like', '%@evc.test')->select('id'))->count(), 'Faire valider ou dépublier les contenus du jeu d’essai.'],
            ['Ressources de démonstration publiées', Resource::whereIn('id', $demoIds)->where('published', true)->count(), 'À retirer de la publication après validation du périmètre.'],
            ['Fichiers de bibliothèque manquants', $missing, 'Remplacer le fichier ou retirer la ressource de la publication.'],
            ['Pages juridiques restant à valider', $legal, 'Renseigner les informations officielles et faire valider les textes.'],
            ['Environnement non production', (int) ! app()->isProduction(), 'APP_ENV doit valoir production sur le serveur public.'],
            ['Mode debug actif', (int) config('app.debug'), 'APP_DEBUG doit être désactivé sur le serveur public.'],
            ['Transport e-mail sans livraison', (int) in_array(config('mail.default'), [null, 'log', 'array'], true), 'Configurer et vérifier un transport SMTP ou transactionnel.'],
            ['Registre officiel non configuré', (int) (! config('services.registre.url') || ! config('services.registre.token')), 'Configurer la connexion privée au registre avant de délivrer les certificats.'],
            ['Adresse de production non HTTPS', (int) ! str_starts_with((string) config('app.url'), 'https://'), 'Configurer le domaine public et HTTPS avant mise en service.'],
            ['Adresse publique locale', (int) (bool) preg_match('#^https?://(localhost|127\.|0\.0\.0\.0|\[::1\])#i', (string) config('app.url')), 'APP_URL doit contenir le domaine public : le sitemap et les images de partage en dépendent.'],
            ['Mesure d’audience non configurée', (int) (! config('services.analytics.ga4') && ! config('services.analytics.meta_pixel')), 'Renseigner GA4_MEASUREMENT_ID et/ou META_PIXEL_ID pour suivre les campagnes.'],
        ];
        if (PHP_SAPI !== 'cli') {
            $limit = min(50 * 1048576, ini_parse_quantity(ini_get('upload_max_filesize')) ?: PHP_INT_MAX, ini_parse_quantity(ini_get('post_max_size')) ?: PHP_INT_MAX);
            $checks[] = ['Limite PHP inférieure aux 50 Mo de la bibliothèque', (int) ($limit < 50 * 1048576), 'Limite effective : '.round($limit / 1048576, 1).' Mo.'];
        }

        return $checks;
    }
}
