<?php

namespace Tests\Feature;

use App\Livewire\Admin\Actualites;
use App\Models\News;
use App\Models\NewsletterSubscriber;
use App\Notifications\ActualiteNewsletter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterActualiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Storage::fake('public');
        $this->actingAs($this->makeStaff('Super Administrateur'));
    }

    private function publier(bool $newsletter): News
    {
        Livewire::test(Actualites::class)->call('creer')->assertSet('n.newsletter', true)
            ->set('n.title', 'La newsletter est partie')->set('n.excerpt', 'Résumé de l’actualité.')
            ->set('n.body', '<p>Texte de l’actualité.</p>')
            ->set('n.newsletter', $newsletter)
            ->set('couverture', UploadedFile::fake()->image('couverture.jpg'))
            ->call('enregistrer')->assertHasNoErrors();

        return News::where('slug', 'la-newsletter-est-partie')->firstOrFail();
    }

    public function test_la_publication_notifie_les_abonnes_sauf_desabonnes(): void
    {
        Notification::fake();
        NewsletterSubscriber::create(['email' => 'un@exemple.ci']);
        NewsletterSubscriber::create(['email' => 'deux@exemple.ci']);
        NewsletterSubscriber::create(['email' => 'parti@exemple.ci', 'unsubscribed_at' => now()]);

        $news = $this->publier(true);
        $this->assertNotNull($news->newsletter_sent_at);
        Notification::assertSentOnDemandTimes(ActualiteNewsletter::class, 2);
        Notification::assertSentOnDemand(ActualiteNewsletter::class, function ($n, $channels, $notifiable) {
            return ($notifiable->routes['mail'] ?? null) === 'un@exemple.ci'
                && str_contains($n->toMail($notifiable)->viewData['desinscriptionUrl'], 'jeton=');
        });
    }

    public function test_pas_d_envoi_sans_case_cochee_ni_en_brouillon(): void
    {
        Notification::fake();
        NewsletterSubscriber::create(['email' => 'un@exemple.ci']);
        $news = $this->publier(false);
        $this->assertNull($news->newsletter_sent_at);

        Livewire::test(Actualites::class)->call('creer')
            ->set('n.title', 'Brouillon')->set('n.excerpt', 'Résumé')->set('n.body', '<p>Texte</p>')
            ->set('n.publie', false)->set('n.newsletter', true)
            ->set('couverture', UploadedFile::fake()->image('c.jpg'))->call('enregistrer')->assertHasNoErrors();
        Notification::assertNothingSent();
    }

    public function test_desinscription_puis_reabonnement(): void
    {
        $abonne = NewsletterSubscriber::create(['email' => 'lecteur@exemple.ci']);
        $url = $abonne->desinscriptionUrl();
        $this->get($url)->assertOk()->assertSee('désabonné');
        $this->assertNotNull($abonne->fresh()->unsubscribed_at);
        $this->get(route('newsletter.desinscription', ['email' => $abonne->email, 'jeton' => 'mauvais']))->assertOk()->assertSee('Lien invalide');

        /* Réabonnement depuis le formulaire du site : l'adresse redevient active. */
        $this->postJson(route('newsletter.store'), ['email' => 'lecteur@exemple.ci'])->assertOk();
        $this->assertNull($abonne->fresh()->unsubscribed_at);
    }
}
