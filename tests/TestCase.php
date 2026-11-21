<?php

namespace Tests;

use App\Models\Formation;
use App\Models\Promotion;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\ContentSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedBase(): void
    {
        $this->seed([RolesSeeder::class, ContentSeeder::class]);
    }

    protected function makeStudent(array $codes = ['DG'], array $attrs = []): Student
    {
        $promo = Promotion::where('status', 'en_cours')->first();
        $user = User::factory()->create(['password' => 'password']);
        $user->assignRole('etudiant');
        $s = Student::create(['user_id' => $user->id, 'matricule' => Student::nextMatricule(), 'promotion_id' => $promo->id] + $attrs);
        foreach (Formation::whereIn('code', $codes)->get() as $f) {
            $s->enrollments()->create(['formation_id' => $f->id, 'promotion_id' => $promo->id]);
            $s->schedules()->create(['formation_id' => $f->id, 'label' => 'Acompte '.$f->code, 'amount' => $f->deposit, 'due_on' => now()->subDays(10)]);
            $s->schedules()->create(['formation_id' => $f->id, 'label' => 'Solde '.$f->code, 'amount' => $f->balance(), 'due_on' => now()->addDays(20)]);
        }

        return $s->fresh(['user', 'formations']);
    }

    protected function makeStaff(string $role, ?string $formationCode = null): User
    {
        $u = User::factory()->create(['password' => 'password']);
        $u->assignRole($role);
        if ($formationCode) {
            $u->trainerProfiles()->create(['formation_id' => Formation::where('code', $formationCode)->value('id')]);
        }

        return $u;
    }
}
