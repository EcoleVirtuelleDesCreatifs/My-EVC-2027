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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Transforme une candidature acceptée en étudiant : compte, inscriptions, échéancier, e-mail d'activation. */
class AdmissionService
{
    public function activate(Candidature $c, Promotion $promotion, string $mode = 'En ligne'): Student
    {
        [$student, $activated] = DB::transaction(function () use ($c, $promotion, $mode) {
            $c = Candidature::lockForUpdate()->findOrFail($c->id);
            if ($c->stage === 'activee' && $c->student) {
                app(AdmissionPaymentService::class)->sync($c, $c->student);

                return [$c->student, false];
            }
            if ($c->stage === 'paiement' && ! $c->first_payment) {
                throw \Illuminate\Validation\ValidationException::withMessages(['premierVersement' => 'Précisez le montant, la date et le moyen du premier versement reçu avant d’activer le compte.']);
            }
            $user = User::firstOrCreate(['email' => strtolower($c->email)], [
                'name' => $c->full_name, 'phone' => $c->phone, 'password' => Str::random(40),
            ]);
            $user->syncRoles(['etudiant']);

            $student = $user->student ?: Student::create([
                'user_id' => $user->id, 'matricule' => Student::nextMatricule(), 'promotion_id' => $promotion->id,
                'country' => $c->country, 'city' => $c->city, 'gender' => $c->answers['Genre'][0] ?? null,
            ]);

            /* Le solde est dû N jours après le début de la formation pour l'étudiant (session, ou inscription si plus tardive) */
            foreach (Formation::whereIn('code', $c->formations)->get() as $f) {
                app(EnrollmentService::class)->enroll($student, $f, $promotion, $mode);
            }

            $c->update(['stage' => 'activee', 'student_id' => $student->id]);
            app(AdmissionPaymentService::class)->sync($c, $student);
            AuditLog::record('Candidature activée', $c->reference.' → '.$student->matricule);

            return [$student, true];
        });
        $c->refresh();
        if (! $activated) {
            return $student->fresh();
        }

        self::importPhoto($c, $student->user);
        $student->user->notify(new CompteActive(Password::broker('activations')->createToken($student->user), $student->matricule));
        $fresh = $student->fresh(['formations', 'user']);
        foreach ($fresh->formations as $f) {
            app(ProjectService::class)->assignNextAuto($fresh, $f->id);
        }

        return $student;
    }

    /**
     * Photo jointe à la préinscription → photo de profil MY EVC, si le compte n'en a pas déjà une.
     * L'image est redimensionnée (800 px max) et enregistrée en JPEG dans le stockage public des avatars.
     */
    public static function importPhoto(Candidature $c, User $user): bool
    {
        $file = $c->files()->where('label', 'Photo')->latest('id')->first();
        $local = Storage::disk('local');
        if ($user->avatar_path || ! $file || ! $local->exists($file->path)) {
            return false;
        }
        $source = $local->path($file->path);
        [$w, $h, $type] = @getimagesize($source) ?: [0, 0, 0];
        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source), IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => @imagecreatefromwebp($source), default => false,
        };
        if (! $image || ! $w || ! $h) {
            return false;
        }
        $ratio = min(1, 800 / max($w, $h));
        [$nw, $nh] = [(int) round($w * $ratio), (int) round($h * $ratio)];
        $canvas = imagecreatetruecolor($nw, $nh);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $nw, $nh, $w, $h);
        ob_start();
        imagejpeg($canvas, null, 85);
        $path = 'avatars/'.Str::random(40).'.jpg';
        Storage::disk('public')->put($path, (string) ob_get_clean());
        imagedestroy($image);
        imagedestroy($canvas);
        $user->update(['avatar_path' => $path]);
        AuditLog::record('Photo de préinscription reprise comme photo de profil', $c->reference.' → '.$user->email);

        return true;
    }
}
