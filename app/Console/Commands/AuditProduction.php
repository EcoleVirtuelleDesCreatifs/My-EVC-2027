<?php

namespace App\Console\Commands;

use App\Support\ProductionAudit;
use Illuminate\Console\Command;

class AuditProduction extends Command
{
    protected $signature = 'evc:audit-production';

    protected $description = 'Contrôler en lecture seule les points bloquants avant publication officielle, sans afficher de secrets.';

    public function handle(): int
    {
        $checks = ProductionAudit::checks();
        $this->table(['Contrôle', 'Nombre', 'Action'], $checks);
        $this->line('Les limites d’envoi doivent être vérifiées sur le serveur HTTP ; les réglages du PHP CLI peuvent être différents.');
        if (collect($checks)->contains(fn ($check) => $check[1] > 0)) {
            $this->warn('Des validations restent nécessaires. Aucune donnée ni configuration modifiée.');

            return self::FAILURE;
        }
        $this->info('Contrôles automatiques réussis. La validation métier et juridique reste nécessaire.');

        return self::SUCCESS;
    }
}
