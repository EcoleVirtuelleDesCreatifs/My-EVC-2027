<?php

namespace Tests\Feature;

use App\Notifications\ReinitialisationMotDePasse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Notification::fake();
    }

    public function test_demande_envoie_un_mail_personnalise_francais_et_sans_connexion_prealable(): void
    {
        $s = $this->makeStudent();
        $s->user->update(['name' => '<script>Awa</script> Kouassi', 'email' => 'awa+evc@example.test']);
        $this->post(route('password.email'), ['email' => $s->user->email])->assertSessionHas('status');
        Notification::assertSentTo($s->user, ReinitialisationMotDePasse::class, function ($notification, $channels) use ($s) {
            $mail = $notification->toMail($s->user);
            $html = (string) $mail->render();
            $text = view($mail->view['text'], $mail->viewData)->render();
            $url = route('password.reset', ['token' => $notification->token, 'email' => $s->user->email]);
            foreach (['Réinitialiser mon mot de passe', '60 minutes', 'sans connexion préalable', 'Ne partagez jamais ce lien', 'Tous droits réservés'] as $expected) {
                $this->assertStringContainsString($expected, $html);
                $this->assertStringContainsString($expected, $text);
            }
            $this->assertStringContainsString('#1E1B4B', $html);
            $this->assertStringContainsString(e($s->user->name), $html);
            $this->assertStringContainsString(e($url), $html);
            $this->assertStringContainsString($url, $text);
            foreach (['<script>', 'Hello!', 'Reset Password', 'All rights reserved', 'Une connexion à votre compte MY EVC est nécessaire.'] as $bad) {
                $this->assertStringNotContainsString($bad, $html);
            }
            $this->get($url)->assertOk();
            return $channels === ['mail'];
        });
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_duree_affichee_suit_la_configuration_et_le_jeton_expire(): void
    {
        $this->freezeTime();
        config(['auth.passwords.users.expire' => 2]);
        $s = $this->makeStudent();
        $token = Password::createToken($s->user);
        $mail = (new ReinitialisationMotDePasse($token))->toMail($s->user);
        $this->assertStringContainsString('2 minutes', (string) $mail->render());
        $this->assertTrue(Password::tokenExists($s->user, $token));
        $this->travel(3)->minutes();
        $this->assertFalse(Password::tokenExists($s->user, $token));
        $this->post(route('password.update'), ['email' => $s->user->email, 'token' => $token, 'password' => 'NouveauPass123!', 'password_confirmation' => 'NouveauPass123!'])->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('password', $s->user->fresh()->password));
    }

    public function test_lien_valide_reinitialise_le_mot_de_passe_et_ne_peut_pas_etre_reutilise(): void
    {
        $s = $this->makeStudent();
        $token = Password::createToken($s->user);
        $data = ['email' => $s->user->email, 'token' => $token, 'password' => 'NouveauPass123!', 'password_confirmation' => 'NouveauPass123!'];
        $this->post(route('password.update'), $data)->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NouveauPass123!', $s->user->fresh()->password));
        $this->assertFalse(Password::tokenExists($s->user, $token));
        $this->post(route('password.update'), $data)->assertSessionHasErrors('email');
    }

    public function test_adresse_inconnue_ne_divulgue_pas_existence_du_compte(): void
    {
        $this->post(route('password.email'), ['email' => 'inconnu@example.test'])->assertSessionHas('status', 'Si un compte existe pour cette adresse, un lien de réinitialisation vient d\'être envoyé.');
        Notification::assertNothingSent();
    }
}
