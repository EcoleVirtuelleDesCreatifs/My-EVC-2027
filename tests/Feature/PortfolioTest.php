<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\SubmissionFile;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function validatedWork($s, string $title, string $appreciation, ?string $comment = null, bool $image = true): Assignment
    {
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => $title, 'type' => 'Intermédiaire', 'brief' => 'Concevoir une affiche.', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(4), 'status' => 'soumis', 'submitted_at' => now()]);
        $sub = $a->submissions()->create(['message' => 'Mon rendu', 'links' => ['https://behance.net/exemple']]);
        if ($image) {
            $img = imagecreatetruecolor(1200, 900);
            imagefill($img, 0, 0, imagecolorallocate($img, 238, 90, 54));
            ob_start();
            imagejpeg($img);
            Storage::disk('local')->put('depots/'.$a->id.'/affiche.jpg', ob_get_clean());
            $sub->files()->create(['path' => 'depots/'.$a->id.'/affiche.jpg', 'original_name' => 'affiche.jpg', 'size' => 1000]);
        }
        app(ProjectService::class)->validate($a->fresh(['project', 'student.user']), $appreciation, $comment);

        return $a->fresh();
    }

    public function test_le_portfolio_presente_la_couverture_et_seulement_les_projets_valides(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $this->validatedWork($s, 'Affiche Festival', 'Excellent', 'Composition forte et hiérarchie claire.');
        $this->validatedWork($s, 'Carte de visite', 'Acquis', null, false);
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Projet pas encore validé', 'brief' => 'x', 'published' => true]);
        Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(4), 'status' => 'soumis']);

        $this->actingAs($s->user)->get(route('etudiant.portfolio'))->assertOk()
            ->assertSee('Mon portfolio')->assertSee('Portfolio de projets validés')->assertSee($s->user->name)
            ->assertSee('Affiche Festival')->assertSee('Carte de visite')->assertDontSee('Projet pas encore validé')
            ->assertSee('Composition forte et hiérarchie claire.')->assertDontSee('Projet validé — appréciation')
            ->assertSee('Télécharger le catalogue PDF')->assertSee('fichiers/depot/', false);
    }

    public function test_portfolio_vide_et_apercu_reserve_au_titulaire(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $this->actingAs($s->user)->get(route('etudiant.portfolio'))->assertOk()->assertSee('Votre portfolio se construit')->assertDontSee('Télécharger le catalogue PDF');

        $this->validatedWork($s, 'Affiche Festival', 'Très bien');
        $file = SubmissionFile::firstOrFail();
        $preview = $this->actingAs($s->user)->get(route('fichier.depot.apercu', [$file, 'w' => 480]))->assertOk();
        $this->assertSame('image/jpeg', $preview->headers->get('Content-Type'));
        $this->assertSame(480, getimagesize($preview->baseResponse->getFile()->getPathname())[0]);

        $other = $this->makeStudent(['DG']);
        $this->actingAs($other->user)->get(route('fichier.depot.apercu', $file))->assertForbidden();
        $this->actingAs($other->user)->get(route('pdf.portfolio', $s))->assertForbidden();
    }

    public function test_le_catalogue_pdf_est_genere(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $this->validatedWork($s, 'Affiche Festival', 'Excellent', 'Très belle maîtrise.');
        $this->validatedWork($s, 'Brochure', 'Acquis', null, false);
        $response = $this->actingAs($s->user)->get(route('pdf.portfolio', $s))->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_tous_les_livrables_sont_presentes_dans_leur_format(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'Identité visuelle', 'type' => 'Avancé', 'brief' => 'Logo, affiche et charte.', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(4), 'status' => 'soumis']);
        $sub = $a->submissions()->create(['links' => ['https://www.behance.net/gallery/123']]);
        foreach (['affiche.jpg' => [800, 1200], 'banniere.png' => [1500, 500]] as $name => [$w, $h]) {
            $img = imagecreatetruecolor($w, $h);
            ob_start();
            str_ends_with($name, 'png') ? imagepng($img) : imagejpeg($img);
            Storage::disk('local')->put('depots/'.$name, ob_get_clean());
            $sub->files()->create(['path' => 'depots/'.$name, 'original_name' => $name, 'size' => 250000]);
        }
        Storage::disk('local')->put('depots/charte.pdf', '%PDF-1.4 test');
        $sub->files()->create(['path' => 'depots/charte.pdf', 'original_name' => 'charte.pdf', 'size' => 2400000]);
        Storage::disk('local')->put('depots/sources.zip', 'PK');
        $sub->files()->create(['path' => 'depots/sources.zip', 'original_name' => 'sources.zip', 'size' => 5000]);
        app(ProjectService::class)->validate($a->fresh(['project', 'student.user']), 'Excellent', null);

        $html = $this->actingAs($s->user)->get(route('etudiant.portfolio'))->assertOk()
            ->assertSee('5 livrables')->assertSee('affiche.jpg')->assertSee('banniere.png')->assertSee('charte.pdf')->assertSee('sources.zip')
            ->assertSee('www.behance.net')->assertSee('800 × 1200 px · portrait')->assertSee('1500 × 500 px · paysage')
            ->assertSee('Archive ZIP')->assertSee('2,3 Mo')->getContent();
        $this->assertStringContainsString('width="800" height="1200"', $html);
        $this->assertStringContainsString('width="1500" height="500"', $html);
        $this->assertStringNotContainsString('object-fit:cover', $html);

        $pdf = SubmissionFile::where('original_name', 'charte.pdf')->firstOrFail();
        $this->get(route('fichier.depot.apercu', $pdf))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $zip = SubmissionFile::where('original_name', 'sources.zip')->firstOrFail();
        $this->get(route('fichier.depot.apercu', $zip))->assertNotFound();
        $this->get(route('fichier.depot', $zip))->assertOk()->assertDownload('sources.zip');
        $this->get(route('pdf.portfolio', $s))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_l_administration_consulte_les_portfolios_de_chaque_etudiant(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $this->validatedWork($s, 'Affiche Festival', 'Excellent', 'Belle hiérarchie.');
        $this->validatedWork($s, 'Brochure', 'Acquis', null, false);
        $sans = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');

        $this->actingAs($admin)->get(route('admin.portfolios'))->assertOk()
            ->assertSee('Portfolios')->assertSee($s->user->name)->assertDontSee($sans->user->name)
            ->assertSee('Dernier projet validé')->assertSee('Voir le portfolio')->assertSee('fichiers/depot/', false);
        \Livewire\Livewire::actingAs($admin)->test(\App\Livewire\Admin\Portfolios::class)->set('avecProjets', false)->assertSee($sans->user->name)->assertSee('Aucun projet validé')
            ->set('q', $s->matricule)->assertSee($s->user->name)->assertDontSee($sans->user->name);

        $this->actingAs($admin)->get(route('admin.portfolio', $s))->assertOk()
            ->assertSee('Portfolio de projets validés')->assertSee('Affiche Festival')->assertSee('Brochure')->assertSee('Belle hiérarchie.')
            ->assertSee('Fiche étudiant')->assertSee('Tous les portfolios')->assertSee('Suivi du projet')->assertDontSee('Mes projets en cours');
        $this->actingAs($admin)->get(route('admin.etudiant', $s))->assertOk()->assertSee(route('admin.portfolio', $s), false);
        $this->actingAs($s->user)->get(route('admin.portfolios'))->assertForbidden();
        $this->actingAs($this->makeStaff('formateur', 'DG'))->get(route('admin.portfolios'))->assertForbidden();
    }
}
