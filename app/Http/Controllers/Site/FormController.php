<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Formation;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Notifications\Avis;
use App\Notifications\CandidatureRecue;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

/** Formulaires du site : préinscription, contact, newsletter, suivi de candidature, vérification de certificat. */
class FormController extends Controller
{
    /** Correspondance entre les libellés du formulaire de la maquette et les codes de formation. */
    private const FORMATION_KEYS = ['design' => 'DG', 'community' => 'CM', 'bureautique' => 'GIA'];

    public function preinscription(Request $request)
    {
        $request->validate([
            'answers.Nom.0' => 'required|string|max:80', 'answers.Prénoms.0' => 'required|string|max:120',
            'answers.E-mail.0' => 'required|email|max:150', 'answers.Téléphone.0' => 'required|string|max:30',
            'answers.Formations' => 'required|array|min:1', 'answers.*.*' => 'nullable|string|max:3000',
            'fichiers.*.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf',
        ], [], ['answers.Nom.0' => 'nom', 'answers.Prénoms.0' => 'prénoms', 'answers.E-mail.0' => 'e-mail', 'answers.Téléphone.0' => 'téléphone',
            'answers.Formations' => 'formation', 'fichiers.*.*' => 'fichier']);

        $answers = collect($request->input('answers'))->map(fn ($v) => array_values(array_filter(Arr::wrap($v), fn ($x) => is_string($x) && trim($x) !== '')))->filter()->all();
        $one = fn (string $k) => $answers[$k][0] ?? null;
        $codes = collect($answers['Formations'])->map(function ($label) {
            $l = mb_strtolower($label);
            foreach (self::FORMATION_KEYS as $k => $code) {
                if (str_contains($l, $k)) {
                    return $code;
                }
            }

            return null;
        })->filter()->unique()->values()->all();
        $codes = array_values(array_intersect($codes, Formation::pluck('code')->all())) ?: [Formation::active()->value('code')];

        $c = DB::transaction(function () use ($request, $codes, $answers, $one) {
            $c = Candidature::create([
                'reference' => Candidature::nextReference(), 'first_name' => $one('Prénoms'), 'last_name' => $one('Nom'),
                'email' => strtolower($one('E-mail')), 'phone' => $one('Téléphone'), 'country' => $one('Pays'), 'city' => $one('Ville'),
                'formations' => $codes, 'mode' => $one('Modalité'), 'answers' => $answers,
            ]);
            foreach ($request->file('fichiers', []) as $label => $files) {
                foreach (Arr::wrap($files) as $file) {
                    $c->files()->create(['label' => $label, 'path' => $file->store('candidatures/'.$c->reference, 'local'),
                        'original_name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
                }
            }

            return $c;
        });

        Notification::route('mail', $c->email)->notify(new CandidatureRecue($c));
        User::permission('admin.candidatures')->get()->each->notify(new Avis('Nouvelle préinscription',
            $c->full_name.' — '.implode(' + ', $codes).' ('.$c->reference.')', route('admin.candidature', $c), 'fa-user-plus', 'cor', true));

        return response()->json(['reference' => $c->reference, 'suivi' => self::suiviUrl($c)]);
    }

    public static function suiviUrl(Candidature $c): string
    {
        return URL::temporarySignedRoute('candidature.suivi', now()->addDays(90), ['reference' => $c->reference]);
    }

    public function contact(Request $request)
    {
        $fields = collect($request->except(['_token', 'source']))->filter(fn ($v) => is_string($v) && trim($v) !== '')->map(fn ($v) => mb_substr($v, 0, 2000))->all();
        abort_if(empty($fields), 422, 'Formulaire vide.');
        $find = fn (array $keys) => collect($fields)->first(fn ($v, $k) => collect($keys)->contains(fn ($x) => str_contains(mb_strtolower($k), $x)));

        $m = ContactMessage::create(['source' => $request->input('source') === 'partenariat' ? 'partenariat' : 'contact',
            'name' => $find(['nom', 'structure', 'entreprise']), 'email' => $find(['mail']), 'phone' => $find(['téléphone', 'telephone', 'whatsapp']), 'fields' => $fields]);
        User::permission('admin.messages')->get()->each->notify(new Avis($m->source === 'partenariat' ? 'Demande de partenariat' : 'Nouveau message du site',
            ($m->name ?: 'Visiteur').' a écrit depuis le site.', route('admin.messages'), 'fa-envelope', 'info'));

        return response()->json(['ok' => true]);
    }

    public function newsletter(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:150']);
        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return response()->json(['ok' => true]);
    }

    public function suivi(Request $request)
    {
        $reference = strtoupper(trim((string) $request->query('reference')));
        $email = strtolower(trim((string) $request->query('email')));
        $found = $reference ? Candidature::where('reference', $reference)->first() : null;
        /* Accès par le lien sécurisé reçu après la préinscription, ou par référence + e-mail */
        $ok = $found && ($request->hasValidSignature() || ($email !== '' && $email === $found->email));

        return view('site.suivi', [
            'candidature' => $ok ? $found : null, 'reference' => $reference, 'email' => $email,
            'error' => $reference && ! $ok ? 'Aucune candidature ne correspond à cette référence et cet e-mail.' : null,
        ]);
    }

    public function verifier(Request $request)
    {
        $code = $request->filled('reference') ? Certificate::normalize($request->query('reference')) : null;
        $certificate = $code ? Certificate::with(['student.user', 'formation', 'registryRequest'])->where('code', $code)->first() : null;
        if ($certificate?->registryRequest) {
            if ($url = $certificate->registryRequest->verification_url) {
                return redirect()->away($url);
            }
            $certificate = null;
        }

        return view('site.verification', compact('code', 'certificate'));
    }
}
