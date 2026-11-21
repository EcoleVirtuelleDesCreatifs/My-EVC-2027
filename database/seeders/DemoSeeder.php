<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Candidature;
use App\Models\Certificate;
use App\Models\ContactMessage;
use App\Models\Formation;
use App\Models\NewsletterSubscriber;
use App\Models\Payment;
use App\Models\Presence;
use App\Models\Project;
use App\Models\Promotion;
use App\Models\Seance;
use App\Models\Student;
use App\Models\Trainer;
use App\Models\User;
use App\Notifications\Avis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Jeu de démonstration réaliste. Mot de passe de tous les comptes : « password ».
 *   admin@evc.test (Super Administrateur) · finance@evc.test (Responsable financier)
 *   marc.yao@evc.test (formateur DG) · awa.kone@evc.test (formatrice CM) · serge.ble@evc.test (formateur GIA)
 *   aya.kouassi@evc.test (étudiante DG + CM + GIA, matricule EVC-26-0142)
 */
class DemoSeeder extends Seeder
{
    private array $f;

    private int $seed = 7;

    public function run(): void
    {
        if (User::where('email', 'admin@evc.test')->exists()) {
            return;
        }
        mt_srand($this->seed);
        $this->f = Formation::all()->keyBy('code')->all();
        $promo = Promotion::where('status', 'en_cours')->first();

        /* ---------- Équipe ---------- */
        $this->user('Didier Koné', 'admin@evc.test', 'Super Administrateur');
        $finance = $this->user('Awa Bamba', 'finance@evc.test', 'Responsable financier');
        $this->user('Sophie Amon', 'pedagogie@evc.test', 'Responsable pédagogique');
        $trainers = [
            'DG' => $this->user('Marc Yao', 'marc.yao@evc.test', 'formateur'),
            'CM' => $this->user('Awa Koné', 'awa.kone@evc.test', 'formateur'),
            'GIA' => $this->user('Serge Blé', 'serge.ble@evc.test', 'formateur'),
        ];
        foreach ($trainers as $code => $u) {
            Trainer::create(['user_id' => $u->id, 'formation_id' => $this->f[$code]->id, 'title' => 'Formateur principal']);
        }

        /* ---------- Étudiants ---------- */
        $people = [
            ['Aya Kouassi', ['DG', 'CM', 'GIA'], 'EVC-26-0142'], ['Kevin Ahoua', ['DG']], ['Nadia Touré', ['DG', 'CM']], ['Ousmane Cissé', ['GIA']],
            ['Fanta Koné', ['DG']], ['Hamed Fofana', ['CM']], ['Mariam Diallo', ['CM', 'GIA']], ['Jean-Marc Kouadio', ['DG']],
            ['Prisca Gnagne', ['GIA']], ['Ibrahim Sanogo', ['CM']], ['Christelle N\'Guessan', ['DG', 'GIA']], ['Yao Konan', ['GIA']],
            ['Aminata Bamba', ['CM']], ['Serge Ouattara', ['DG']], ['Grâce Assi', ['DG', 'CM']], ['Moussa Traoré', ['GIA']],
            ['Laetitia Brou', ['CM']], ['Abdoul Koné', ['DG']],
        ];
        $students = [];
        foreach ($people as $i => $p) {
            $u = User::create(['name' => $p[0], 'email' => $this->mail($p[0]), 'phone' => '+225 07 '.sprintf('%02d %02d %02d %02d', mt_rand(10, 99), mt_rand(10, 99), mt_rand(10, 99), mt_rand(10, 99)), 'password' => 'password', 'last_login_at' => now()->subHours(mt_rand(1, 96))]);
            $u->assignRole('etudiant');
            $s = Student::create(['user_id' => $u->id, 'matricule' => $p[2] ?? sprintf('EVC-26-%04d', 200 + $i * 7), 'promotion_id' => $promo->id,
                'country' => 'Côte d\'Ivoire', 'city' => ['Cocody', 'Yopougon', 'Abobo', 'Plateau', 'Marcory', 'Treichville', 'Bingerville'][$i % 7],
                'level' => ['Débutant', 'Intermédiaire', 'Avancé'][$i % 3], 'status' => $i === 9 ? 'suspendu' : 'actif']);
            foreach ($p[1] as $code) {
                $f = $this->f[$code];
                $s->enrollments()->create(['formation_id' => $f->id, 'promotion_id' => $promo->id, 'mode' => $i % 3 ? 'En ligne' : 'Présentiel']);
                $s->schedules()->create(['formation_id' => $f->id, 'label' => 'Acompte '.$code, 'amount' => $f->deposit, 'due_on' => $promo->starts_on]);
                $s->schedules()->create(['formation_id' => $f->id, 'label' => 'Solde '.$code, 'amount' => $f->balance(), 'due_on' => $promo->starts_on->copy()->addDays($f->balance_after_days)]);
            }
            $students[] = $s;
        }
        $aya = $students[0];

        /* ---------- Paiements ---------- */
        foreach ($students as $i => $s) {
            $s->load('schedules');
            $due = $s->schedules->sum('amount');
            $deposits = $s->schedules->filter(fn ($x) => str_starts_with($x->label, 'Acompte'))->sum('amount');
            $paid = match (true) { $i === 0 => $deposits + 40000, $i % 4 === 0 => $due, $i === 9 => (int) ($deposits / 2), default => $deposits };
            $parts = $paid > 60000 ? [(int) (round($paid * 0.6 / 5000) * 5000), $paid - (int) (round($paid * 0.6 / 5000) * 5000)] : [$paid];
            foreach ($parts as $k => $amount) {
                Payment::create(['receipt_number' => Payment::nextReceiptNumber(), 'student_id' => $s->id, 'amount' => $amount,
                    'method' => Payment::METHODS[($i + $k) % 5], 'transaction_ref' => $k ? null : 'MP'.mt_rand(100000, 999999),
                    'paid_on' => $k ? now()->subDays(1 + $i % 12) : $promo->starts_on->copy()->addDays($i % 5), 'recorded_by' => $finance->id]);
            }
        }

        /* ---------- Séances (planning de la promotion) et présences ---------- */
        $rythme = ['DG' => [[1, 18], [3, 18]], 'CM' => [[2, 17]], 'GIA' => [[4, 17], [6, 10]]];
        foreach ($this->f as $code => $f) {
            $chapters = collect($f->modules)->flatMap(fn ($m) => collect($m['ch'])->map(fn ($c) => [$m['t'], $c]))->values();
            $n = 0;
            for ($week = 0; $week < 17 && $n < $chapters->count(); $week++) {
                foreach ($rythme[$code] as [$dow, $hour]) {
                    if ($n >= $chapters->count()) {
                        break;
                    }
                    $start = $promo->starts_on->copy()->startOfWeek()->addWeeks($week)->addDays($dow - 1)->setTime($hour, 0);
                    if ($start->lt($promo->starts_on)) {
                        continue;
                    }
                    [$module, $title] = $chapters[$n++];
                    Seance::create(['formation_id' => $f->id, 'promotion_id' => $promo->id, 'trainer_id' => $trainers[$code]->id,
                        'title' => $title, 'module' => $module, 'starts_at' => $start, 'ends_at' => $start->copy()->addHours(2),
                        'mode' => $n % 4 === 0 ? 'presentiel' : 'virtuel', 'meet_url' => 'https://meet.google.com/evc-'.strtolower($code).'-'.str_pad((string) $n, 3, '0', STR_PAD_LEFT), 'room' => $n % 4 === 0 ? 'Salle 2 — Cocody' : null]);
                }
            }
        }
        /* Une séance DG en cours aujourd'hui : pour essayer le pointage et le bouton « Rejoindre » */
        Seance::create(['formation_id' => $this->f['DG']->id, 'promotion_id' => $promo->id, 'trainer_id' => $trainers['DG']->id,
            'title' => 'Atelier : présentation client d\'un logo', 'module' => 'Création de logos', 'starts_at' => now()->subMinutes(15)->second(0),
            'ends_at' => now()->addHours(2)->second(0), 'mode' => 'virtuel', 'meet_url' => 'https://meet.google.com/evc-dg-live']);

        foreach (Seance::where('ends_at', '<', now())->get() as $se) {
            foreach ($se->expectedStudents()->get() as $s) {
                $r = mt_rand(1, 100);
                Presence::create(['seance_id' => $se->id, 'student_id' => $s->id, 'status' => $r <= 86 ? 'present' : ($r <= 93 ? 'retard' : 'absent'),
                    'checked_at' => $r <= 93 ? $se->starts_at->copy()->addMinutes($r <= 86 ? mt_rand(-10, 4) : mt_rand(10, 25)) : null]);
            }
        }

        /* ---------- Projets : catalogue et attributions ---------- */
        $catalogue = [
            'DG' => [['Logo — Maquis « Chez Tantie Rose »', 'Identité visuelle', 'Créer le logo d\'un maquis d\'Abidjan : 3 pistes, puis la version finale en couleur et en noir et blanc.'],
                ['Affiche — Festival des Grillades', 'Affiche', 'Affiche A2 pour un festival culinaire : hiérarchie de l\'information, photos, lisibilité à 3 mètres.'],
                ['Mise en page — magazine 8 pages', 'Mise en page', 'Mettre en page 8 pages d\'un magazine culturel dans InDesign avec gabarits et styles.'],
                ['Charte graphique — Café Ivoire', 'Identité visuelle', 'Charte complète : logo, couleurs, typographies, déclinaisons (carte de visite, sachet, réseaux sociaux).'],
                ['Packaging — jus de bissap', 'Packaging', 'Étiquette et gabarit de découpe d\'une bouteille de jus de bissap, avec maquette 3D.']],
            'CM' => [['Audit des réseaux sociaux d\'une marque', 'Stratégie', 'Analyser la présence en ligne d\'une marque ivoirienne : forces, faiblesses, recommandations.'],
                ['Calendrier éditorial d\'un mois', 'Contenus', 'Construire un calendrier de 30 jours : réseaux, formats, objectifs et visuels.'],
                ['Campagne Meta Ads', 'Publicité', 'Paramétrer une campagne fictive : cible, budget, visuels, textes et indicateurs.'],
                ['Reporting mensuel', 'Reporting', 'Restituer les résultats d\'un mois dans une présentation claire.']],
            'GIA' => [['Rapport Word avec sommaire automatique', 'Word', 'Rédiger un rapport de 10 pages : styles, sommaire, numérotation, en-têtes.'],
                ['Tableau de suivi des stocks', 'Excel', 'Tableau Excel avec formules, mise en forme conditionnelle et graphique.'],
                ['Présentation d\'entreprise', 'PowerPoint', 'Présentation de 10 diapositives pour une PME, avec transitions sobres.'],
                ['Formulaire d\'inscription Google Forms', 'Google', 'Créer un formulaire et exploiter les réponses dans Google Sheets.']],
        ];
        foreach ($catalogue as $code => $list) {
            foreach ($list as [$t, $type, $brief]) {
                Project::create(['formation_id' => $this->f[$code]->id, 'title' => $t, 'type' => $type, 'brief' => $brief]);
            }
        }
        $statuses = ['valide', 'valide', 'soumis', 'encours', 'afaire'];
        foreach ($students as $i => $s) {
            foreach ($s->formations as $f) {
                $projects = Project::where('formation_id', $f->id)->orderBy('id')->get();
                $count = 1 + ($i + $f->id) % 4;
                foreach ($projects->take($count) as $k => $p) {
                    $st = $k < $count - 1 ? 'valide' : $statuses[($i + $k + $f->id) % 5];
                    $this->assign($p, $s, $st, $trainers[$f->code], $k);
                }
            }
        }
        /* Aya : un projet de chaque statut, pour la démonstration */
        $aya->assignments()->delete();
        $map = ['DG' => ['valide', 'soumis', 'reprendre', 'afaire'], 'CM' => ['valide', 'encours'], 'GIA' => ['valide', 'valide', 'encours']];
        foreach ($map as $code => $sts) {
            foreach (Project::where('formation_id', $this->f[$code]->id)->orderBy('id')->get()->take(count($sts)) as $k => $p) {
                $this->assign($p, $aya, $sts[$k], $trainers[$code], $k, $code === 'CM' && $sts[$k] === 'encours' ? -1 : null);
            }
        }

        /* ---------- Bibliothèque : quelques téléchargements ---------- */
        foreach (\App\Models\Resource::where('essential', true)->get() as $r) {
            if (! $r->formation_id || $aya->formations->contains('id', $r->formation_id)) {
                $aya->downloads()->firstOrCreate(['resource_id' => $r->id]);
            }
        }

        /* ---------- Certificat déjà délivré (promotion précédente) ---------- */
        $grace = User::create(['name' => 'Grace Mensah', 'email' => 'grace.mensah@evc.test', 'password' => 'password']);
        $grace->assignRole('etudiant');
        $gs = Student::create(['user_id' => $grace->id, 'matricule' => 'EVC-25-0451', 'status' => 'certifie', 'country' => 'Togo', 'city' => 'Lomé']);
        $gs->enrollments()->create(['formation_id' => $this->f['DG']->id, 'status' => 'terminee']);
        Certificate::create(['code' => 'EVC-CERT-2026-0451', 'student_id' => $gs->id, 'formation_id' => $this->f['DG']->id, 'appreciation' => 'Très bien', 'issued_on' => now()->subMonths(3)]);

        /* ---------- Candidatures ---------- */
        $cands = [
            ['Daniel', 'Atta', ['DG'], 'recue', 0], ['Khadija', 'Sow', ['GIA', 'CM'], 'eligible', 1], ['Arnaud', 'Bédié', ['GIA', 'DG', 'CM'], 'test', 2],
            ['Rose', 'Kacou', ['CM'], 'paiement', 3], ['Paul', 'Ehouman', ['GIA'], 'recue', 0], ['Inès', 'Yapi', ['DG'], 'refusee', 5],
        ];
        foreach ($cands as $k => [$fn, $ln, $fs, $stage, $days]) {
            Candidature::create(['reference' => Candidature::nextReference(), 'first_name' => $fn, 'last_name' => $ln, 'email' => $this->mail("$fn $ln"),
                'phone' => '+225 05 '.sprintf('%02d %02d %02d %02d', mt_rand(10, 99), mt_rand(10, 99), mt_rand(10, 99), mt_rand(10, 99)), 'country' => 'Côte d\'Ivoire',
                'city' => 'Abidjan', 'formations' => $fs, 'mode' => $k % 2 ? 'En ligne' : 'Présentiel (Abidjan)', 'stage' => $stage, 'test_score' => in_array($stage, ['test', 'paiement']) ? 14 + $k : null,
                'answers' => ['Situation' => ['Étudiant(e)'], 'Niveau d\'études' => ['Bac + 2'], 'Ordinateur' => ['Oui, personnel'], 'Source' => ['Facebook'],
                    'Motivation' => ['Je veux apprendre un métier du numérique et travailler en freelance.']], 'created_at' => now()->subDays($days)->subHours($k)]);
        }

        /* ---------- Messages du site et newsletter ---------- */
        ContactMessage::create(['source' => 'contact', 'name' => 'Brice Kouamé', 'email' => 'brice.kouame@exemple.ci', 'phone' => '+225 07 58 21 44 10',
            'fields' => ['Nom' => 'Brice Kouamé', 'Sujet' => 'Formation du soir', 'Message' => 'Proposez-vous des cours le soir pour la bureautique ?'], 'created_at' => now()->subDay()]);
        ContactMessage::create(['source' => 'partenariat', 'name' => 'Agence Sankofa', 'email' => 'rh@sankofa.ci',
            'fields' => ['Structure' => 'Agence Sankofa', 'Besoin' => 'Stagiaires graphistes', 'Message' => 'Nous recherchons 2 stagiaires DG pour janvier.'], 'created_at' => now()->subDays(3)]);
        foreach (['lecteur@exemple.ci', 'awa.d@exemple.ci', 'koffi.n@exemple.ci'] as $e) {
            NewsletterSubscriber::create(['email' => $e]);
        }

        /* ---------- Notifications d'Aya ---------- */
        $aya->user->notify(new Avis('Bienvenue sur MY EVC', 'Toute l\'équipe de l\'EVC vous souhaite une excellente formation. Pensez à installer vos logiciels depuis la bibliothèque.', route('etudiant.bibliotheque'), 'fa-hand', 'lav'));
        $aya->user->notify(new Avis('Correction disponible', '« Mise en page — magazine 8 pages » est à reprendre : consultez le commentaire de votre formateur.', route('etudiant.projets'), 'fa-pen-nib', 'warn'));
    }

