<?php

namespace App\Console\Commands;

use App\Services\RegistreService;
use Illuminate\Console\Command;

class RegistreSync extends Command
{
    protected $signature = 'registre:sync {--watch : Actualiser les seuls dossiers du registre toutes les 60 secondes}';

    protected $description = 'Synchronise les certificats publiés ou révoqués du registre officiel';

    public function handle(RegistreService $registre): int
    {
        do {
            $counts = $registre->synchroniserTout();
            $this->info($counts['ok'].' dossier(s) synchronisé(s), '.$counts['echecs'].' échec(s).');
            if (! $this->option('watch')) {
                return $counts['echecs'] ? self::FAILURE : self::SUCCESS;
            }
            sleep(60);
        } while (true);
    }
}
