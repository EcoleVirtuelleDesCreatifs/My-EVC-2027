<?php

namespace Tests\Feature;

use App\Livewire\Etudiant\QuizPassage;
use App\Livewire\Etudiant\Reco;
use App\Livewire\Etudiant\Video as VideoPage;
use App\Livewire\Gestion\QuizEditeur;
use App\Livewire\Gestion\RecoFiche;
use App\Livewire\Gestion\WebTv;
use App\Livewire\Shared\Messagerie;
use App\Models\Conversation;
use App\Models\Formation;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\RecoProject;
use App\Models\Video;
use App\Services\MessagingService;
use App\Services\QuizService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Storage::fake('local');
    }

    private function quiz(string $code = 'DG', array $attrs = []): Quiz
    {
        $q = Quiz::create($attrs + ['formation_id' => Formation::where('code', $code)->value('id'), 'title' => 'QCM test', 'duration_min' => 10, 'attempts_allowed' => 2, 'pass_percent' => 60, 'published' => true]);
        foreach ([['A ?', ['oui', 'non'], 0], ['B ?', ['x', 'y', 'z'], 2], ['C ?', ['1', '2'], 1]] as $i => [$t, $o, $a]) {
            $q->questions()->create(['text' => $t, 'options' => $o, 'answer' => $a, 'position' => $i]);
        }

        return $q->load('questions');
    }

    /* ---------------- Web TV ---------------- */

    public function test_web_tv_publication_lecture_et_acces(): void
    {
        $trainer = $this->makeStaff('formateur', 'DG');
        $s = $this->makeStudent(['DG']);
        $other = $this->makeStudent(['CM']);

        Livewire::actingAs($trainer)->test(WebTv::class)->call('creer')
            ->set('v.title', 'Tutoriel calques')->set('v.source', 'fichier')->set('fichier', UploadedFile::fake()->create('calques.mp4', 900, 'video/mp4'))
            ->call('enregistrer')->assertHasNoErrors();
        $v = Video::firstOrFail();
        Storage::disk('local')->assertExists($v->path);
        $this->assertSame(1, $s->user->notifications()->count());

        $this->actingAs($s->user)->get(route('etudiant.video', $v))->assertOk()->assertSee('Tutoriel calques');
        $this->actingAs($s->user)->get(route('fichier.video', $v))->assertOk();
        $this->actingAs($other->user)->get(route('etudiant.video', $v))->assertForbidden();
        $this->actingAs($other->user)->get(route('fichier.video', $v))->assertForbidden();

        Livewire::actingAs($s->user)->test(VideoPage::class, ['video' => $v])->call('progression', 50, 100)->call('progression', 95, 100);
        $p = $v->progress()->where('student_id', $s->id)->first();
        $this->assertSame(95, $p->percent);
        $this->assertNotNull($p->completed_at);
        $this->assertSame(1, $v->fresh()->views_count);
    }

    public function test_lien_youtube_valide_et_converti(): void
    {
        $trainer = $this->makeStaff('formateur', 'DG');
        Livewire::actingAs($trainer)->test(WebTv::class)->call('creer')->set('v.title', 'Masterclass')->set('v.source', 'youtube')
            ->set('v.url', 'https://example.com/pas-youtube')->call('enregistrer')->assertHasErrors('v.url')
            ->set('v.url', 'https://youtu.be/dQw4w9WgXcQ')->call('enregistrer')->assertHasNoErrors();
        $this->assertStringContainsString('youtube-nocookie.com/embed/dQw4w9WgXcQ', Video::firstOrFail()->embedUrl());
    }

    /* ---------------- QCM ---------------- */

    public function test_quiz_correction_tentatives_et_temps(): void
    {
        $s = $this->makeStudent(['DG']);
        $q = $this->quiz();
        $svc = app(QuizService::class);
        [$a, $b, $c] = $q->questions;

        $att = $svc->start($q, $s);
        $this->assertSame($att->id, $svc->start($q, $s)->id, 'une tentative ouverte est reprise');
        $att = $svc->submit($att, [$a->id => 0, $b->id => 0, $c->id => 0]);
        $this->assertSame([1, 3, 33], [$att->score, $att->total, $att->percent]);
        $this->assertFalse($att->passed());

        // Deuxième tentative expirée : soumise automatiquement avec les réponses enregistrées
        $att2 = $svc->start($q, $s);
        $att2->update(['answers' => [$a->id => 1], 'started_at' => now()->subMinutes(30)]);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        try {
            $svc->start($q, $s);
        } finally {
            $this->assertNotNull($att2->fresh()->submitted_at);
            $this->assertSame(0, $att2->fresh()->score);
            $this->assertSame(2, QuizAttempt::where('student_id', $s->id)->count(), 'pas de 3e tentative');
        }
    }

    public function test_passage_du_quiz_depuis_l_interface(): void
    {
        $s = $this->makeStudent(['DG']);
        $q = $this->quiz();
        [$a, $b, $c] = $q->questions;
        Livewire::actingAs($s->user)->test(QuizPassage::class, ['quiz' => $q])->call('commencer')
            ->call('repondre', $a->id, 0)->call('repondre', $b->id, 2)->call('repondre', $c->id, 1)->call('soumettre')->assertSee('100 %');
        $this->assertSame(100, QuizAttempt::firstOrFail()->percent);
        // La bonne réponse n'est jamais envoyée au navigateur pendant le passage
        $this->assertFalse(str_contains(json_encode(Livewire::actingAs($s->user)->test(QuizPassage::class, ['quiz' => $this->quiz()])->call('commencer')->snapshot), '"answer"'));
    }

    public function test_quiz_hors_formation_ou_brouillon_inaccessible(): void
    {
        $s = $this->makeStudent(['CM']);
        $this->actingAs($s->user)->get(route('etudiant.quiz.passer', $this->quiz('DG')))->assertForbidden();
        $s2 = $this->makeStudent(['DG']);
        $this->actingAs($s2->user)->get(route('etudiant.quiz.passer', $this->quiz('DG', ['published' => false])))->assertForbidden();
    }

    public function test_editeur_de_quiz_et_portee_formateur(): void
    {
        $trainer = $this->makeStaff('formateur', 'DG');
        $q = $this->quiz('DG', ['published' => false]);
        Livewire::actingAs($trainer)->test(QuizEditeur::class, ['quiz' => $q])->call('nouvelleQuestion')
            ->set('qq.text', 'Nouvelle ?')->set('qq.options', ['a', ''])->call('enregistrerQuestion')->assertHasErrors('qq.options.1')
            ->set('qq.options', ['a', 'b', 'c'])->set('qq.answer', 2)->call('enregistrerQuestion')->assertHasNoErrors()->call('publier');
        $this->assertSame(4, $q->questions()->count());
        $this->assertTrue($q->fresh()->published);

        $cm = $this->makeStaff('formateur', 'CM');
        $this->actingAs($cm)->get(route('formateur.quiz.editeur', $q))->assertForbidden();
    }

    public function test_quiz_obligatoire_et_reco_dans_les_conditions_du_certificat(): void
    {
        $s = $this->makeStudent(['CM']);
        $q = $this->quiz('CM', ['required' => true]);
        $checks = $s->certificationChecks(Formation::where('code', 'CM')->first());
        $this->assertArrayHasKey('Quiz obligatoires réussis', $checks);
        $this->assertArrayHasKey('RECO 60 évalué', $checks);
        $this->assertFalse($checks['Quiz obligatoires réussis']);
        QuizAttempt::create(['quiz_id' => $q->id, 'student_id' => $s->id, 'answers' => [], 'score' => 3, 'total' => 3, 'percent' => 100, 'started_at' => now(), 'submitted_at' => now()]);
        $this->assertTrue($s->fresh()->certificationChecks(Formation::where('code', 'CM')->first())['Quiz obligatoires réussis']);
    }

    /* ---------------- RECO 60 ---------------- */

    public function test_cycle_complet_reco_60(): void
    {
        $s = $this->makeStudent(['CM']);
        $trainer = $this->makeStaff('formateur', 'CM');

        $lw = Livewire::actingAs($s->user)->test(Reco::class)
            ->set('creation.name', 'Lancement Bissap')->set('creation.description', 'Présence en ligne d\'une marque de jus.')->call('creer')->assertHasNoErrors();
        $p = RecoProject::firstOrFail();
        $this->assertSame(1, $p->day());
        $this->assertSame(1, $trainer->notifications()->count());

        $lw->call('ouvrirJournal')->set('e.action', 'Campagne Meta')->set('e.result', '260 clics')->set('e.hours', 3)
            ->set('preuve', UploadedFile::fake()->create('ads.pdf', 50, 'application/pdf'))->call('ajouterEntree')->assertHasNoErrors();
        $entry = $p->entries()->firstOrFail();

        $lw->set('rapport', UploadedFile::fake()->create('rapport.pdf', 120, 'application/pdf'));
        $lw->call('rendreRapport')->assertHasNoErrors();
        $this->assertSame('rendu', $p->fresh()->status);

        // Le rapport rendu, l'étudiant ne peut plus modifier le journal
        Livewire::actingAs($s->user)->test(Reco::class)->set('e', ['entry_date' => now()->toDateString(), 'action' => 'Encore', 'hours' => 1])->call('ajouterEntree')->assertStatus(422);
        $this->assertSame(1, $p->entries()->count());

        Livewire::actingAs($trainer)->test(RecoFiche::class, ['project' => $p->fresh()])
            ->set('retours.'.$entry->id, 'Bon CPC.')->call('commenter', $entry->id)
            ->set('appreciation', 'Très bien')->set('evaluation', 'Projet rigoureux et bien mesuré.')->call('evaluer')->assertHasNoErrors();
        $p->refresh();
        $this->assertSame(['evalue', 'Très bien', 'Bon CPC.'], [$p->status, $p->appreciation, $entry->fresh()->feedback]);
        $this->assertTrue($s->fresh()->certificationChecks(Formation::where('code', 'CM')->first())['RECO 60 évalué']);

        $this->actingAs($s->user)->get(route('fichier.reco', $entry))->assertOk();
        $this->actingAs($s->user)->get(route('fichier.reco.rapport', $p))->assertOk();
        $this->actingAs($this->makeStudent(['CM'])->user)->get(route('fichier.reco', $entry))->assertForbidden();
        $this->actingAs($this->makeStaff('formateur', 'DG'))->get(route('formateur.reco.fiche', $p))->assertForbidden();
    }

    /* ---------------- Messagerie ---------------- */

    public function test_messagerie_destinataires_envoi_et_non_lus(): void
    {
        $s = $this->makeStudent(['DG']);
        $dg = $this->makeStaff('formateur', 'DG');
        $cm = $this->makeStaff('formateur', 'CM');
        $other = $this->makeStudent(['DG']);
        $svc = app(MessagingService::class);

        $ids = $svc->recipients($s->user)->pluck('id');
        $this->assertTrue($ids->contains($dg->id));
        $this->assertFalse($ids->contains($cm->id), 'un étudiant n\'écrit qu\'aux formateurs de ses formations');
        $this->assertFalse($ids->contains($other->user_id), 'pas de messagerie entre étudiants');

        Livewire::actingAs($s->user)->test(Messagerie::class)->call('ouvrirNouveau')
            ->set('destinataires', [$dg->id, $other->user_id])->set('objet', 'Question logo')->set('premier', 'Bonjour, une question.')->call('demarrer')->assertHasNoErrors();
        $c = Conversation::firstOrFail();
        $this->assertEqualsCanonicalizing([$s->user_id, $dg->id], $c->participants->pluck('id')->all(), 'un destinataire non autorisé est ignoré');
        $this->assertSame(1, Conversation::unreadCount($dg));
        $this->assertSame(0, Conversation::unreadCount($s->user));

        Livewire::actingAs($dg)->test(Messagerie::class, ['conversation' => $c->id])->call('ouvrir', $c->id)
            ->set('texte', 'Passe jeudi.')->set('piece', UploadedFile::fake()->create('brief.pdf', 30, 'application/pdf'))->call('envoyer')->assertHasNoErrors();
        $this->assertSame(0, Conversation::unreadCount($dg));
        $this->assertSame(1, Conversation::unreadCount($s->user));

        $m = $c->messages()->whereNotNull('file_path')->firstOrFail();
        $this->actingAs($s->user)->get(route('fichier.message', $m))->assertOk();
        $this->actingAs($other->user)->get(route('fichier.message', $m))->assertForbidden();
        Livewire::actingAs($other->user)->test(Messagerie::class)->call('ouvrir', $c->id)->assertDontSee('Passe jeudi')->assertDontSee('Question logo');
    }
}
