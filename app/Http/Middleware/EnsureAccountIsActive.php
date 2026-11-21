<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Un compte suspendu est déconnecté avec un message. */
class EnsureAccountIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->status === 'suspendu') {
            Auth::logout();
            $request->session()->invalidate();

            return redirect()->route('login')->withErrors(['login' => 'Votre compte est suspendu. Contactez la relation étudiants sur WhatsApp.']);
        }

        return $next($request);
    }
}
