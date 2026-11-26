<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Candidature;
use App\Models\Formation;
use App\Models\Project;
use App\Models\Promotion;
use App\Notifications\Avis;
use App\Services\AdmissionService;
use App\Services\ProjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProjetsAutoTest extends TestCase
{
    use RefreshDatabase;

    private function catalogue(string $code = 'DG'): \Illuminate\Support\Collection
    {
        $f = Formation::where('code', $code)->firstOrFail();
        Project::where('formation_id', $f->id)->delete();
        Project::create(['formation_id' => $f->id, 'title' => 'XX-99 — Brouillon', 'brief' => 'x', 'published' => false]);

        return collect(range(7, 1))->map(fn ($n) => Project::create(['formation_id' => $f->id, 'title' => sprintf('PS-%02d — Projet %d', $n, $n), 'brief' => 'Brief', 'published' => true]))
            ->sortBy('title')->values();
    }

    public function test_le_premier_projet_est_attribue_a_l_activation_avec_5_jours_et_un_email(): void
    {
        Notification::fake();
        $this->seedBase();
        $projects = $this->catalogue();
        $c = Candidature::create(['reference' => Candidature::nextReference(), 'first_name' => 'Awa', 'last_name' => 'Kouassi', 'email' => 'awa@example.test',
            'phone' => '0700000000', 'formations' => ['DG'], 'answers' => [], 'stage' => 'paiement',
            'first_payment' => ['amount' => 95000, 'method' => 'Wave', 'paid_on' => now()->toDateString()]]);
        $student = app(AdmissionService::class)->activate($c, Promotion::firstOrFail());

        $a = Assignment::where('student_id', $student->id)->sole();
        $this->assertSame($projects[0]->id, $a->project_id);
        $this->assertSame(now()->addDays(5)->toDateString(), $a->due_on->toDateString());
        $this->assertNull($a->assigned_by);
        Notification::assertSentTo($student->user, Avis::class, fn (Avis $n, array $channels) => in_array('mail', $channels, true)
            && str_contains($n->toMail($student->user)->render(), 'attribué automatiquement'));
    }

    public function test_chaque_rendu_declenche_le_projet_suivant_une_heure_plus_tard_jusqu_au_cinquieme(): void
    {
        Notification::fake();
        $this->seedBase();
        $projects = $this->catalogue();
        $s = $this->makeStudent(['DG']);
        $svc = app(ProjectService::class);
        $this->assertSame($projects[0]->id, $svc->assignNextAuto($s, $projects[0]->formation_id)->project_id);
        $this->assertNull($svc->assignNextAuto($s, $projects[0]->formation_id), 'Un seul projet du parcours à la fois.');

        for ($i = 0; $i < 5; $i++) {
            $current = Assignment::where('student_id', $s->id)->whereIn('status', Assignment::OPEN)->sole();
            $this->assertSame($projects[$i]->id, $current->project_id);
            $this->travel(1)->days();
            $svc->submit($current->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/rendu-'.$i], null);
            $this->assertSame($i + 1, Assignment::where('student_id', $s->id)->count(), 'Aucun envoi immédiat après le rendu.');
            $this->travel(59)->minutes();
            $this->artisan('evc:projets-auto')->assertSuccessful();
            $this->assertSame($i + 1, Assignment::where('student_id', $s->id)->count(), 'Toujours en attente avant 1 heure.');
            $this->travel(2)->minutes();
            $this->artisan('evc:projets-auto')->assertSuccessful();
        }

        $this->assertSame(5, Assignment::where('student_id', $s->id)->count(), 'Après le 5e rendu, plus d’attribution automatique.');
        $this->assertSame($projects->take(5)->pluck('id')->all(), Assignment::where('student_id', $s->id)->orderBy('id')->pluck('project_id')->all());
        $last = Assignment::where('student_id', $s->id)->orderByDesc('id')->first();
        $this->assertSame($last->created_at->copy()->addDays(5)->toDateString(), $last->due_on->toDateString());
        Notification::assertSentToTimes($s->user, Avis::class, 5);
    }

    public function test_un_projet_a_reprendre_ne_bloque_pas_et_n_est_pas_duplique(): void
    {
        Notification::fake();
        $this->seedBase();
        $projects = $this->catalogue();
        $s = $this->makeStudent(['DG']);
        $svc = app(ProjectService::class);
        $a = $svc->assignNextAuto($s, $projects[0]->formation_id);
        $svc->submit($a->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/1'], null);
        $svc->rework($a->fresh(['project', 'student.user']), 'Revoir la typographie.');
        $this->travel(2)->hours();
        $this->artisan('evc:projets-auto')->assertSuccessful();
        $this->assertSame(1, Assignment::where('student_id', $s->id)->count(), 'Projet à reprendre : le suivant attend.');
        $svc->submit($a->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/1b'], null);
        $this->travel(61)->minutes();
        $this->artisan('evc:projets-auto')->assertSuccessful();
        $this->artisan('evc:projets-auto')->assertSuccessful();
        $this->assertSame(2, Assignment::where('student_id', $s->id)->count());

        $this->actingAs($this->makeStaff('Super Administrateur'))->get(route('admin.projets', ['onglet' => 'catalogue']))->assertOk()->assertSee('Auto 1/5')->assertSee('Auto 5/5')->assertDontSee('Auto 6/5');
    }

    public function test_l_admin_configure_l_attribution_automatique(): void
    {
        Notification::fake();
        $this->seedBase();
        $projects = $this->catalogue();
        $s = $this->makeStudent(['DG']);
        $admin = $this->makeStaff('Super Administrateur');
        $svc = app(ProjectService::class);
        $a = $svc->assignNextAuto($s, $projects[0]->formation_id);
        $svc->submit($a->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/1'], null);

        $page = \Livewire\Livewire::actingAs($admin)->test(\App\Livewire\Admin\Projets::class)->set('onglet', 'auto')
            ->assertSee('Prochains envois')->assertSee($s->user->name)->assertSee($projects[1]->title)->assertSee('envoi prévu le');
        $page->set('auto.count', 3)->set('auto.days', 7)->set('auto.delay', '0')->call('enregistrerAuto')->assertHasNoErrors();
        $this->assertSame(['enabled' => true, 'count' => 3, 'days' => 7, 'delay' => 0], ProjectService::autoConfig());

        $page->call('envoyerMaintenant', $s->id, $projects[0]->formation_id);
        $b = Assignment::where('student_id', $s->id)->where('project_id', $projects[1]->id)->sole();
        $this->assertSame(now()->addDays(7)->toDateString(), $b->due_on->toDateString());
        $svc->submit($b->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/2'], null);
        $this->assertSame(3, Assignment::where('student_id', $s->id)->count(), 'Attente 0 : envoi immédiat au rendu.');
        $c = Assignment::where('student_id', $s->id)->orderByDesc('id')->first();
        $svc->submit($c->fresh(['project', 'student.user', 'student.formations']), [], ['https://example.test/3'], null);
        $this->assertSame(3, Assignment::where('student_id', $s->id)->count(), 'Parcours limité à 3 projets.');

        $page->set('auto.enabled', false)->call('enregistrerAuto');
        $other = $this->makeStudent(['DG']);
        $this->assertNull($svc->assignNextAuto($other, $projects[0]->formation_id));
        $this->artisan('evc:projets-auto')->expectsOutputToContain('désactivée')->assertSuccessful();
        $this->actingAs($admin)->get(route('admin.projets', ['onglet' => 'catalogue']))->assertDontSee('Auto 1/');
        $this->assertSame(0, $other->assignments()->count());
    }
}
