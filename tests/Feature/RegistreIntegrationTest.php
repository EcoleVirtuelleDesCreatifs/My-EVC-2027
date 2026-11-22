<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\CertificateRequest;
use App\Models\Student;
use App\Notifications\CertificatDelivre;
use App\Services\RegistreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RegistreIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedBase();
        Notification::fake();
        Http::preventStrayRequests();
        config(['services.registre.url' => 'https://registre.example.test', 'services.registre.token' => str_repeat('a', 64)]);
    }

    private function document(Student $student, array $attributes = []): array
    {
        return $attributes + ['id' => 41, 'type' => 'certificat', 'formation' => 'DG', 'matricule' => $student->matricule,
            'statut' => 'brouillon', 'reference' => null, 'delivre_le' => null, 'mention' => 'Très bien',
            'pdf_disponible' => false, 'pdf_sha256' => null, 'url_verification' => null];
    }

    private function published(Student $student): array
    {
        return $this->document($student, ['statut' => 'publie', 'reference' => 'EVC-DG-2026-0009',
            'delivre_le' => '2026-11-22', 'pdf_disponible' => true, 'pdf_sha256' => hash('sha256', '%PDF-1.4 officiel'),
            'url_verification' => 'https://registre.example.test/v/EVC-DG-2026-0009']);
    }

    public function test_transmission_en_brouillon_sans_certificat_ni_mail_et_sans_double_post(): void
    {
        $s = $this->makeStudent(['DG']);
        Http::fake(['*' => Http::response($this->document($s), 200)]);
        $service = app(RegistreService::class);
        $request = $service->transmettre($s, $s->formations->first(), 'Très bien');
        $this->assertSame('brouillon', $request->status);
        $this->assertSame(41, $request->registry_document_id);
        $service->transmettre($s, $s->formations->first(), 'Très bien');
        $this->assertDatabaseCount('certificate_requests', 1);
        $this->assertDatabaseCount('certificates', 0);
        Notification::assertNothingSent();
        Http::assertSent(fn (Request $r) => $r->method() === 'POST' && $r['matricule'] === $s->matricule
            && $r['type'] === 'certificat' && isset($r['criteres']['Formation soldée']) && $r['details']['mention'] === 'Très bien');
        $this->assertCount(1, Http::recorded(fn (Request $r) => $r->method() === 'POST'));
        $this->assertSame('actif', $s->fresh()->status);
    }

    public function test_publication_seule_cree_la_reference_officielle_et_notifie_une_fois(): void
    {
        $s = $this->makeStudent(['DG']);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 41]);
        $data = $this->document($s, ['statut' => 'approuve', 'reference' => 'EVC-DG-2026-0009']);
        Http::fake(function () use (&$data) { return Http::response($data, 200); });
        $service = app(RegistreService::class);
        $service->synchroniser($request);
        $this->assertDatabaseCount('certificates', 0);
        Notification::assertNothingSent();
        $data = $this->published($s);
        $request = $service->synchroniser($request);
        $service->synchroniser($request);
        $this->assertDatabaseCount('certificates', 1);
        $this->assertSame('EVC-DG-2026-0009', $request->certificate->code);
        $this->assertNotNull($request->notified_at);
        $this->assertSame('certifie', $s->fresh()->status);
        Notification::assertSentToTimes($s->user, CertificatDelivre::class, 1);
        $this->actingAs($s->user)->get(route('etudiant.certificat'))->assertOk()->assertSee('Publié au registre')->assertSee('Télécharger mon certificat');
        $this->get(route('certificat.verifier', ['reference' => $request->reference]))->assertRedirect($request->verification_url);
    }

    public function test_telechargement_proxy_du_pdf_exact_et_blocage_apres_revocation(): void
    {
        $s = $this->makeStudent(['DG']);
        $other = $this->makeStudent(['CM']);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 41]);
        $data = $this->published($s);
        Http::fake(function (Request $r) use (&$data) {
            return str_contains($r->url(), '/pdf') ? Http::response('%PDF-1.4 officiel', 200, ['Content-Type' => 'application/pdf']) : Http::response($data, 200);
        });
        $request = app(RegistreService::class)->synchroniser($request);
        $url = route('pdf.certificat', $request->certificate);
        $this->actingAs($s->user)->get($url)->assertOk()->assertContent('%PDF-1.4 officiel');
        $count = count(Http::recorded());
        $this->actingAs($other->user)->get($url)->assertForbidden();
        Http::assertSentCount($count);
        $data = $this->document($s, ['statut' => 'revoque', 'reference' => $data['reference']]);
        $this->actingAs($s->user)->get($url)->assertStatus(409);
        $this->get(route('etudiant.certificat'))->assertOk()->assertSee('Certificat révoqué')->assertDontSee('Télécharger mon certificat');
    }

    public function test_indisponibilite_ou_pdf_altere_ne_genere_jamais_de_pdf_de_remplacement(): void
    {
        $s = $this->makeStudent(['DG']);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 41]);
        $unavailable = false;
        Http::fake(function (Request $r) use ($s, &$unavailable) {
            return $unavailable ? Http::response([], 503) : (str_contains($r->url(), '/pdf')
                ? Http::response('%PDF falsifié', 200) : Http::response($this->published($s), 200));
        });
        $request = app(RegistreService::class)->synchroniser($request);
        $url = route('pdf.certificat', $request->certificate);
        $unavailable = true;
        $this->actingAs($s->user)->get($url)->assertStatus(503);
        $unavailable = false;
        $this->get($url)->assertStatus(409);
        Notification::assertSentToTimes($s->user, CertificatDelivre::class, 1);
    }

    public function test_reponse_d_un_autre_apprenant_refusee_sans_modifier_le_dossier(): void
    {
        $s = $this->makeStudent(['DG']);
        Http::fake(['*' => Http::response($this->published($s), 200)]);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 42]);
        try {
            app(RegistreService::class)->synchroniser($request);
            $this->fail('Le dossier d’un autre identifiant doit être refusé.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('certificates', 0);
            Notification::assertNothingSent();
        }
    }

    public function test_publication_sans_pdf_officiel_complet_ne_notifie_pas(): void
    {
        $s = $this->makeStudent(['DG']);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 41]);
        $data = $this->published($s);
        $data['pdf_disponible'] = false;
        Http::fake(['*' => Http::response($data, 200)]);
        try {
            app(RegistreService::class)->synchroniser($request);
            $this->fail('Un PDF manquant doit bloquer la mise à disposition.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('certificates', 0);
            Notification::assertNothingSent();
        }
    }

    public function test_commande_de_synchronisation_et_identite_du_titulaire(): void
    {
        $s = $this->makeStudent(['DG']);
        $request = CertificateRequest::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id, 'registry_document_id' => 41]);
        $data = $this->published($s);
        $data['matricule'] = 'AUTRE-APPRENANT';
        Http::fake(function () use (&$data) { return Http::response($data, 200); });
        $this->artisan('registre:sync')->assertExitCode(1);
        $this->assertNull($request->fresh()->certificate_id);
        Notification::assertNothingSent();
        $data = $this->published($s);
        $this->artisan('registre:sync')->assertExitCode(0);
        $this->assertNotNull($request->fresh()->certificate_id);
        Notification::assertSentToTimes($s->user, CertificatDelivre::class, 1);
    }

    public function test_anciens_certificats_conserves_sans_renumerotation(): void
    {
        $s = $this->makeStudent(['DG']);
        $c = Certificate::create(['student_id' => $s->id, 'formation_id' => $s->formations->first()->id,
            'code' => 'EVC-CERT-2026-0453', 'issued_on' => now(), 'appreciation' => 'Très bien']);
        try {
            app(RegistreService::class)->transmettre($s, $s->formations->first(), 'Très bien');
            $this->fail('Un certificat existant ne doit pas être remplacé.');
        } catch (ValidationException) {
            $this->assertSame('EVC-CERT-2026-0453', $c->fresh()->code);
            $this->assertDatabaseCount('certificate_requests', 0);
            Http::assertNothingSent();
        }
    }
}
