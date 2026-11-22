<?php

use Illuminate\Support\Facades\Schedule;

/* Tâches planifiées : sur le serveur, une seule ligne cron suffit :
   * * * * * cd /chemin/vers/my-evc && php artisan schedule:run >> /dev/null 2>&1 */
Schedule::command('evc:relances')->dailyAt('08:30')->timezone('Africa/Abidjan')->withoutOverlapping();
Schedule::command('queue:prune-failed')->weekly();
Schedule::command('registre:sync')->everyMinute()->withoutOverlapping();
