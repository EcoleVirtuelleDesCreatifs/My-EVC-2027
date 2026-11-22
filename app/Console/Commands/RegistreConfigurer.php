<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class RegistreConfigurer extends Command
{
    protected $signature = 'registre:configurer {chemin : Chemin du projet evc-registre local}';

    protected $description = 'Initialise un nouveau jeton partagé local sans jamais l’afficher';

    public function handle(): int
    {
        $root = realpath($this->argument('chemin'));
        if (! app()->environment('local') || ! $root || ! is_file($root.'/.env') || ! is_file($root.'/artisan')) {
            $this->error('Cette commande nécessite deux projets Laravel locaux et un chemin de registre valide.');

            return self::FAILURE;
        }
        $paths = [base_path('.env'), $root.'/.env'];
        $originals = array_map(fn ($path) => File::get($path), $paths);
        $local = Dotenv::parse($originals[0]);
        $remote = Dotenv::parse($originals[1]);
        $url = $remote['APP_URL'] ?? '';
        if (($remote['APP_ENV'] ?? '') !== 'local' || ! in_array(parse_url($url, PHP_URL_HOST), ['localhost', '127.0.0.1', '[::1]'], true)
            || filled($local['EVC_REGISTRE_TOKEN'] ?? null) || filled($remote['EVC_API_TOKEN'] ?? null)) {
            $this->error('Configuration refusée : registre non local ou jeton déjà présent. Aucun secret existant ne sera remplacé.');

            return self::FAILURE;
        }
        $token = bin2hex(random_bytes(32));
        $settings = [['EVC_REGISTRE_URL' => $url, 'EVC_REGISTRE_TOKEN' => $token], ['EVC_API_TOKEN' => $token]];
        try {
            foreach ($paths as $i => $path) {
                $contents = $originals[$i];
                foreach ($settings[$i] as $key => $value) {
                    $line = $key.'="'.addcslashes($value, '\\"$').'"';
                    $contents = preg_match('/^'.preg_quote($key, '/').'=/m', $contents)
                        ? preg_replace_callback('/^'.preg_quote($key, '/').'=.*$/m', fn () => $line, $contents)
                        : rtrim($contents).PHP_EOL.$line.PHP_EOL;
                }
                if (File::put($path, $contents, true) === false) {
                    throw new \RuntimeException('Écriture impossible.');
                }
            }
            $this->callSilent('config:clear');
            (new Process([PHP_BINARY, $root.'/artisan', 'config:clear'], $root))->mustRun();
        } catch (\Throwable) {
            foreach ($paths as $i => $path) {
                File::put($path, $originals[$i], true);
            }
            $this->error('La configuration n’a pas pu être appliquée. Les fichiers ont été restaurés.');

            return self::FAILURE;
        }
        $this->info('Connexion locale configurée. Le nouveau jeton est enregistré dans les deux fichiers .env et n’a pas été affiché.');

        return self::SUCCESS;
    }
}
