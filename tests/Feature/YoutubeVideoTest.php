<?php

namespace Tests\Feature;

use App\Livewire\Gestion\WebTv;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class YoutubeVideoTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_formats_youtube_sont_extraits_sans_playlist(): void
    {
        foreach (['https://youtu.be/Un5XxJGRRYc?si=abc', 'https://www.youtube.com/watch?v=Un5XxJGRRYc&list=RDMMUn5XxJGRRYc',
            'https://m.youtube.com/watch?v=Un5XxJGRRYc', 'https://www.youtube.com/shorts/Un5XxJGRRYc',
            'https://www.youtube.com/live/Un5XxJGRRYc', 'https://www.youtube-nocookie.com/embed/Un5XxJGRRYc'] as $url) {
            $v = new Video(['source' => 'youtube', 'url' => $url]);
            $this->assertSame('Un5XxJGRRYc', $v->youtubeId());
            $this->assertSame('https://www.youtube.com/watch?v=Un5XxJGRRYc', $v->externalUrl());
        }
        foreach (['https://youtube.com/playlist?list=abc', 'https://youtube.com/@ecole', 'https://youtube.com/watch?v=Un5XxJGRRYc-extra',
            'https://youtube.com.evil.test/watch?v=Un5XxJGRRYc', 'https://example.test/?v=Un5XxJGRRYc'] as $url) {
            $this->assertNull((new Video(['source' => 'youtube', 'url' => $url]))->embedUrl());
        }
    }

    public function test_l_admin_verifie_le_vrai_titre_et_enregistre_un_lien_nettoye(): void
    {
        $this->seedBase();
        Http::preventStrayRequests();
        Http::fake(['www.youtube.com/oembed*' => Http::response(['title' => 'Le vrai titre YouTube', 'author_name' => 'Chaîne officielle'])]);
        $admin = $this->makeStaff('Super Administrateur');
        $page = Livewire::actingAs($admin)->test(WebTv::class)->call('creer')->set('v.title', 'Cours vidéo')->set('v.source', 'youtube')
            ->set('v.url', 'https://youtu.be/Un5XxJGRRYc?si=abc')->call('verifierYoutube')->assertHasNoErrors()
            ->assertSee('Le vrai titre YouTube')->assertSee('Chaîne officielle')->assertSeeHtml('youtube-nocookie.com/embed/Un5XxJGRRYc');
        $page->call('enregistrer')->assertHasNoErrors();
        $this->assertSame('https://www.youtube.com/watch?v=Un5XxJGRRYc', Video::firstOrFail()->url);
        $page->call('creer')->set('v.title', 'Invalide')->set('v.source', 'youtube')->set('v.url', 'https://youtube.com/playlist?list=abc')
            ->call('enregistrer')->assertHasErrors('v.url');
        $this->assertSame(1, Video::count());
    }

    public function test_un_lien_prive_ou_indisponible_affiche_un_message_clair(): void
    {
        $this->seedBase();
        Http::fake(['www.youtube.com/oembed*' => Http::response([], 404)]);
        Livewire::actingAs($this->makeStaff('Super Administrateur'))->test(WebTv::class)->call('creer')->set('v.source', 'youtube')
            ->set('v.url', 'https://youtu.be/Un5XxJGRRYc')->call('verifierYoutube')->assertHasErrors('v.url')->assertSet('apercuYoutube', []);
    }
}
