<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $short = ['DG' => 'Design graphique', 'CM' => 'Community management', 'GIA' => 'Bureautique'];
        $balanceDays = ['DG' => 60, 'CM' => 30, 'GIA' => 30];
        $colors = ['DG' => '#C2255C', 'CM' => '#2F6BD8', 'GIA' => '#1F8F5A'];
        foreach (json_decode(file_get_contents(__DIR__.'/data/formations.json'), true) as $i => $f) {
            Formation::firstOrCreate(['code' => $f['code']], [
                'name' => $f['name'], 'short_name' => $short[$f['code']], 'pitch' => $f['pitch'], 'price' => $f['price'],
                'deposit' => $f['deposit'], 'months' => $f['months'], 'pay_rule' => $f['pay_rule'], 'modules' => $f['modules'],
                'balance_after_days' => $balanceDays[$f['code']], 'color' => $colors[$f['code']], 'position' => $i,
                'has_reco' => in_array($f['code'], ['CM', 'GIA'], true),
            ]);
        }
    }
}
