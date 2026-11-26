<?php

namespace Tests\Feature;

use App\Livewire\Admin\Finances;
use App\Models\Expense;
use App\Services\CashService;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DepensesCaisseTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_depenses_sont_enregistrees_et_la_caisse_recalculee(): void
    {
        Storage::fake('local');
        Notification::fake();
        $this->seedBase();
        $admin = $this->makeStaff('Super Administrateur');
        $this->actingAs($admin);
        $a = $this->makeStudent(['DG']);
        $b = $this->makeStudent(['DG']);
        $pay = app(PaymentService::class);
        $pay->record($a->fresh(), 95000, 'Espèces');
        $pay->record($b->fresh(), 50000, 'Wave');
        $pay->record($b->fresh(), 200000, 'Wave'); // trop-perçu : ne doit pas réduire l'impayé de l'étudiant A

        $page = Livewire::actingAs($admin)->test(Finances::class)->set('vue', 'depenses')->call('nouvelleDepense')
            ->set('d.label', 'Loyer de novembre')->set('d.amount', 60000)->set('d.category', 'Internet, logiciels et abonnements')->set('d.method', 'Espèces')
            ->set('justificatif', UploadedFile::fake()->create('facture.pdf', 50, 'application/pdf'))->call('enregistrerDepense')->assertHasNoErrors();
        $e = Expense::sole();
        $this->assertSame([60000, 'Espèces', $admin->id], [$e->amount, $e->method, $e->recorded_by]);
        Storage::disk('local')->assertExists($e->receipt_path);
        $page->call('nouvelleDepense')->set('d.label', 'Câbles')->set('d.amount', 15000)->set('d.method', 'Wave')->call('enregistrerDepense')->assertHasNoErrors();
        $page->call('nouvelleDepense')->set('d.label', '')->set('d.amount', 0)->call('enregistrerDepense')->assertHasErrors(['d.label', 'd.amount']);

        $k = app(CashService::class)->summary(now()->startOfMonth(), now()->endOfMonth());
        $this->assertSame([345000, 75000, 270000, 345000, 75000, 270000], [$k['in'], $k['out'], $k['balance'], $k['periodIn'], $k['periodOut'], $k['periodResult']]);
        $this->assertSame($a->fresh(['schedules', 'payments'])->balance(), $k['due'], 'Le trop-perçu de B ne compense pas le solde dû par A.');
        $methods = collect(app(CashService::class)->byMethod(now()->startOfMonth(), now()->endOfMonth()))->keyBy('method');
        $this->assertSame(35000, $methods['Espèces']['balance']);
        $this->assertSame(235000, $methods['Wave']['balance']);

        $page->set('vue', 'depenses')->assertSee('Loyer de novembre')->assertSee('75 000')->set('vue', 'caisse')->assertSee('Soldes par caisse')->assertSee('270 000')
            ->assertSee('Internet, logiciels et abonnements')->assertSee('Évolution sur 6 mois');
        $page->call('modifierDepense', $e->id)->set('d.amount', 70000)->call('enregistrerDepense')->assertHasNoErrors();
        $this->assertSame(70000, $e->fresh()->amount);
        $this->get(route('fichier.depense', $e))->assertOk()->assertDownload('facture.pdf');
        $page->call('supprimerDepense', $e->id);
        $this->assertSame(15000, (int) Expense::sum('amount'));
        Storage::disk('local')->assertMissing($e->receipt_path);

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Solde de caisse');
        $this->actingAs($this->makeStaff('formateur', 'DG'))->get(route('fichier.depense', Expense::first()))->assertForbidden();
    }
}
