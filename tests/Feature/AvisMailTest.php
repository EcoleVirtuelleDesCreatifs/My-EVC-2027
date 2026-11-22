<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Project;
use App\Notifications\Avis;
use App\Services\PaymentService;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AvisMailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_mail_generique_francais_personnalise_et_echappe_sans_lien(): void
    {
        $s = $this->makeStudent();
        $s->user->name = '<script>Awa</script>';
        $avis = new Avis('Information', '<img src=x onerror=alert(1)>', mail: true);
        $mail = $avis->toMail($s->user);
        $html = (string) $mail->render();
        foreach (['#1E1B4B', 'Bonjour', e($s->user->name), e($avis->body), 'Tous droits réservés'] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
        foreach (['<script>', '<img src=x', "If you're having trouble", 'All rights reserved', 'Le bouton ne fonctionne pas ?'] as $bad) {
            $this->assertStringNotContainsString($bad, $html);
        }
        $this->assertStringContainsString($avis->body, view($mail->view['text'], $mail->viewData)->render());
        $this->assertSame(['database', 'mail'], $avis->via($s->user));
        $this->assertSame($avis->body, $avis->toArray($s->user)['body']);
    }

    public function test_confirmation_de_paiement_avec_recapitulatif_et_recu(): void
    {
        Notification::fake();
        $s = $this->makeStudent();
        $p = app(PaymentService::class)->record($s, 90000, 'Orange Money', 'OM-TEST-36', Carbon::parse('2026-11-22'));
        Notification::assertSentTo($s->user, Avis::class, function ($avis, $channels) use ($s, $p) {
            $mail = $avis->toMail($s->user);
            $html = (string) $mail->render();
            $text = view($mail->view['text'], $mail->viewData)->render();
            foreach (['90 000 FCFA', $p->receipt_number, 'Orange Money', 'OM-TEST-36', '22 novembre 2026', $s->matricule, 'Consulter mon reçu', route('etudiant.paiements')] as $expected) {
                $this->assertStringContainsString($expected, $html);
                $this->assertStringContainsString($expected, $text);
            }
            $this->assertStringContainsString('Le bouton ne fonctionne pas ?', $html);
            $this->assertStringNotContainsString('All rights reserved', $html);
            return $channels === ['database', 'mail'];
        });
    }

    public function test_paiement_sans_reference_facultative_et_sans_note_interne(): void
    {
        Notification::fake();
        $s = $this->makeStudent();
        app(PaymentService::class)->record($s, 10000, 'Espèces', note: 'Note administrative confidentielle');
        Notification::assertSentTo($s->user, Avis::class, function ($avis) use ($s) {
            $mail = $avis->toMail($s->user);
            $html = (string) $mail->render();
            $text = view($mail->view['text'], $mail->viewData)->render();
            foreach ([$html, $text] as $content) {
                $this->assertStringContainsString('10 000 FCFA', $content);
                $this->assertStringNotContainsString('Référence de transaction', $content);
                $this->assertStringNotContainsString('Note administrative confidentielle', $content);
            }
            $this->assertSame(['title', 'body', 'url', 'icon', 'tone'], array_keys($avis->toArray($s->user)));
            return true;
        });
    }

    public function test_attribution_de_projet_avec_formation_echeance_et_lien_prive(): void
    {
        Notification::fake();
        $s = $this->makeStudent(['CM']);
        $f = $s->formations->first();
        $p = Project::create(['formation_id' => $f->id, 'title' => 'CM-01 — Fiche marque <script>test</script>', 'type' => 'Débutant', 'brief' => 'Brief privé à consulter dans MY EVC', 'published' => true]);
        app(ProjectService::class)->assign($p, collect([$s]), Carbon::parse('2026-11-29'));
        $a = Assignment::firstOrFail();
        Notification::assertSentTo($s->user, Avis::class, function ($avis) use ($s, $p, $f, $a) {
            $mail = $avis->toMail($s->user);
            $html = (string) $mail->render();
            $text = view($mail->view['text'], $mail->viewData)->render();
            foreach ([$f->name, $s->matricule, 'Débutant', '29 novembre 2026', 'Consulter mon projet', route('etudiant.projet', $a)] as $expected) {
                $this->assertStringContainsString(e($expected), $html);
                $this->assertStringContainsString($expected, $text);
            }
            $this->assertStringContainsString(e($p->title), $html);
            $this->assertStringNotContainsString('<script>', $html);
            $this->assertStringNotContainsString($p->brief, $html);
            $this->assertStringNotContainsString("If you're having trouble", $html);
            $this->assertSame('Nouveau projet', $avis->toArray($s->user)['title']);
            return true;
        });
    }
}
