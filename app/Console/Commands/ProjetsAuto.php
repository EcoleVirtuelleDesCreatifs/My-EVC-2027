<?php

namespace App\Console\Commands;

use App\Services\ProjectService;
use Illuminate\Console\Command;

class ProjetsAuto extends Command
{
    protected $signature = 'evc:projets-auto {--simulation : Afficher les envois prévus sans rien attribuer ni envoyer}';

    protected $description = 'Envoyer les projets du parcours automatique arrivés à échéance (attente après rendu, réglable dans l’administration).';

    public function handle(ProjectService $svc): int
    {
        $config = ProjectService::autoConfig();
        if (! $config['enabled']) {
            $this->info('Attribution automatique désactivée dans l’administration.');

            return self::SUCCESS;
        }
        $sent = 0;
        foreach ($svc->autoPlan() as $row) {
            $due = ! $row['at']->isFuture();
            $this->line($row['student']->matricule.' '.$row['student']->user->name.' → '.$row['project']->title.' · '.($due ? 'à envoyer' : 'prévu le '.$row['at']->format('d/m/Y H:i')));
            if ($due && ! $this->option('simulation') && $svc->assignNextAuto($row['student'], $row['formation']->id)) {
                $sent++;
            }
        }
        $this->info($this->option('simulation') ? 'Simulation : rien n’a été attribué ni envoyé.' : $sent.' projet(s) attribué(s) ; les étudiants concernés ont été prévenus par e-mail.');

        return self::SUCCESS;
    }
}
