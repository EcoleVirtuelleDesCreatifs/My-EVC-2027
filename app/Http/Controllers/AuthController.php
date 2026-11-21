<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    /** Connexion par e-mail ou par matricule (EVC-26-0142). */
    public function login(Request $request)
    {
        $data = $request->validate(['login' => 'required|string', 'password' => 'required|string'], [], ['login' => 'e-mail ou matricule', 'password' => 'mot de passe']);
        $login = trim($data['login']);
        $key = 'login:'.Str::lower($login).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['login' => 'Trop de tentatives. Réessayez dans '.RateLimiter::availableIn($key).' secondes.'])->onlyInput('login');
        }
        $email = str_contains($login, '@') ? strtolower($login) : Student::where('matricule', strtoupper($login))->first()?->user?->email;

        if (! $email || ! Auth::attempt(['email' => $email, 'password' => $data['password']], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['login' => 'Identifiants incorrects. Vérifiez votre e-mail (ou matricule) et votre mot de passe.'])->onlyInput('login');
        }
        RateLimiter::clear($key);
        $user = $request->user();
        if ($user->status === 'suspendu') {
            Auth::logout();

            return back()->withErrors(['login' => 'Votre compte est suspendu. Contactez la relation étudiants sur WhatsApp.'])->onlyInput('login');
        }
        $request->session()->regenerate();
        $user->forceFill(['last_login_at' => now()])->save();
        if ($user->space() === 'admin') {
            AuditLog::record('Connexion', $user->getRoleNames()->first());
        }

        return redirect()->intended(route($user->homeRoute()));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Vous êtes déconnecté. À bientôt !');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink(['email' => strtolower($request->email)]);

        /* Même réponse que le compte existe ou non (pas de divulgation des adresses) */
        return back()->with('status', 'Si un compte existe pour cette adresse, un lien de réinitialisation vient d\'être envoyé.');
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset', ['token' => $token, 'email' => $request->query('email'), 'activation' => $request->boolean('activation')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required', 'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ], [], ['password' => 'mot de passe']);

        $status = Password::broker($request->boolean('activation') ? 'activations' : null)->reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'email_verified_at' => $user->email_verified_at ?? now()])->setRememberToken(str()->random(60));
            $user->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe enregistré. Vous pouvez vous connecter.')
            : back()->withErrors(['email' => 'Ce lien n\'est plus valable. Demandez-en un nouveau.']);
    }
}
