<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Candidature;
use App\Models\Formation;
use App\Models\Promotion;
use App\Models\Student;
use App\Models\User;
use App\Notifications\CompteActive;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/** Transforme une candidature acceptée en étudiant : compte, inscriptions, échéancier, e-mail d'activation. */
class AdmissionService
{
    public function activate(Candidature $c, Promotion $promotion, string $mode = 'En ligne'): Student
    {
        $student = DB::transaction(function () use ($c, $promotion, $mode) {
            $user = User::firstOrCreate(['email' => strtolower($c->email)], [
                'name' => $c->full_name, 'phone' => $c->phone, 'password' => Str::random(40),
            ]);
            $user->syncRoles(['etudiant']);

            $student = $user->student ?: Student::create([
                'user_id' => $user->id, 'matricule' => Student::nextMatricule(), 'promotion_id' => $promotion->id,
                'country' => $c->country, 'city' => $c->city, 'gender' => $c->answers['Genre'][0] ?? null,
            ]);

            /* Le solde est dû N jours après le début de la formation pour l'étudiant (session, ou inscription si plus tardive) */
            $debut = $promotion->starts_on->max(now()->startOfDay());
            foreach (Formation::whereIn('code', $c->formations)->get() as $f) {
                if ($student->enrollments()->where('formation_id', $f->id)->exists()) {
                    continue;
                }
                $student->enrollments()->create(['formation_id' => $f->id, 'promotion_id' => $promotion->id, 'mode' => $mode]);
                $student->schedules()->create(['formation_id' => $f->id, 'label' => 'Acompte '.$f->code, 'amount' => $f->deposit, 'due_on' => now()->toDateString()]);
                $student->schedules()->create(['formation_id' => $f->id, 'label' => 'Solde '.$f->code, 'amount' => $f->balance(),
                    'due_on' => $debut->copy()->addDays($f->balance_after_days)->toDateString()]);
            }

            $c->update(['stage' => 'activee', 'student_id' => $student->id]);
            AuditLog::record('Candidature activée', $c->reference.' → '.$student->matricule);

            return $student;
        });

        $student->user->notify(new CompteActive(Password::broker('activations')->createToken($student->user), $student->matricule));

        return $student;
    }
}
