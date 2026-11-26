<?php

namespace Tests\Feature;

use App\Livewire\Etudiant\ProjetDetail;
use App\Models\Assignment;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ModificationRenduTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_etudiant_modifie_son_rendu_tant_qu_il_n_est_pas_corrige(): void
    {
        Storage::fake('local');
        Notification::fake();
        $this->seedBase();
        $s = $this->makeStudent(['DG']);
        $p = Project::create(['formation_id' => $s->formations->first()->id, 'title' => 'AI-01 — Logo', 'brief' => 'x', 'published' => true]);
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->addDays(5), 'status' => 'encours']);
        app(ProjectService::class)->submit($a->fresh(['project', 'student.user', 'student.formations']), [UploadedFile::fake()->image('v1.png'), UploadedFile::fake()->create('notes.pdf', 10)], [], 'Première version');
        $submittedAt = $a->fresh()->submitted_at;
        $v1 = $a->fresh()->submissions()->first()->files->firstWhere('original_name', 'v1.png');

        $this->actingAs($s->user)->get(route('etudiant.projet', $a))->assertOk()->assertSee('Mon rendu en cours de validation')->assertSee('Modifier mon rendu');
        $page = Livewire::actingAs($s->user)->test(ProjetDetail::class, ['assignment' => $a->fresh()])->call('modifierRendu')
            ->assertSet('message', 'Première version')->assertSee('v1.png');
        $page->call('basculerRetrait', $v1->id)->set('fichiers', [UploadedFile::fake()->image('v2.png')])
            ->set('liens', 'https://www.behance.net/v2')->set('message', 'Version corrigée')->call('enregistrerModification')->assertHasNoErrors()->assertSet('modification', false);

        $sub = $a->fresh()->submissions()->first();
        $this->assertSame(1, $a->submissions()->count());
        $this->assertEqualsCanonicalizing(['notes.pdf', 'v2.png'], $sub->files->pluck('original_name')->all());
        $this->assertSame(['https://www.behance.net/v2'], $sub->links);
        $this->assertSame('Version corrigée', $sub->message);
        Storage::disk('local')->assertMissing($v1->path);
        $this->assertSame('soumis', $a->fresh()->status);
        $this->assertTrue($submittedAt->equalTo($a->fresh()->submitted_at), 'La date de rendu d’origine est conservée.');
        $this->assertStringContainsString('Rendu modifié', $a->comments()->reorder()->latest('id')->first()->body);

        $page->call('modifierRendu');
        foreach ($sub->files as $f) {
            $page->call('basculerRetrait', $f->id);
        }
        $page->set('liens', '')->call('enregistrerModification')->assertHasErrors('depot');

        $this->actingAs($this->makeStaff('Super Administrateur'));
        app(ProjectService::class)->validate($a->fresh(['project', 'student.user']), 'Acquis', null);
        $this->actingAs($s->user)->get(route('etudiant.projet', $a))->assertOk()->assertDontSee('Modifier mon rendu');
        Livewire::actingAs($s->user)->test(ProjetDetail::class, ['assignment' => $a->fresh()])->call('modifierRendu')->assertForbidden();
    }
}
