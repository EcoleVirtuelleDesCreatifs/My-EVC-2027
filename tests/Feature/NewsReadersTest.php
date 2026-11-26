<?php

namespace Tests\Feature;

use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsReadersTest extends TestCase
{
    use RefreshDatabase;

    private const BROWSER = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15';

    public function test_les_lecteurs_sont_comptes_une_fois_par_session_hors_robots_et_equipe(): void
    {
        $this->seedBase();
        $n = News::published()->firstOrFail();
        $modified = $n->updated_at;
        $url = route('site.actualite', $n);

        $this->withHeader('User-Agent', self::BROWSER)->get($url)->assertOk();
        $this->withHeader('User-Agent', self::BROWSER)->get($url)->assertOk();
        $this->assertSame(1, $n->fresh()->views, 'Une seule lecture par session.');
        $this->assertTrue($modified->equalTo($n->fresh()->updated_at), 'La date de modification (SEO) ne change pas.');

        $this->flushSession();
        $this->withHeader('User-Agent', self::BROWSER)->get($url)->assertOk();
        $this->flushSession();
        $this->withHeader('User-Agent', 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)')->get($url)->assertOk();
        $this->flushSession();
        $this->withHeader('User-Agent', 'WhatsApp/2.23.20.0')->get($url)->assertOk();
        $admin = $this->makeStaff('Super Administrateur');
        $this->flushSession();
        $this->actingAs($admin)->withHeader('User-Agent', self::BROWSER)->get($url)->assertOk();
        $this->assertSame(2, $n->fresh()->views);

        $this->actingAs($admin)->get(route('admin.actualites'))->assertOk()->assertSee('Lecteurs')->assertSee('La plus lue')
            ->assertSee('2</b> lecteurs', false);
    }
}
