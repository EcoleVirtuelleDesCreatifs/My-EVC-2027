<?php

namespace Tests\Feature;

use App\Livewire\Admin\EtudiantFiche;
use App\Livewire\Etudiant\Rapport;
use App\Models\FinalReport;
use App\Notifications\Avis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RapportFinFormationTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_etudiant_depose_son_rapport_et_l_administration_le_relit(): void
    {
        Storage::fake('local');
        Notification::fake();
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');

        $this->actingAs($s->user)->get(route('etudiant.rapport'))->assertOk()->assertSee('Rapport de fin')->assertSee('À déposer')->assertSee('Déposer mon rapport');

        Livewire::actingAs($s->user)->test(Rapport::class)->set('fichier', UploadedFile::fake()->create('image.exe', 10))->call('deposer')->assertHasErrors('fichier');
        Livewire::actingAs($s->user)->test(Rapport::class)->set('fichier', UploadedFile::fake()->create('rapport-final.pdf', 800, 'application/pdf'))
            ->set('message', 'Merci pour cette formation.')->call('deposer')->assertHasNoErrors();
        $r = FinalReport::sole();
        $this->assertSame(['depose', 1, 'rapport-final.pdf'], [$r->status, $r->version, $r->original_name]);
        Storage::disk('local')->assertExists($r->path);
        Notification::assertSentTo($admin, Avis::class, fn ($n, $channels) => $n->title === 'Rapport de fin de formation' && in_array('mail', $channels, true));

        $page = Livewire::actingAs($admin)->test(EtudiantFiche::class, ['student' => $s])->set('onglet', 'rapport')
            ->assertSee('rapport-final.pdf')->assertSee('Merci pour cette formation.')->assertSee('Déposé, en relecture');
        $page->call('relireRapport', $r->id, false)->assertHasErrors('retour.'.$r->id);
        $page->set('retour.'.$r->id, 'Ajoutez le bilan des projets.')->call('relireRapport', $r->id, false)->assertHasNoErrors();
        $this->assertSame('a_reprendre', $r->fresh()->status);
        Notification::assertSentTo($s->user, Avis::class, fn ($n) => $n->title === 'Rapport à compléter' && str_contains($n->body, 'Ajoutez le bilan'));

        $old = $r->fresh()->path;
        Livewire::actingAs($s->user)->test(Rapport::class)->set('fichier', UploadedFile::fake()->create('rapport-v2.docx', 300))->call('deposer')->assertHasNoErrors();
        $r->refresh();
        $this->assertSame(['depose', 2, 'rapport-v2.docx'], [$r->status, $r->version, $r->original_name]);
        Storage::disk('local')->assertMissing($old);

        $this->actingAs($admin);
        $page->call('relireRapport', $r->id, true)->assertHasNoErrors();
        $this->assertSame('valide', $r->fresh()->status);
        $this->actingAs($s->user)->get(route('etudiant.rapport'))->assertOk()->assertSee('Validé')->assertDontSee('Déposer mon rapport');
        Livewire::actingAs($s->user)->test(Rapport::class)->set('fichier', UploadedFile::fake()->create('v3.pdf', 10))->call('deposer')->assertHasErrors('rapport');

        $this->actingAs($s->user)->get(route('fichier.rapport', $r))->assertOk()->assertDownload('rapport-v2.docx');
        $this->actingAs($this->makeStudent(['DG'])->user)->get(route('fichier.rapport', $r))->assertForbidden();
        $this->actingAs($admin)->get(route('fichier.rapport', $r))->assertOk();
    }

    public function test_la_photo_de_profil_s_affiche_dans_les_portfolios_de_l_administration(): void
    {
        Storage::fake('public');
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        Storage::disk('public')->put('avatars/photo.jpg', 'x');
        $s->user->update(['avatar_path' => 'avatars/photo.jpg']);
        $this->assertSame('/storage/avatars/photo.jpg', $s->user->fresh()->avatarUrl());
        \Livewire\Livewire::actingAs($this->makeStaff('Super Administrateur'))->test(\App\Livewire\Admin\Portfolios::class)->set('avecProjets', false)
            ->assertSeeHtml('<img src="/storage/avatars/photo.jpg"');
    }
}
