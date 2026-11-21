<?php

namespace App\Services;

use App\Models\Presence;
use App\Models\Seance;
use App\Models\Student;
use Illuminate\Validation\ValidationException;

class PresenceService
{
    /** Délai de tolérance : au-delà, la présence est notée « en retard ». */
    public const TOLERANCE_MIN = 10;

    /** Pointage de l'étudiant (ouvert 30 min avant le début jusqu'à la fin de la séance). */
    public function checkIn(Student $student, Seance $seance): Presence
    {
        $inscrit = $student->formations->contains('id', $seance->formation_id)
            && (! $seance->promotion_id || $seance->promotion_id === $student->promotion_id);
        if (! $inscrit || ! $seance->checkInOpen() || $seance->cancelled) {
            throw ValidationException::withMessages(['seance' => 'Le pointage n\'est pas ouvert pour cette séance.']);
        }

        return Presence::firstOrCreate(['seance_id' => $seance->id, 'student_id' => $student->id], [
            'status' => now()->gt($seance->starts_at->copy()->addMinutes(self::TOLERANCE_MIN)) ? 'retard' : 'present',
            'checked_at' => now(),
        ]);
    }
}
