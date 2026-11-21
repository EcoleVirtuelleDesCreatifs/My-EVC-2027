<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\News;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

/** Contenus réels du site : formations, promotions, actualités. */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $short = ['DG' => 'Design graphique', 'CM' => 'Community management', 'GIA' => 'Bureautique'];
        $balanceDays = ['DG' => 60, 'CM' => 30, 'GIA' => 30];
        $colors = ['DG' => '#C2255C', 'CM' => '#2F6BD8', 'GIA' => '#1F8F5A'];
        foreach (json_decode(file_get_contents(__DIR__.'/data/formations.json'), true) as $i => $f) {
            Formation::updateOrCreate(['code' => $f['code']], [
                'name' => $f['name'], 'short_name' => $short[$f['code']], 'pitch' => $f['pitch'], 'price' => $f['price'],
                'deposit' => $f['deposit'], 'months' => $f['months'], 'pay_rule' => $f['pay_rule'], 'modules' => $f['modules'],
                'balance_after_days' => $balanceDays[$f['code']], 'color' => $colors[$f['code']], 'position' => $i,
            ]);
        }

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
