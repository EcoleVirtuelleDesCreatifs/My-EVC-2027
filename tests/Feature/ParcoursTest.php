<?php

namespace Tests\Feature;

use App\Livewire\Admin\CandidatureFiche;
use App\Livewire\Etudiant\ProjetDetail;
use App\Livewire\Formateur\Corrections;
use App\Models\Assignment;
use App\Models\Candidature;
use App\Models\Formation;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Promotion;
use App\Models\Resource;
use App\Models\Seance;
use App\Notifications\CompteActive;
use App\Services\PaymentService;
use App\Services\PresenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

/** Parcours métier : admission, projets, paiements, présences, bibliothèque, certificat. */
class ParcoursTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
    }

    public function test_activation_d_une_candidature(): void
    {
        Notification::fake();
        $admin = $this->makeStaff('Super Administrateur');
        $c = Candidature::create(['reference' => 'PRE-2026-0001', 'first_name' => 'Rose', 'last_name' => 'Kacou', 'email' => 'rose@exemple.ci', 'phone' => '0700', 'formations' => ['DG', 'CM'], 'stage' => 'paiement']);

        Livewire::actingAs($admin)->test(CandidatureFiche::class, ['candidature' => $c])
            ->set('promotion', Promotion::current()->id)->set('mode', 'En ligne')
            ->call('confirmerActivation')->assertRedirect();

        $c->refresh();
        $this->assertSame('activee', $c->stage);
        $s = $c->student;
        $this->assertMatchesRegularExpression('/^EVC-\d\d-\d{4}$/', $s->matricule);
        $this->assertTrue($s->user->hasRole('etudiant'));
        $this->assertEqualsCanonicalizing(['DG', 'CM'], $s->formations->pluck('code')->all());
        $this->assertSame(Formation::whereIn('code', ['DG', 'CM'])->sum('price'), $s->totalDue());
        Notification::assertSentTo($s->user, CompteActive::class);

        /* Inscription après le début de la session : le solde reste postérieur à l'acompte */
        foreach ($s->schedules->groupBy('formation_id') as $rows) {
            $acompte = $rows->first(fn ($x) => str_starts_with($x->label, 'Acompte'));
            $solde = $rows->first(fn ($x) => str_starts_with($x->label, 'Solde'));
            $this->assertTrue($solde->due_on->gt($acompte->due_on));
        }
    }

    public function test_les_versements_sont_imputes_aux_echeances_les_plus_anciennes(): void
    {
        Notification::fake();
        $s = $this->makeStudent(['DG']);   // acompte 95 000 (échu) puis solde 90 000
        $this->actingAs($this->makeStaff('Responsable financier'));
        app(PaymentService::class)->record($s, 120000, 'Wave');
        $s->refreshAllocation();
        [$acompte, $solde] = $s->scheduleRows()->values()->all();
        $this->assertSame(0, $acompte->remaining());
        $this->assertSame(65000, $solde->remaining());
        $this->assertSame(65000, $s->balance());
        $this->assertSame('REC-'.now()->year.'-0001', Payment::first()->receipt_number);

        $this->actingAs($s->user)->get(route('pdf.recu', Payment::first()))->assertOk()->assertHeader('content-type', 'application/pdf');
        $other = $this->makeStudent(['CM']);
        $this->actingAs($other->user)->get(route('pdf.recu', Payment::first()))->assertForbidden();
    }

    public function test_cycle_complet_d_un_projet(): void
    {
        Storage::fake('local');
        Notification::fake();
        $s = $this->makeStudent(['DG']);
        $trainer = $this->makeStaff('formateur', 'DG');
        $p = Project::create(['formation_id' => Formation::where('code', 'DG')->value('id'), 'title' => 'Logo maquis', 'brief' => 'Créer un logo.']);
        $this->actingAs($this->makeStaff('Super Administrateur'));
        app(\App\Services\ProjectService::class)->assign($p, collect([$s]), now()->addDays(7));
        $a = Assignment::firstOrFail();
        $this->assertSame('afaire', $a->status);

        Livewire::actingAs($s->user)->test(ProjetDetail::class, ['assignment' => $a])
            ->call('demarrer')
            ->set('fichiers', [UploadedFile::fake()->create('logo.pdf', 200, 'application/pdf')])
            ->set('liens', 'https://behance.net/demo pas-un-lien')
            ->call('deposer')->assertHasNoErrors();
        $a->refresh();
        $this->assertSame('soumis', $a->status);
        $this->assertSame(['https://behance.net/demo'], $a->submissions[0]->links);

        Livewire::actingAs($trainer)->test(Corrections::class)
            ->call('ouvrirCorrection', $a->id)->set('commentaire', '')->call('aReprendre')->assertHasErrors('commentaire')
            ->set('commentaire', 'Revoir les couleurs.')->call('aReprendre');
        $this->assertSame('reprendre', $a->fresh()->status);

        $a->update(['status' => 'soumis']);
        Livewire::actingAs($trainer)->test(Corrections::class)->call('ouvrirCorrection', $a->id)->set('appreciation', 'Excellent')->call('valider');
        $this->assertSame('valide', $a->fresh()->status);
        $this->assertSame('Excellent', $a->fresh()->appreciation);

        /* Un autre étudiant ne peut pas ouvrir ce projet */
        $other = $this->makeStudent(['DG']);
        $this->actingAs($other->user)->get(route('etudiant.projet', $a))->assertForbidden();
    }

    public function test_pointage_present_puis_en_retard(): void
    {
        $s = $this->makeStudent(['DG']);
        $f = Formation::where('code', 'DG')->first();
        $now = Seance::create(['formation_id' => $f->id, 'title' => 'Plume', 'starts_at' => now()->subMinutes(2), 'ends_at' => now()->addHour()]);
        $late = Seance::create(['formation_id' => $f->id, 'title' => 'Calques', 'starts_at' => now()->subMinutes(30), 'ends_at' => now()->addHour()]);
        $closed = Seance::create(['formation_id' => $f->id, 'title' => 'Demain', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHours(2)]);
        $svc = app(PresenceService::class);
        $this->assertSame('present', $svc->checkIn($s, $now)->status);
        $this->assertSame('retard', $svc->checkIn($s, $late)->status);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $svc->checkIn($s, $closed);
    }

    public function test_bibliotheque_reservee_aux_formations_de_l_etudiant(): void
    {
        Storage::fake('local');
        $s = $this->makeStudent(['CM']);
        $dg = Formation::where('code', 'DG')->value('id');
        $cm = Formation::where('code', 'CM')->value('id');
        Storage::disk('local')->put('library/guide.pdf', '%PDF');
        $mine = Resource::create(['formation_id' => $cm, 'category' => 'ebooks', 'title' => 'Guide CM', 'description' => 'd', 'format' => 'PDF', 'path' => 'library/guide.pdf']);
        $common = Resource::create(['formation_id' => null, 'category' => 'supports', 'title' => 'Règlement', 'description' => 'd', 'format' => 'PDF', 'url' => 'https://exemple.ci/reglement']);
        $other = Resource::create(['formation_id' => $dg, 'category' => 'ebooks', 'title' => 'Guide DG', 'description' => 'd', 'format' => 'PDF', 'path' => 'library/guide.pdf']);

        $this->actingAs($s->user)->get(route('etudiant.bibliotheque'))->assertOk()->assertSee('Guide CM')->assertSee('Règlement')->assertDontSee('Guide DG');
        $this->actingAs($s->user)->get(route('etudiant.ressource', $mine))->assertOk()->assertDownload('guide-cm.pdf');
        $this->actingAs($s->user)->get(route('etudiant.ressource', $mine))->assertOk();
        $this->assertSame(1, $mine->fresh()->downloads_count);
        $this->actingAs($s->user)->get(route('etudiant.ressource', $common))->assertRedirect('https://exemple.ci/reglement');
        $this->actingAs($s->user)->get(route('etudiant.ressource', $other))->assertForbidden();
    }

    public function test_conditions_de_certification(): void
    {
        $s = $this->makeStudent(['GIA']);
        $f = Formation::where('code', 'GIA')->first();
        $this->assertFalse($s->isEligible($f));
        $checks = $s->certificationChecks($f);
        $this->assertFalse($checks['Formation soldée']);
        $this->assertFalse($checks['Tous les projets validés']);
    }
}
