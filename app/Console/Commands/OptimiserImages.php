<?php

namespace App\Console\Commands;

use App\Support\ResponsiveImages;
use Illuminate\Console\Command;

class OptimiserImages extends Command
{
    protected $signature = 'evc:images {--force : Régénérer toutes les variantes} {--qualite=78 : Qualité WebP (1-100)}';

    protected $description = 'Générer les variantes WebP (480, 960, 1440 px) des images du site public et leur manifeste de dimensions.';

    public function handle(): int
    {
        $quality = max(1, min(100, (int) $this->option('qualite')));
        $stats = ResponsiveImages::build($quality, (bool) $this->option('force'));
        $this->info($stats['images'].' images traitées, '.$stats['generated'].' variantes générées.');
        $this->line('Poids des originaux : '.round($stats['before'] / 1024).' Ko → plus grandes variantes WebP : '.round($stats['after'] / 1024).' Ko.');

        return self::SUCCESS;
    }
}
