<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Formation;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\RecoProject;
use App\Models\Seance;
use App\Models\Student;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use App\Support\DemoFile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Démonstration des modules Web TV, RECO 60, QCM et messagerie. */
class Phase2DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new \RuntimeException('Les données de démonstration sont interdites en production.');
        }
        if (Video::exists() || ! User::where('email', 'admin@evc.test')->exists()) {
            return;
        }
        $f = Formation::all()->keyBy('code');
        $aya = Student::where('matricule', 'EVC-26-0142')->firstOrFail();
        $trainer = fn (string $code) => User::whereHas('trainerProfiles', fn ($q) => $q->where('formation_id', $f[$code]->id))->first();

        /* ---------- Web TV : tutoriels et replays (vidéos de démonstration lisibles) ---------- */
        $software = ['DG' => ['Adobe Photoshop', 'Adobe Illustrator', 'Adobe InDesign'], 'CM' => ['Canva', 'CapCut', 'Meta Business Suite'], 'GIA' => ['Microsoft Office', 'Google Chrome', 'LibreOffice']];
        foreach ($f as $code => $fo) {
            $demo = __DIR__.'/data/videos/demo-'.strtolower($code).'.mp4';
            $store = function (string $title) use ($demo) {
                $path = 'webtv/'.Str::slug($title).'-'.Str::random(6).'.mp4';
                Storage::disk('local')->put($path, file_get_contents($demo));

                return $path;
            };
            foreach ($software[$code] as $i => $sw) {
                Video::create(['formation_id' => $fo->id, 'module' => 'Bien démarrer', 'title' => 'Installer '.$sw, 'description' => "Téléchargement, installation et premiers réglages de $sw, pas à pas.",
                    'source' => 'fichier', 'path' => $store('installer '.$sw), 'duration_min' => 6 + $i * 2, 'views_count' => 90 + $i * 31, 'created_at' => now()->subDays(40)]);
            }
            $past = Seance::where('formation_id', $fo->id)->where('ends_at', '<', now())->orderByDesc('starts_at')->take(6)->get();
            foreach ($past as $k => $se) {
                Video::create(['formation_id' => $fo->id, 'seance_id' => $se->id, 'module' => 'Séances', 'title' => 'Replay — '.$se->title, 'description' => 'Enregistrement de la séance « '.$se->title.' » ('.$se->module.').',
                    'source' => 'fichier', 'path' => $store('replay '.$se->title), 'duration_min' => 85 + ($k * 7) % 30, 'views_count' => 12 + ($k * 13) % 40, 'created_at' => $se->ends_at->copy()->addHours(3)]);
            }
            Video::create(['formation_id' => $fo->id, 'module' => 'Masterclass', 'title' => ['DG' => 'Masterclass : construire son portfolio', 'CM' => 'Masterclass : vivre du community management', 'GIA' => 'Masterclass : Excel au service de l\'entreprise'][$code],
                'description' => 'Intervention d\'un professionnel invité, questions-réponses avec les étudiants.', 'source' => 'fichier', 'path' => $store('masterclass '.$code), 'duration_min' => 64, 'views_count' => 140, 'created_at' => now()->subDays(12)]);
        }
        $vids = Video::whereIn('formation_id', $aya->formations->pluck('id'))->orderBy('id')->get();
        foreach ($vids->take(7) as $i => $v) {
            VideoProgress::create(['video_id' => $v->id, 'student_id' => $aya->id, 'seconds' => $i === 6 ? 14 : 30, 'percent' => $i === 6 ? 46 : 100, 'completed_at' => $i === 6 ? null : now()->subDays(10 - $i)]);
        }

        /* ---------- QCM ---------- */
        $bank = [
            'DG' => [
                ['QCM — Photoshop', true, -20, [
                    ['Quel format conserve les calques d\'un document Photoshop ?', ['.JPG', '.PSD', '.PNG', '.GIF'], 1, 'Le PSD est le format natif : il garde calques, masques et réglages.'],
                    ['Quel mode colorimétrique choisir pour un document imprimé ?', ['RVB', 'CMJN', 'Niveaux de gris uniquement', 'HSL'], 1, 'L\'imprimerie travaille en quadrichromie : Cyan, Magenta, Jaune, Noir.'],
                    ['Que sont les fonds perdus ?', ['Une marge de sécurité intérieure', 'Une extension de l\'image au-delà du format de coupe', 'Un type de papier', 'Un calque masqué'], 1, 'On prolonge le visuel de 3 à 5 mm pour éviter un liseré blanc à la coupe.'],
                    ['Quelle résolution viser pour une affiche imprimée de qualité ?', ['72 dpi', '150 dpi', '300 dpi', '600 dpi'], 2, '300 dpi à la taille d\'impression est la norme pour l\'imprimé.'],
                    ['À quoi sert un masque de fusion ?', ['Supprimer définitivement des pixels', 'Masquer une partie d\'un calque sans la détruire', 'Fusionner tous les calques', 'Changer la taille de la toile'], 1, 'Le masque est non destructif : le noir masque, le blanc révèle.'],
                    ['Quel outil permet de corriger une imperfection en copiant une zone voisine ?', ['Le tampon de duplication', 'La baguette magique', 'Le lasso', 'Le dégradé'], 0, null]]],
                ['QCM — InDesign', false, 2, [
                    ['Dans InDesign, à quoi sert une page type (gabarit) ?', ['Exporter en PDF', 'Appliquer des éléments communs à plusieurs pages', 'Changer la langue du document', 'Compresser les images'], 1, null],
                    ['Quel élément permet d\'appliquer la même mise en forme à tous les titres ?', ['Un style de paragraphe', 'Un calque', 'Une bibliothèque', 'Un nuancier'], 0, 'Modifier le style met à jour tous les titres d\'un coup.'],
                    ['Que signifie un « + » rouge en bas d\'un bloc de texte ?', ['Le texte est en anglais', 'Il y a du texte en excès non affiché', 'Le bloc est verrouillé', 'Le texte est lié à Excel'], 1, null],
                    ['Quel format d\'export envoyer à l\'imprimeur ?', ['PDF X-1a / X-4', 'JPG', 'INDD', 'DOCX'], 0, null],
                    ['À quoi sert l\'assemblage (Fichier > Assembler) ?', ['Fusionner deux documents', 'Réunir le document, les polices et les images liées', 'Imprimer en recto verso', 'Créer une table des matières'], 1, null]]]],
            'CM' => [
                ['QCM — Stratégie digitale', true, -15, [
                    ['Qu\'est-ce qu\'un persona ?', ['Un influenceur', 'Le portrait type d\'un client cible', 'Un faux compte', 'Un logiciel de planification'], 1, null],
                    ['Un objectif SMART doit être…', ['Simple, Moderne, Attractif, Rapide, Tendance', 'Spécifique, Mesurable, Atteignable, Réaliste, Temporel', 'Social, Marketing, Agile, Rentable, Testé', 'Aucune de ces réponses'], 1, null],
                    ['Quel indicateur mesure l\'interaction avec une publication ?', ['La portée', 'Le taux d\'engagement', 'Le nombre d\'abonnés', 'Le CPM'], 1, 'Engagement = réactions + commentaires + partages, rapportés à la portée ou aux abonnés.'],
                    ['À quoi sert une ligne éditoriale ?', ['Fixer le ton, les sujets et les formats de la marque', 'Payer les publicités', 'Supprimer les commentaires', 'Créer un logo'], 0, null],
                    ['Quel réseau privilégier pour toucher des décideurs B2B ?', ['TikTok', 'LinkedIn', 'Snapchat', 'Pinterest'], 1, null]]],
                ['QCM — Publicité Meta', false, 3, [
                    ['Où crée-t-on une campagne publicitaire Facebook et Instagram ?', ['Meta Ads Manager', 'Google Analytics', 'Canva', 'CapCut'], 0, null],
                    ['Que signifie CPC ?', ['Coût par clic', 'Contenu par campagne', 'Clients par commande', 'Coût pour création'], 0, null],
                    ['Une audience similaire (lookalike) cible…', ['Des personnes proches de vos clients existants', 'Uniquement vos abonnés', 'Vos concurrents', 'Une seule ville'], 0, null],
                    ['Quel objectif choisir pour obtenir des messages WhatsApp ?', ['Notoriété', 'Messages', 'Couverture', 'Vues de vidéo'], 1, null],
                    ['Combien de temps laisser une campagne en phase d\'apprentissage ?', ['Quelques heures', 'Environ 50 conversions ou une semaine', 'Une minute', 'Elle n\'existe pas'], 1, 'Modifier la campagne trop tôt relance l\'apprentissage.']]]],
            'GIA' => [
                ['QCM — Environnement informatique', true, -25, [
                    ['Quel raccourci copie une sélection sous Windows ?', ['Ctrl + V', 'Ctrl + C', 'Ctrl + X', 'Ctrl + Z'], 1, null],
                    ['Qu\'est-ce qu\'une extension de fichier ?', ['La taille du fichier', 'Le suffixe qui indique le type (ex. .docx)', 'Le dossier parent', 'Le nom de l\'auteur'], 1, null],
                    ['Où retrouver un fichier supprimé par erreur ?', ['Dans la corbeille', 'Dans le BIOS', 'Dans le presse-papiers', 'Nulle part'], 0, null],
                    ['Quel périphérique est un périphérique d\'entrée ?', ['L\'imprimante', 'L\'écran', 'Le clavier', 'Les haut-parleurs'], 2, null],
                    ['Quelle est la meilleure pratique pour un mot de passe ?', ['Sa date de naissance', 'Le même partout', 'Une phrase longue et unique', '123456'], 2, null]]],
                ['QCM — Google Workspace', false, 0, [
                    ['Quel outil Google sert à créer un questionnaire ?', ['Google Docs', 'Google Forms', 'Google Meet', 'Google Drive'], 1, null],
                    ['Comment partager un fichier Drive en lecture seule ?', ['Rôle « Lecteur »', 'Rôle « Éditeur »', 'Supprimer le fichier', 'L\'envoyer par SMS'], 0, null],
                    ['Dans Google Sheets, quelle formule additionne une plage ?', ['=MOYENNE(A1:A10)', '=SOMME(A1:A10)', '=NB(A1:A10)', '=SI(A1:A10)'], 1, null],
                    ['Où planifier une réunion avec un lien Meet ?', ['Google Calendar', 'Google Slides', 'Google Keep', 'YouTube'], 0, null],
                    ['Que permet l\'historique des versions ?', ['Revenir à une version précédente du document', 'Traduire le document', 'Imprimer plus vite', 'Changer de compte'], 0, null]]]],
        ];
        $quizzes = [];
        foreach ($bank as $code => $list) {
            foreach ($list as [$title, $required, $offset, $questions]) {
                $q = Quiz::create(['formation_id' => $f[$code]->id, 'title' => $title, 'description' => 'Répondez à toutes les questions. Une seule bonne réponse par question.',
                    'duration_min' => 10 + count($questions), 'attempts_allowed' => 2, 'pass_percent' => 60, 'required' => $required, 'published' => true,
                    'opens_at' => now()->addDays($offset)->subDays($offset >= 0 ? 1 : 0), 'closes_at' => $offset >= 0 ? now()->addDays($offset + 7) : null, 'created_by' => $trainer($code)?->id]);
                foreach ($questions as $i => [$text, $opts, $answer, $expl]) {
                    $q->questions()->create(['text' => $text, 'options' => $opts, 'answer' => $answer, 'explanation' => $expl, 'position' => $i]);
                }
                $quizzes[$code][] = $q->load('questions');
            }
        }
        /* Tentatives : les quiz passés (premier de chaque formation) ont été faits par les étudiants */
        foreach (Student::with('formations')->where('status', 'actif')->get() as $k => $s) {
            foreach ($s->formations as $fo) {
                $q = $quizzes[$fo->code][0];
                $good = $s->is($aya) ? ['DG' => 5, 'CM' => 3, 'GIA' => 5][$fo->code] : 2 + ($k + $fo->id) % 4;
                $answers = [];
                foreach ($q->questions as $i => $qq) {
                    $answers[$qq->id] = $i < $good ? $qq->answer : ($qq->answer + 1) % count($qq->options);
                }
                $total = $q->questions->count();
                QuizAttempt::create(['quiz_id' => $q->id, 'student_id' => $s->id, 'answers' => $answers, 'score' => min($good, $total), 'total' => $total,
                    'percent' => (int) round(min($good, $total) / $total * 100), 'started_at' => now()->subDays(14)->subMinutes(12), 'submitted_at' => now()->subDays(14)]);
            }
        }

        /* ---------- RECO 60 ---------- */
        $reco = function (Student $s, string $code, array $data, array $entries) use ($f) {
            $tpl = RecoProject::TEMPLATES[$code];
            $done = $data['done'] ?? 2;
            unset($data['done']);
            $p = RecoProject::create($data + ['student_id' => $s->id, 'formation_id' => $f[$code]->id, 'steps' => collect($tpl)->map(fn ($x, $i) => $x + ['done' => $i < $done])->all()]);
            foreach ($entries as [$days, $action, $result, $hours, $file]) {
                $p->entries()->create(['entry_date' => now()->subDays($days), 'action' => $action, 'result' => $result, 'hours' => $hours,
                    'file_path' => $file ? DemoFile::make($action, 'PDF', 'reco/'.$p->id) : null, 'file_name' => $file]);
            }

            return $p;
        };
        $p = $reco($aya, 'CM', ['name' => 'Bissap Délice — lancement digital', 'sector' => 'Agroalimentaire', 'description' => 'Lancer la présence en ligne d\'une jeune marque de jus de bissap artisanal vendu à Cocody et Bingerville.',
            'objectives' => '1 000 abonnés Instagram, 5 % d\'engagement, 50 commandes WhatsApp en 60 jours.', 'channels' => 'Facebook, Instagram, TikTok, WhatsApp Business', 'started_on' => now()->subDays(36),
            'kpis' => [['l' => 'Abonnés', 'v' => '1 284'], ['l' => 'Portée (30 j)', 'v' => '18,6 k'], ['l' => 'Engagement', 'v' => '6,4 %'], ['l' => 'Commandes WhatsApp', 'v' => '47']], 'done' => 3],
            [[1, 'Live Instagram dégustation', '312 vues, 41 commentaires, 9 commandes dans l\'heure.', 2, 'capture_live.pdf'], [3, 'Campagne Meta Ads 10 000 FCFA', 'CPC 38 FCFA, 260 clics vers WhatsApp.', 3, 'rapport_ads.pdf'],
             [6, 'Partenariat avec une micro-influenceuse', '+180 abonnés en 48 h.', 1.5, null], [12, 'Calendrier éditorial du mois', '24 publications planifiées.', 4, 'calendrier.pdf']]);
        $p->entries()->orderByDesc('entry_date')->skip(1)->first()?->update(['feedback' => 'Très bon CPC. Testez une audience similaire à vos acheteurs.', 'feedback_by' => $trainer('CM')->id, 'feedback_at' => now()->subDays(2)]);
        $reco($aya, 'GIA', ['name' => 'Gestion numérique d\'une boutique de quartier', 'sector' => 'Commerce', 'description' => 'Équiper la boutique de Mme Koffi (Yopougon) : fichier clients, suivi des stocks et factures.',
            'objectives' => 'Fichier stocks à jour, factures automatiques, présentation au commerçant.', 'channels' => 'Excel, Word, Google Sheets', 'started_on' => now()->subDays(29), 'kpis' => [['l' => 'Livrables déposés', 'v' => '3 / 6'], ['l' => 'Clients saisis', 'v' => '126']], 'done' => 1],
            [[2, 'Fichier clients Google Sheets', '126 clients saisis, partagé avec la commerçante.', 5, 'clients.pdf'], [8, 'Modèle de facture Word', 'Validé par la commerçante.', 3, 'facture_modele.pdf']]);
        $others = Student::where('id', '!=', $aya->id)->where('status', 'actif')->with('formations', 'user')->get();
        foreach ($others as $k => $s) {
            foreach ($s->formations->where('has_reco', true) as $fo) {
                if ($k % 3 === 2) {
                    continue;   // quelques étudiants n'ont pas encore lancé leur RECO 60
                }
                $status = $k % 5 === 0 ? 'rendu' : ($k % 7 === 1 ? 'evalue' : 'en_cours');
                $start = now()->subDays($status === 'en_cours' ? 10 + $k * 2 : 62);
                $proj = $reco($s, $fo->code, ['name' => $fo->code === 'CM' ? 'Lancement digital — '.['Maquis Chez Tantie', 'Salon de coiffure Belle Nattes', 'Boutique de pagnes Wax Émotion', 'Pâtisserie Doux Délices'][$k % 4] : 'Bureautique — '.['Cabinet comptable Konan', 'École primaire Les Lauriers', 'Quincaillerie du Carrefour'][$k % 3],
                    'description' => 'Projet professionnel réel mené avec une structure partenaire pendant 60 jours.', 'started_on' => $start, 'status' => $status,
                    'kpis' => [['l' => 'Livrables', 'v' => (2 + $k % 4).' / 6']], 'done' => $status === 'en_cours' ? 1 + $k % 3 : 5,
                    'submitted_at' => $status !== 'en_cours' ? now()->subDays(2) : null,
                    'report_path' => $status !== 'en_cours' ? DemoFile::make('Rapport final RECO 60', 'PDF', 'reco/rapports') : null, 'report_name' => $status !== 'en_cours' ? 'rapport-final-reco60.pdf' : null,
                    'appreciation' => $status === 'evalue' ? 'Très bien' : null, 'evaluation' => $status === 'evalue' ? 'Projet mené avec rigueur, résultats mesurés et bien présentés.' : null,
                    'evaluated_by' => $status === 'evalue' ? $trainer($fo->code)->id : null, 'evaluated_at' => $status === 'evalue' ? now()->subDay() : null],
                    [[2 + $k % 5, 'Point d\'étape avec la structure partenaire', 'Objectifs de la semaine validés.', 2, null], [9 + $k % 4, 'Livrable : première version', 'Remis et commenté.', 4, 'livrable.pdf']]);
            }
        }

        /* ---------- Messagerie ---------- */
        $conv = function (string $subject, array $people, array $msgs, ?array $readBy = null) {
            $c = Conversation::create(['subject' => $subject, 'created_by' => $people[0]->id]);
            $c->participants()->attach(collect($people)->pluck('id')->all());
            foreach ($msgs as [$who, $body, $ago]) {
                $m = $c->messages()->create(['user_id' => $people[$who]->id, 'body' => $body, 'created_at' => now()->subMinutes($ago), 'updated_at' => now()->subMinutes($ago)]);
            }
            $c->update(['last_message_at' => $m->created_at]);
            foreach ($people as $i => $u) {
                $read = in_array($i, $readBy ?? array_keys($people), true);
                $c->participants()->updateExistingPivot($u->id, ['last_read_id' => $read ? $m->id : null, 'last_read_at' => $read ? now() : null]);
            }
        };
        $ayaU = $aya->user;
        $marc = $trainer('DG');
        $awaK = $trainer('CM');
        $finance = User::where('email', 'finance@evc.test')->first();
        $peda = User::where('email', 'pedagogie@evc.test')->first();
        $conv('Pistes de logo — Chez Tantie Rose', [$marc, $ayaU], [[0, 'Bonjour Aya, tes pistes de logo sont intéressantes. Tu peux passer jeudi après la séance pour en parler ?', 2900],
            [1, 'Bonjour M. Yao, oui je serai disponible. Merci !', 2860], [0, 'Parfait. Apporte aussi tes croquis papier, et pense à tester la version noir et blanc.', 95]], [0]);
        $conv('Échéance du solde', [$finance, $ayaU], [[0, 'Bonjour Aya, nous vous rappelons l\'échéance de votre solde. Le paiement Mobile Money est possible : envoyez-nous la preuve ici.', 1500]], [0, 1]);
        $conv('Meta Business Suite pour le RECO 60', [$ayaU, $awaK], [[0, 'Bonjour Madame, je n\'arrive pas à connecter la page Facebook à Meta Business Suite pour le RECO 60.', 4300],
            [1, 'Vérifie que tu es bien administratrice de la page (Paramètres > Accès à la Page). On regarde ensemble jeudi si besoin.', 4200], [0, 'C\'était ça, merci beaucoup !', 4100]]);
        $conv('Replays de la semaine', [$peda, $marc], [[0, 'Bonjour Marc, pensez à publier le replay de l\'atelier logo dans la Web TV après la séance.', 300]], [0]);
    }
}
