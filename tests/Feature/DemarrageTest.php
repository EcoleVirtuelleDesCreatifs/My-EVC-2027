<?php

namespace Tests\Feature;

use App\Livewire\Admin\Demarrage;
use App\Models\Formation;
use App\Models\StarterTool;
use App\Models\VirtualClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class DemarrageTest extends TestCase
{
    use RefreshDatabase;

    public function test_l_admin_configure_classes_et_outils_et_l_etudiant_les_voit(): void
    {
        Storage::fake('local');
        $this->seedBase();
        $admin = $this->makeStaff('Super Administrateur');
        $dg = Formation::where('code', 'DG')->firstOrFail();
        $cm = Formation::where('code', 'CM')->firstOrFail();
        $s = $this->makeStudent(['DG']);

        $page = Livewire::actingAs($admin)->test(Demarrage::class)
            ->call('nouvelleClasse')->set('c.name', 'Classe DG · Octobre')->set('c.url', 'http://pas-https.test')->set('c.formation_id', $dg->id)->call('enregistrerClasse')->assertHasErrors('c.url')
            ->set('c.url', 'https://chat.whatsapp.com/AbCdEf123')->set('c.schedule', 'Lundi et jeudi, 19 h')->call('enregistrerClasse')->assertHasNoErrors()
            ->call('nouvelleClasse')->set('c.name', 'Classe CM')->set('c.url', 'https://chat.whatsapp.com/Cm')->set('c.formation_id', $cm->id)->call('enregistrerClasse')
            ->call('nouvelleClasse')->set('c.name', 'Autre session DG')->set('c.url', 'https://chat.whatsapp.com/Old')->set('c.formation_id', $dg->id)
            ->set('c.promotion_id', (string) \App\Models\Promotion::create(['name' => 'Ancienne', 'starts_on' => now()->subYear(), 'ends_on' => now()->subMonths(6), 'status' => 'terminee'])->id)->call('enregistrerClasse');
        $this->assertSame(['WhatsApp', 'fa-brands fa-whatsapp'], VirtualClass::firstWhere('name', 'Classe DG · Octobre')->platform());

        $page->set('onglet', 'outils')->call('nouvelOutil', 'logiciel')->set('o.title', 'Adobe Photoshop')->call('enregistrerOutil')->assertHasErrors('fichier')
            ->set('o.url', 'https://www.adobe.com/fr/products/photoshop.html')->set('o.version', '2025 · Windows et macOS')->call('enregistrerOutil')->assertHasNoErrors()
            ->call('nouvelOutil', 'programme')->set('o.title', 'Programme DG')->set('o.formation_id', (string) $dg->id)
            ->set('fichier', UploadedFile::fake()->create('programme-dg.pdf', 300, 'application/pdf'))->call('enregistrerOutil')->assertHasNoErrors()
            ->call('nouvelOutil', 'support')->set('o.title', 'Support CM')->set('o.formation_id', (string) $cm->id)->set('o.url', 'https://example.test/cm.pdf')->call('enregistrerOutil')
            ->call('nouvelOutil', 'support')->set('o.title', 'Brouillon interne')->set('o.url', 'https://example.test/x')->set('o.published', false)->call('enregistrerOutil');
        $this->assertSame(4, StarterTool::count());

        $this->actingAs($s->user)->get(route('etudiant.projets'))->assertOk()->assertSee('Mes classes virtuelles')->assertSee('Classe DG · Octobre')
            ->assertSee('https://chat.whatsapp.com/AbCdEf123', false)->assertSee('Lundi et jeudi, 19 h')->assertDontSee('Classe CM')->assertDontSee('Autre session DG');
        $this->actingAs($s->user)->get(route('etudiant.demarrage'))->assertOk()->assertSee('Vos outils de démarrage')->assertSee('Outils de démarrage')
            ->assertSee('Adobe Photoshop')->assertSee('Site officiel')->assertSee('Programme DG')->assertSee('Télécharger')
            ->assertDontSee('Support CM')->assertDontSee('Brouillon interne')->assertSee('Classe DG · Octobre');

        $prog = StarterTool::firstWhere('title', 'Programme DG');
        $this->actingAs($s->user)->get(route('fichier.outil', $prog))->assertOk()->assertDownload('programme-dg.pdf');
        $this->assertSame(1, $prog->fresh()->downloads);
        $this->actingAs($this->makeStudent(['CM'])->user)->get(route('fichier.outil', $prog))->assertForbidden();
        $this->actingAs($s->user)->get(route('admin.demarrage'))->assertForbidden();

        Livewire::actingAs($admin)->test(Demarrage::class)->call('basculerClasse', VirtualClass::firstWhere('name', 'Classe DG · Octobre')->id);
        $this->actingAs($s->user)->get(route('etudiant.projets'))->assertOk()->assertDontSee('Mes classes virtuelles');
        $this->actingAs($admin)->get(route('admin.demarrage', ['onglet' => 'outils']))->assertOk()->assertSee('Logiciels')->assertSee('1 téléchargement');
    }
}
