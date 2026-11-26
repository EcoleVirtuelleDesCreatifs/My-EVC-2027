<?php

namespace Tests\Feature;

use App\Models\Candidature;
use App\Models\Promotion;
use App\Services\AdmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdmissionPhotoTest extends TestCase
{
    use RefreshDatabase;

    private function candidature(string $email): Candidature
    {
        $c = Candidature::create(['reference' => Candidature::nextReference(), 'first_name' => 'Awa', 'last_name' => 'Kouassi', 'email' => $email,
            'phone' => '0700000000', 'formations' => ['DG'], 'answers' => [], 'stage' => 'paiement',
            'first_payment' => ['amount' => 95000, 'method' => 'Wave', 'paid_on' => now()->toDateString()]]);
        $img = imagecreatetruecolor(1600, 1200);
        imagefill($img, 0, 0, imagecolorallocate($img, 30, 27, 75));
        ob_start();
        imagepng($img);
        Storage::disk('local')->put('candidatures/'.$c->reference.'/photo.png', ob_get_clean());
        $c->files()->create(['label' => 'Photo', 'path' => 'candidatures/'.$c->reference.'/photo.png', 'original_name' => 'moi.png', 'size' => 1000]);

        return $c;
    }

    public function test_la_photo_de_preinscription_devient_la_photo_de_profil(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Notification::fake();
        $this->seedBase();
        $student = app(AdmissionService::class)->activate($this->candidature('awa@example.test'), Promotion::firstOrFail());

        $user = $student->user->fresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
        $this->assertSame([800, 600], array_slice(getimagesize(Storage::disk('public')->path($user->avatar_path)), 0, 2));
        $this->actingAs($user)->get(route('myevc.profil'))->assertOk()->assertSee($user->avatarUrl(), false);
    }

    public function test_une_photo_deja_choisie_est_conservee_et_la_reprise_retroactive_fonctionne(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        Notification::fake();
        $this->seedBase();
        $c = $this->candidature('awa@example.test');
        $student = app(AdmissionService::class)->activate($c, Promotion::firstOrFail());
        $user = $student->user;
        $user->update(['avatar_path' => 'avatars/choisie.jpg']);
        $this->assertFalse(AdmissionService::importPhoto($c, $user->fresh()));
        $this->assertSame('avatars/choisie.jpg', $user->fresh()->avatar_path);

        $user->update(['avatar_path' => null]);
        $this->artisan('evc:photos-preinscription')->expectsOutputToContain('1 photo(s)')->assertSuccessful();
        $this->assertNotNull($user->fresh()->avatar_path);
    }
}
