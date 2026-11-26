<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\Resource;
use Illuminate\Database\Seeder;

/** Liens officiels de la bibliothèque, sans fichiers ni statistiques de démonstration. */
class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        if (Resource::exists()) {
            return;
        }
        $ids = Formation::pluck('id', 'code');
        foreach (json_decode(file_get_contents(__DIR__.'/data/resources.json'), true) as $r) {
            if (empty($r['url']) || ! in_array(parse_url($r['url'], PHP_URL_SCHEME), ['http', 'https'], true)) {
                continue;
            }
            $res = new Resource([
                'formation_id' => $r['f'] === 'ALL' ? null : $ids[$r['f']], 'category' => $r['cat'], 'title' => $r['t'],
                'description' => $r['desc'], 'format' => $r['fmt'], 'size' => $r['size'] === '—' ? null : $r['size'],
                'licence' => $r['lic'], 'compatible' => $r['os'] ?? null, 'url' => $r['url'] ?? null,
                'essential' => (bool) ($r['key'] ?? false), 'downloads_count' => 0,
            ]);
            $res->save();
        }
    }
}
