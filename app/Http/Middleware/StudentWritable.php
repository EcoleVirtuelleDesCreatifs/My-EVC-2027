<?php

namespace App\Http\Middleware;

use App\Support\LectureSeule;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Rubriques fermées quand la formation de l'étudiant est terminée (compte en lecture seule). */
class StudentWritable
{
    public function handle(Request $request, Closure $next): Response
    {
        if (LectureSeule::applies($request->user())) {
            if ($request->expectsJson()) {
                abort(403, LectureSeule::MESSAGE);
            }

            return redirect()->route('etudiant.accueil')->with('lecture_seule', LectureSeule::MESSAGE);
        }

        return $next($request);
    }
}
