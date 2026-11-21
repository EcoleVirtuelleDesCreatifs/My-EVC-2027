<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Resource;
use App\Support\DemoFile;
use Illuminate\Database\Seeder;

/** Catalogue initial de la bibliothèque (36 ressources réparties en 7 rayons). */
class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        if (Resource::exists()) {
            return;
        }
        $ids = Formation::pluck('id', 'code');
        foreach (json_decode(file_get_contents(__DIR__.'/data/resources.json'), true) as $r) {
            $res = new Resource([
                'formation_id' => $r['f'] === 'ALL' ? null : $ids[$r['f']], 'category' => $r['cat'], 'title' => $r['t'],
                'description' => $r['desc'], 'format' => $r['fmt'], 'size' => $r['size'] === '—' ? null : $r['size'],
                'licence' => $r['lic'], 'compatible' => $r['os'] ?? null, 'url' => $r['url'] ?? null,
                'essential' => (bool) ($r['key'] ?? false), 'downloads_count' => $r['dl'],
            ]);
            if (! $res->url) {
                $res->path = DemoFile::make($r['t'], $r['fmt']);
            }
            $res->created_at = $res->updated_at = now()->addDays($r['d']);
            $res->save();
        }
    }
}