    private function user(string $name, string $email, string $role): User
    {
        $u = User::create(['name' => $name, 'email' => $email, 'password' => 'password', 'phone' => '+225 07 '.mt_rand(10, 99).' 00 00 '.mt_rand(10, 99)]);
        $u->assignRole($role);

        return $u;
    }

    private function mail(string $name): string
    {
        return strtolower(preg_replace('/[^a-z.]+/i', '', str_replace(' ', '.', iconv('UTF-8', 'ASCII//TRANSLIT', $name)))).'@evc.test';
    }

    private function assign(Project $p, Student $s, string $status, User $trainer, int $k, ?int $dueOffset = null): void
    {
        $base = now()->subDays(40 - $k * 9);
        $due = $dueOffset !== null ? now()->addDays($dueOffset) : match ($status) {
            'valide' => $base->copy()->addDays(10), 'soumis' => now()->subDays(1), 'reprendre' => now()->addDays(2),
            'encours' => now()->addDays(4 + $k), default => now()->addDays(6 + $k),
        };
        $a = Assignment::create(['project_id' => $p->id, 'student_id' => $s->id, 'assigned_by' => $trainer->id, 'due_on' => $due, 'status' => $status,
            'appreciation' => $status === 'valide' ? Assignment::APPRECIATIONS[($s->id + $k) % 3] : null,
            'started_at' => $status !== 'afaire' ? $base->copy()->addDay() : null,
            'submitted_at' => in_array($status, ['soumis', 'valide', 'reprendre']) ? $base->copy()->addDays(8) : null,
            'validated_at' => $status === 'valide' ? $base->copy()->addDays(9) : null, 'created_at' => $base, 'updated_at' => $base]);
        $a->log('attribution', 'Projet attribué. Formateur référent : '.$trainer->name.'.', $trainer->id);
        if ($a->submitted_at) {
            $sub = $a->submissions()->create(['message' => 'Voici mon travail.', 'links' => [], 'created_at' => $a->submitted_at]);
            $sub->files()->create(['path' => 'demo/'.str($p->title)->slug().'.pdf', 'original_name' => str($p->title)->slug().'.pdf', 'size' => 1048576]);
            $a->log('depot', 'Travail déposé.', $s->user_id);
        }
        if ($status === 'reprendre') {
            $a->log('reprise', 'Bonne base. Revoyez la grille : les marges intérieures ne sont pas régulières, et la page 5 manque de respiration.', $trainer->id);
        }
        if ($status === 'valide') {
            $a->log('validation', 'Projet validé — appréciation : '.$a->appreciation.'. Bravo !', $trainer->id);
        }
    }
}
