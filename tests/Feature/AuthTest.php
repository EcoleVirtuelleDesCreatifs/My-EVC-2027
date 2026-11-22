<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_email_certificat_professionnel_personnalise_et_en_francais(): void
    {
        $s = $this->makeStudent(['DG']);
        $s->user->update(['name' => '<script>Awa</script> Kouassi']);
        $f = $s->formations->first();
        $c = \App\Models\Certificate::create(['student_id' => $s->id, 'formation_id' => $f->id,
            'code' => 'EVC-CERT-2026-0453', 'issued_on' => '2026-11-22', 'appreciation' => 'Très bien']);
        $notification = new \App\Notifications\CertificatDelivre($c);
        $this->assertSame(['database', 'mail'], $notification->via($s->user));
        $this->assertSame('Certificat délivré', $notification->toArray($s->user)['title']);
        $this->assertSame(route('etudiant.certificat'), $notification->toArray($s->user)['url']);
        $mail = $notification->toMail($s->user);
        $html = (string) $mail->render();
        foreach (['Votre certificat est disponible', $c->code, $s->matricule, e($f->name), 'Très bien', '22 novembre 2026',
            'Consulter mon certificat', route('etudiant.certificat'), 'Le bouton ne fonctionne pas ?', e($s->user->name)] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString("If you're having trouble", $html);
        $this->assertStringNotContainsString('All rights reserved', $html);
        $text = view($mail->view['text'], $mail->viewData)->render();
        foreach ([$c->code, $s->matricule, $f->name, 'Très bien', route('etudiant.certificat')] as $expected) {
            $this->assertStringContainsString($expected, $text);
        }
    }

    public function test_email_activation_professionnel_en_francais(): void
    {
        $s = $this->makeStudent();
        $s->user->update(['name' => '<script>Awa</script> Kouassi']);
        $token = \Illuminate\Support\Facades\Password::broker('activations')->createToken($s->user);
        $mail = (new \App\Notifications\CompteActive($token, $s->matricule))->toMail($s->user);
        $url = route('password.reset', ['token' => $token, 'email' => $s->user->email, 'activation' => 1]);
        $html = (string) $mail->render();
        $this->assertStringContainsString('Votre espace étudiant vous attend', $html);
        $this->assertStringContainsString($s->matricule, $html);
        $this->assertStringContainsString('Activer mon compte', $html);
        $this->assertStringContainsString('7 jours', $html);
        $this->assertStringContainsString(e($url), $html);
        $this->assertStringContainsString('&lt;script&gt;Awa&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString("If you're having trouble", $html);
        $this->assertStringNotContainsString('All rights reserved', $html);
        $this->assertSame('auth.mail-activation-texte', $mail->view['text']);
        $text = view($mail->view['text'], $mail->viewData)->render();
        $this->assertStringContainsString($url, $text);
        $this->assertStringContainsString($s->matricule, $text);
        $this->get($url)->assertOk();
    }

    public function test_activation_et_reinitialisation_permettent_d_afficher_les_mots_de_passe(): void
    {
        foreach ([0, 1] as $activation) {
            $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'awa@example.test', 'activation' => $activation]))
                ->assertOk()
                ->assertSee('type="password" id="password"', false)
                ->assertSee('type="password" id="password_confirmation"', false)
                ->assertSee('data-password-toggle="password"', false)
                ->assertSee('data-password-toggle="password_confirmation"', false)
                ->assertSee('aria-label="Afficher le mot de passe"', false)
                ->assertSee('aria-label="Afficher la confirmation du mot de passe"', false);
        }
    }

    public function test_profil_affiche_un_avatar_cadre_et_les_informations_etudiant(): void
    {
        $s = $this->makeStudent(['DG', 'CM']);
        $s->user->update(['avatar_path' => 'avatars/portrait.jpg']);
        $this->actingAs($s->user)->get(route('myevc.profil'))->assertOk()
            ->assertSee('profile-layout')->assertSee('profile-avatar')->assertSee('width="96" height="96"', false)
            ->assertSee($s->matricule)->assertSee('Mon parcours à l’EVC')->assertSee('Sécurité du compte')
            ->assertSee('autocomplete="tel"', false)->assertSee('aria-controls="actuel"', false);
    }

    public function test_profil_sauvegarde_la_photo_et_le_telephone(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $s = $this->makeStudent();
        \Livewire\Livewire::actingAs($s->user)->test(\App\Livewire\Shared\Profil::class)
            ->set('phone', '+2250700000000')->set('photo', \Illuminate\Http\UploadedFile::fake()->image('portrait.jpg', 900, 1600))
            ->call('enregistrer')->assertHasNoErrors()->assertSet('photo', null);
        $user = $s->user->fresh();
        $this->assertSame('+2250700000000', $user->phone);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->avatar_path);
        $this->actingAs($user)->get(route('myevc.profil'))->assertOk()->assertSee('profile-avatar');
    }

    public function test_profil_equipe_sans_dossier_etudiant(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin)->get(route('myevc.profil'))->assertOk()->assertSee('profile-avatar')
            ->assertSee('Mes coordonnées')->assertSee('Sécurité du compte')->assertDontSee('Mon parcours à l’EVC');
    }

    public function test_connexion_par_matricule_ou_par_email(): void
    {
        $s = $this->makeStudent();
        $this->post('/myevc/connexion', ['login' => strtolower($s->matricule), 'password' => 'password'])->assertRedirect(route('etudiant.accueil'));
        $this->post('/myevc/deconnexion');
        $this->post('/myevc/connexion', ['login' => $s->user->email, 'password' => 'password'])->assertRedirect(route('etudiant.accueil'));
    }

    public function test_mauvais_mot_de_passe_et_limitation(): void
    {
        $s = $this->makeStudent();
        foreach (range(1, 5) as $i) {
            $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'faux'])->assertSessionHasErrors('login');
        }
        $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_compte_suspendu_refuse(): void
    {
        $s = $this->makeStudent();
        $s->user->update(['status' => 'suspendu']);
        $this->post('/myevc/connexion', ['login' => $s->matricule, 'password' => 'password'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_chaque_espace_est_protege(): void
    {
        $this->get('/myevc/etudiant')->assertRedirect(route('login'));
        $s = $this->makeStudent();
        $this->actingAs($s->user)->get('/myevc/admin')->assertForbidden();
        $this->actingAs($s->user)->get('/myevc/formateur')->assertForbidden();

        $trainer = $this->makeStaff('formateur', 'DG');
        $this->actingAs($trainer)->get('/myevc/formateur')->assertOk();
        $this->actingAs($trainer)->get('/myevc/etudiant')->assertForbidden();

        $finance = $this->makeStaff('Responsable financier');
        $this->actingAs($finance)->get('/myevc/admin/finances')->assertOk();
        $this->actingAs($finance)->get('/myevc/admin/utilisateurs')->assertForbidden();
        $this->actingAs($finance)->get('/myevc/admin/candidatures')->assertForbidden();

        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin)->get('/myevc/admin/utilisateurs')->assertOk();
    }

    public function test_tous_les_ecrans_s_affichent_pour_chaque_role(): void
    {
        $s = $this->makeStudent(['DG', 'CM']);
        foreach (['etudiant.accueil', 'etudiant.cours', 'etudiant.projets', 'etudiant.bibliotheque', 'etudiant.paiements', 'etudiant.certificat', 'myevc.notifications', 'myevc.profil'] as $r) {
            $this->actingAs($s->user)->get(route($r))->assertOk();
        }
        $t = $this->makeStaff('formateur', 'DG');
        foreach (['formateur.accueil', 'formateur.seances', 'formateur.corrections', 'formateur.bibliotheque'] as $r) {
            $this->actingAs($t)->get(route($r))->assertOk();
        }
        $a = $this->makeStaff('Super Administrateur');
        foreach (['dashboard', 'candidatures', 'etudiants', 'formations', 'seances', 'projets', 'disponibilite', 'finances', 'bibliotheque', 'actualites', 'messages', 'certificats', 'utilisateurs', 'audit', 'parametres'] as $r) {
            $this->actingAs($a)->get(route('admin.'.$r))->assertOk();
        }
        $this->actingAs($a)->get(route('admin.etudiant', $s))->assertOk()->assertSee($s->matricule);
    }
}
