<?php

namespace Tests\Feature;

use App\Livewire\Admin\Actualites;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ActualitesEditorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Storage::fake('public');
        $this->actingAs($this->makeStaff('Super Administrateur'));
    }

    public function test_editeur_enregistre_la_mise_en_forme_et_filtre_le_html_dangereux(): void
    {
        Livewire::test(Actualites::class)->call('creer')->assertSee('Mise en forme du texte')
            ->set('n.title', 'Nouvelle actualité éditoriale')->set('n.excerpt', 'Le chapeau de notre actualité.')
            ->set('n.body', '<h2>Nos résultats</h2><p><strong>Bravo</strong> à tous.</p><ul><li>Un projet</li></ul><a href="https://example.test/formation">Formation</a><script>alert(1)</script><img src=x onerror=alert(1)><a href="javascript:alert(1)">Interdit</a>')
            ->set('couverture', UploadedFile::fake()->image('couverture.jpg'))->call('enregistrer')->assertHasNoErrors();
        $news = News::where('slug', 'nouvelle-actualite-editoriale')->firstOrFail();
        foreach (['<h2>Nos résultats</h2>', '<strong>Bravo</strong>', '<ul><li>Un projet</li></ul>', 'https://example.test/formation'] as $expected) {
            $this->assertStringContainsString($expected, $news->body);
        }
        foreach (['<script', '<img', 'javascript:', 'onerror'] as $bad) {
            $this->assertStringNotContainsString($bad, $news->body);
        }
        Storage::disk('public')->assertExists(str_replace('/storage/', '', $news->cover));
        $this->get(route('site.actualite', $news))->assertOk()->assertSee('<h2>Nos résultats</h2>', false)->assertDontSee('javascript:', false);
    }

    public function test_modifier_normalise_le_texte_et_nettoie_le_html_avant_affichage_editeur(): void
    {
        $news = News::create(['title' => 'Texte existant', 'slug' => 'texte-existant', 'category' => 'Événements', 'excerpt' => 'Résumé', 'body' => "Première ligne\nSeconde ligne\n\nDeuxième paragraphe", 'cover' => '/image.jpg']);
        Livewire::test(Actualites::class)->call('modifier', $news->id)
            ->assertSet('n.body', "<p>Première ligne<br />\nSeconde ligne</p>\n<p>Deuxième paragraphe</p>")
            ->set('n.body', '<p>Texte modifié</p>')->call('enregistrer')->assertHasNoErrors();
        $this->assertSame('<p>Texte modifié</p>', $news->fresh()->body);
        $news->update(['body' => '<p>À conserver</p><script>alert(1)</script>']);
        Livewire::test(Actualites::class)->call('modifier', $news->id)->assertSet('n.body', '<p>À conserver</p>');
    }

    public function test_un_contenu_vide_ou_uniquement_dangereux_est_refuse(): void
    {
        foreach (['<p><br></p>', '<p>&nbsp;</p>', '<script>alert(1)</script>'] as $body) {
            Livewire::test(Actualites::class)->call('creer')->set('n.title', 'Publication vide')->set('n.excerpt', 'Résumé')
                ->set('n.body', $body)->set('couverture', UploadedFile::fake()->image('image.jpg'))->call('enregistrer')->assertHasErrors('n.body');
        }
        $this->assertFalse(News::where('title', 'Publication vide')->exists());
    }
}
