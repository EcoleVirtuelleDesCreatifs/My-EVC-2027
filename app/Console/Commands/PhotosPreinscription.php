<?php

namespace App\Console\Commands;

use App\Models\Candidature;
use App\Services\AdmissionService;
use Illuminate\Console\Command;

class PhotosPreinscription extends Command
{
    protected $signature = 'evc:photos-preinscription';

    protected $description = 'Reprendre la photo de préinscription comme photo de profil des étudiants déjà activés qui n’en ont pas.';

    public function handle(): int
    {
        $done = 0;
        Candidature::where('stage', 'activee')->whereNotNull('student_id')->with('student.user')->each(function (Candidature $c) use (&$done) {
            if ($c->student?->user && AdmissionService::importPhoto($c, $c->student->user)) {
                $done++;
                $this->line($c->reference.' → '.$c->student->user->name);
            }
        });
        $this->info($done.' photo(s) de profil reprise(s). Les photos déjà choisies par les étudiants sont conservées.');

        return self::SUCCESS;
    }
}
