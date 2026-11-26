<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

/** Contenus de référence pour les tests et les essais locaux explicites. */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('Les contenus de test sont interdits en production. Utilisez db:seed sans classe.');
        }
        $this->call(FormationSeeder::class);

        if (! Promotion::exists()) {
            $start = now()->startOfMonth()->subMonth()->addDays(4);
            Promotion::create(['name' => 'Session '.ucfirst($start->translatedFormat('F Y')), 'starts_on' => $start, 'ends_on' => $start->copy()->addMonths(4), 'status' => 'en_cours']);
            $next = $start->copy()->addMonths(3);
            Promotion::create(['name' => 'Session '.ucfirst($next->translatedFormat('F Y')), 'starts_on' => $next, 'ends_on' => $next->copy()->addMonths(4), 'status' => 'ouverte']);
        }

        foreach (json_decode(file_get_contents(__DIR__.'/data/news.json'), true) as $n) {
            News::updateOrCreate(['slug' => $n['slug']], [
                'title' => $n['title'], 'category' => $n['category'], 'label' => $n['label'], 'excerpt' => $n['excerpt'],
                'cover' => $n['cover'], 'body' => $n['body'], 'featured' => $n['featured'],
                'published_at' => now()->subDays(2 + $n['position'] * 11),
            ]);
        }
    }
}
