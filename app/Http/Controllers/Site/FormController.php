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
    private const FORMATION_KEYS = [
        'design' => 'DG', 'community' => 'CM', 'bureautique' => 'GIA',
        'Infographie & design graphique' => 'DG', 'Community management & social media' => 'CM', 'Bureautique & informatique (GIA)' => 'GIA',
    ];

    /** Limites effectives d'envoi (octets) : 10 Mo par fichier au plus, dans la limite de la configuration PHP. */
    public static function uploadLimits(): array
    {
        $ini = fn (string $key) => ini_parse_quantity((string) ini_get($key)) ?: PHP_INT_MAX;
        $total = $ini('post_max_size');

        return ['file' => min(10 * 1048576, $ini('upload_max_filesize'), $total), 'total' => min($total, 512 * 1048576)];
    }

    public function preinscription(Request $request)
    {
        $limit = round(self::uploadLimits()['file'] / 1048576, 1);
        $request->validate([
            'answers' => 'required|array|max:80', 'answers.Formations.*' => 'required|string|max:150',
            'answers.Nom.0' => 'required|string|max:80', 'answers.Prénoms.0' => 'required|string|max:120',
            'answers.E-mail.0' => 'required|email|max:150', 'answers.Téléphone.0' => 'required|string|max:30',
            'answers.Formations' => 'required|array|min:1', 'answers.*.*' => 'nullable|string|max:3000',
            'answers.Date souhaitée d’inscription et de paiement.0' => 'required|date_format:Y-m-d|after_or_equal:today',
            'fichiers.*.*' => 'file|max:10240|mimes:jpg,jpeg,png,webp,pdf',
        ], [
            'answers.Date souhaitée d’inscription et de paiement.0.required' => 'Indiquez la date souhaitée pour votre inscription et votre premier paiement.',
            'answers.Date souhaitée d’inscription et de paiement.0.date_format' => 'Indiquez une date d’inscription et de paiement valide.',
            'answers.Date souhaitée d’inscription et de paiement.0.after_or_equal' => 'La date souhaitée d’inscription et de paiement doit être aujourd’hui ou une date à venir.',
            'fichiers.*.*.uploaded' => 'Un document joint n’a pas pu être reçu : chaque fichier doit peser au plus '.$limit.' Mo. Réduisez-le ou retirez-le, puis réessayez.',
            'fichiers.*.*.max' => 'Chaque document joint doit peser au plus 10 Mo.',
            'fichiers.*.*.mimes' => 'Les documents joints doivent être des images JPG, PNG ou WebP, ou des PDF.',
        ], ['answers.Nom.0' => 'nom', 'answers.Prénoms.0' => 'prénoms', 'answers.E-mail.0' => 'e-mail', 'answers.Téléphone.0' => 'téléphone',
            'answers.Formations' => 'formation', 'fichiers.*.*' => 'fichier']);

        $answers = collect($request->input('answers'))->map(fn ($v) => array_values(array_filter(Arr::wrap($v), fn ($x) => is_string($x) && trim($x) !== '')))->filter()->all();
        $one = fn (string $k) => $answers[$k][0] ?? null;
        $available = Formation::active()->get();
        $labels = [];
        foreach (self::FORMATION_KEYS as $label => $code) {
            if ($available->contains('code', $code)) {
                $labels[mb_strtolower($label)] = $code;
            }
        }
        foreach ($available as $formation) {
            foreach ([$formation->code, $formation->name, $formation->short_name] as $label) {
                $labels[mb_strtolower(trim($label))] = $formation->code;
            }
        }
        $selected = collect($answers['Formations'])->map(fn ($label) => $labels[mb_strtolower(trim($label))] ?? null);
        if ($selected->contains(null)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['answers.Formations' => 'Choisissez une formation ouverte aux inscriptions.']);
        }
        $codes = $selected->unique()->values()->all();

        $c = DB::transaction(function () use ($request, $codes, $answers, $one) {
            $c = Candidature::create([
                'reference' => Candidature::nextReference(), 'first_name' => $one('Prénoms'), 'last_name' => $one('Nom'),
                'email' => strtolower($one('E-mail')), 'phone' => $one('Téléphone'), 'country' => $one('Pays'), 'city' => $one('Ville'),
                'formations' => $codes, 'mode' => $one('Modalité'), 'answers' => $answers,
                'attribution' => $request->session()->get(\App\Http\Middleware\CaptureCampaign::KEY),
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

        return response()->json(['reference' => $c->reference, 'suivi' => self::suiviUrl($c), 'formations' => $codes]);
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
        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])])->update(['unsubscribed_at' => null]);

        return response()->json(['ok' => true]);
    }

    /* Désinscription depuis le lien présent dans chaque e-mail de newsletter. */
    public function desinscriptionNewsletter(Request $request)
    {
        $email = strtolower(trim((string) $request->query('email')));
        $abonne = $email !== '' ? NewsletterSubscriber::where('email', $email)->first() : null;
        $ok = $abonne && hash_equals($abonne->jeton(), (string) $request->query('jeton'));
        if ($ok) {
            $abonne->update(['unsubscribed_at' => now()]);
        }

        return view('site.newsletter-desinscription', ['ok' => $ok, 'email' => $ok ? $email : null]);
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
