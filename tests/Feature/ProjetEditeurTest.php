<?php

namespace Tests\Feature;

use App\Livewire\Admin\ProjetEditeur;
use App\Models\Assignment;
use App\Models\Formation;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjetEditeurTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_pages_et_liens_du_catalogue_reserves_aux_habilites(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin)->get(route('admin.projet.nouveau'))->assertOk()->assertSee('Éditeur de consignes');
        $this->get(route('admin.projets', ['onglet' => 'catalogue']))->assertOk()->assertSee(route('admin.projet.nouveau'), false);
        $student = $this->makeStudent();
        $this->actingAs($student->user)->get(route('admin.projet.nouveau'))->assertForbidden();
    }

    public function test_creation_avec_mise_en_forme_et_nettoyage_du_html(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $f = Formation::where('code', 'CM')->firstOrFail();
        Livewire::actingAs($admin)->test(ProjetEditeur::class)
            ->set('p.formation_id', $f->id)->set('p.title', 'Projet éditorial')->set('p.type', 'Débutant')
            ->set('p.brief', '<h2>Objectif</h2><p onclick="alert(1)">Créer <strong>trois posts</strong>.</p><ul><li>Un visuel</li></ul><script>alert(1)</script><a href="javascript:alert(2)">lien</a>')
            ->call('enregistrer')->assertHasNoErrors()->assertRedirect(route('admin.projets', ['onglet' => 'catalogue', 'formation' => $f->id]));
        $this->assertSame('Projet créé.', session('toast'));
        $p = Project::where('title', 'Projet éditorial')->firstOrFail();
        $this->assertSame('html', $p->brief_format);
        $this->assertStringContainsString('<strong>trois posts</strong>', $p->brief);
        $this->assertStringContainsString('<li>Un visuel</li>', $p->brief);
        foreach (['<script', 'onclick', 'javascript:'] as $bad) {
            $this->assertStringNotContainsString($bad, $p->brief);
        }
    }

    public function test_modification_d_un_brief_texte_preserve_les_retours_et_la_formation_cm(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $f = Formation::where('code', 'CM')->firstOrFail();
        $p = Project::create(['formation_id' => $f->id, 'title' => 'CM-01', 'type' => 'Débutant', 'brief' => "Consigne une\nConsigne deux <texte>", 'published' => true]);
        $this->actingAs($admin)->get(route('admin.projet.modifier', $p))->assertOk();
        Livewire::actingAs($admin)->test(ProjetEditeur::class, ['project' => $p])->assertSet('projectId', $p->id)
            ->set('p.title', 'CM-01 modifié')->call('enregistrer')->assertHasNoErrors();
        $p->refresh();
        $this->assertSame($f->id, $p->formation_id);
        $this->assertStringContainsString('<br', $p->brief);
        $this->assertStringContainsString('&lt;texte&gt;', $p->brief);
        $this->assertDatabaseCount('projects', 1);
    }

    public function test_consigne_visuellement_vide_ou_trop_longue_refusee(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        foreach (['<p><br></p>', '<p>&nbsp; &#8203;</p>', '<script>texte</script>', '<p>'.str_repeat('a', 20001).'</p>'] as $brief) {
            Livewire::actingAs($admin)->test(ProjetEditeur::class)->set('p.title', 'Projet')->set('p.type', 'Projet')
                ->set('p.brief', $brief)->call('enregistrer')->assertHasErrors('p.brief');
        }
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_fiche_du_catalogue_structuree_sans_recrire_le_texte_en_base(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $f = Formation::where('code', 'CM')->firstOrFail();
        $text = "PROJET CM-01 · Débutant\nDurée indicative : 2 à 4 h\n\nANNONCEUR Mama Kitchen\nPROBLÉMATIQUE Définir la marque\nOBJECTIF Créer une fiche\nINDICATEURS DE\nRÉUSSITE\nFiche validée\nTEXTES FOURNIS Le goût de la maison\nLIVRABLES PDF 2 pages\nRÈGLES DE PRODUCTION COMMUNES\nImages libres de droits\nGrille d’évaluation\nNote sur 20";
        $p = Project::create(['formation_id' => $f->id, 'title' => 'CM-01 — Fiche marque', 'type' => 'Débutant', 'brief' => $text, 'published' => true]);
        $html = $p->briefHtml();
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('data-section="problematique"', $html);
        $this->assertStringContainsString('data-section="textes-fournis"', $html);
        $this->assertStringContainsString('INDICATEURS DE RÉUSSITE', $html);
        $this->assertStringContainsString('Images libres de droits', $html);
        $this->assertStringContainsString('Note sur 20', $html);
        $this->assertSame($text, $p->fresh()->brief);
        Livewire::actingAs($admin)->test(ProjetEditeur::class, ['project' => $p])->assertSet('p.brief', $html)
            ->assertSee('project-sheet-header', false)->call('enregistrer')->assertHasNoErrors();
        $p->refresh();
        $this->assertSame('html', $p->brief_format);
        $this->assertStringContainsString('data-section="problematique"', $p->brief);
        $this->assertStringContainsString('<details>', $p->brief);
        $this->assertStringContainsString('Fiche validée', $p->brief);
        $this->assertStringContainsString('Images libres de droits', $p->brief);
        $this->assertSame($f->id, $p->formation_id);
    }

    public function test_ancien_brief_deja_enregistre_en_html_simple_adopte_la_fiche_sans_perte(): void
    {
        $html = "PROJET CM-01 · Débutant<br />\nANNONCEUR Mama Kitchen<br />\nPROBLÉMATIQUE Définir la marque<br />\nOBJECTIF Créer une fiche<br />\nLIVRABLES PDF";
        $p = new Project(['brief_format' => 'html', 'brief' => $html]);
        $this->assertStringContainsString('<table>', $p->briefHtml());
        $this->assertStringContainsString('data-section="problematique"', $p->briefHtml());
        $this->assertStringContainsString('Mama Kitchen', $p->briefHtml());
        $this->assertSame($html, $p->brief);
        $custom = new Project(['brief_format' => 'html', 'brief' => '<h2>Objectif</h2><p>Créer <strong>trois posts</strong>.</p>']);
        $this->assertStringContainsString('<strong>trois posts</strong>', $custom->briefHtml());
        $this->assertStringNotContainsString('<table>', $custom->briefHtml());
    }

    public function test_modele_du_document_vide_ne_suffit_pas_pour_creer_un_projet(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        Livewire::actingAs($admin)->test(ProjetEditeur::class)->set('p.title', 'CM-02 — Nouveau projet')
            ->call('enregistrer')->assertHasErrors('p.brief');
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_tableaux_conserves_sans_style_script_ou_attribut_evenement(): void
    {
        $html = \App\Support\ProjectBrief::sanitize('<table style="background:red"><tbody><tr data-section="problematique" onclick="alert(1)"><th scope="row">PROBLÉMATIQUE</th><td>Texte <strong>important</strong><script>alert(2)</script></td></tr></tbody></table>');
        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('data-section="problematique"', $html);
        $this->assertStringContainsString('<strong>important</strong>', $html);
        foreach (['style=', 'onclick=', '<script', 'alert('] as $bad) {
            $this->assertStringNotContainsString($bad, $html);
        }
    }

    public function test_formation_d_un_projet_attribue_non_modifiable_et_rendu_etudiant_securise(): void
    {
        $admin = $this->makeStaff('Super Administrateur');
        $s = $this->makeStudent(['CM']);
        $f = $s->formations->first();
        $p = Project::create(['formation_id' => $f->id, 'title' => 'Projet CM', 'type' => 'Projet', 'brief_format' => 'html',
            'brief' => '<h2>Livrables</h2><p><strong>Deux posts</strong></p><img src=x onerror="alert(1)"><script>alert(2)</script>', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(3)]);
        Livewire::actingAs($admin)->test(ProjetEditeur::class, ['project' => $p])
            ->set('p.formation_id', Formation::where('code', 'DG')->value('id'))->call('enregistrer')->assertHasErrors('p.formation_id');
        $this->actingAs($s->user)->get(route('etudiant.projet', $a))->assertOk()
            ->assertSee('<strong>Deux posts</strong>', false)->assertDontSee('onerror=', false)->assertDontSee('<script>alert(2)', false);
        $this->assertSame($f->id, $p->fresh()->formation_id);
    }
}
