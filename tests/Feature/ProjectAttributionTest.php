<?php

namespace Tests\Feature;

use App\Livewire\Admin\EtudiantFiche;
use App\Livewire\Admin\Projets;
use App\Models\Assignment;
use App\Models\Formation;
use App\Models\Project;
use App\Notifications\Avis;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectAttributionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Notification::fake();
        $this->actingAs($this->makeStaff('Super Administrateur'));
    }

    private function project(string $code = 'CM', array $attributes = []): Project
    {
        return Project::create($attributes + ['formation_id' => Formation::where('code', $code)->value('id'), 'title' => 'Projet '.fake()->unique()->numerify('###'), 'brief' => 'Consignes', 'published' => true]);
    }

    public function test_projets_deja_recus_exclus_des_options_mais_conserves_dans_historique(): void
    {
        $s = $this->makeStudent(['CM']);
        $other = $this->makeStudent(['CM']);
        $received = $this->project();
        $available = $this->project();
        $otherFormation = $this->project('DG');
        $draft = $this->project(attributes: ['published' => false]);
        Assignment::create(['project_id' => $received->id, 'student_id' => $s->id, 'due_on' => now()->addDays(3), 'status' => 'valide']);
        Assignment::create(['project_id' => $available->id, 'student_id' => $other->id, 'due_on' => now()->addDays(3)]);
        Livewire::test(EtudiantFiche::class, ['student' => $s])->call('ouvrirAttribution')
            ->assertViewHas('projects', fn ($projects) => $projects->pluck('id')->all() === [$available->id])
            ->assertSee($received->title)->assertDontSee('value="'.$received->id.'">CM', false);
        $this->assertDatabaseCount('assignments', 2);
    }

    public function test_aucune_reattribution_quel_que_soit_le_statut_et_sans_effacer_les_resultats(): void
    {
        $p = $this->project();
        foreach (array_keys(Assignment::STATUSES) as $status) {
            $s = $this->makeStudent(['CM']);
            $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'due_on' => now()->subDays(3), 'status' => $status,
                'started_at' => now()->subDays(6), 'submitted_at' => now()->subDays(4), 'validated_at' => $status === 'valide' ? now()->subDays(2) : null, 'appreciation' => 'Très bien']);
            $a->log('commentaire', 'Historique à conserver');
            $a->submissions()->create(['message' => 'Travail remis', 'links' => ['https://example.test/travail']]);
            $before = $a->fresh()->getAttributes();
            $this->assertSame([0, 1], app(ProjectService::class)->assign($p, collect([$s]), now()->addDays(10)));
            $this->assertSame($before, $a->fresh()->getAttributes());
            $this->assertSame(1, $a->comments()->count());
            $this->assertSame(1, $a->submissions()->count());
        }
        Notification::assertNothingSent();
        $this->assertDatabaseCount('assignments', count(Assignment::STATUSES));
    }

    public function test_apres_attribution_le_projet_disparait_et_les_clics_repetes_ne_renvoient_rien(): void
    {
        $s = $this->makeStudent(['CM']);
        $p = $this->project();
        $page = Livewire::test(EtudiantFiche::class, ['student' => $s])->call('ouvrirAttribution')->set('projet', $p->id)
            ->set('echeance', now()->addDays(3)->toDateString())->call('attribuer')->assertHasNoErrors();
        $a = Assignment::firstOrFail();
        $before = $a->getAttributes();
        $page->call('ouvrirAttribution')->assertViewHas('projects', fn ($projects) => $projects->isEmpty())
            ->set('projet', $p->id)->set('echeance', now()->addDays(8)->toDateString())->call('attribuer');
        $this->assertSame($before, $a->fresh()->getAttributes());
        $this->assertDatabaseCount('assignments', 1);
        Notification::assertSentToTimes($s->user, Avis::class, 1);
    }

    public function test_attribution_groupe_masque_les_anciens_destinataires_et_nettoie_selection(): void
    {
        $received = $this->makeStudent(['CM']);
        $available = $this->makeStudent(['CM']);
        $p = $this->project();
        Assignment::create(['project_id' => $p->id, 'student_id' => $received->id, 'due_on' => now()->subDay(), 'status' => 'valide']);
        Livewire::test(Projets::class)->set('onglet', 'catalogue')->call('ouvrirAttribution', [$received->id, $available->id], $p->id)
            ->assertSet('aEtudiants', [$available->id])->assertDontSee($received->matricule)->assertSee($available->matricule)
            ->set('aProjet', null)->set('aEtudiants', [$received->id, $available->id])->set('aProjet', $p->id)
            ->assertSet('aEtudiants', [$available->id]);
    }
}
