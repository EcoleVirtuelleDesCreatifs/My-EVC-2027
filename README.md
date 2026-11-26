# EVC — Site vitrine et plateforme MY EVC (Laravel)

Application complète de l'École Virtuelle des Créatifs :

- **le site vitrine** : 30 pages, actualités gérées depuis l'administration, préinscription en ligne avec
  pièces jointes, suivi de candidature, vérification des certificats, formulaires de contact et newsletter ;
- **MY EVC**, la plateforme de l'école, avec trois espaces :
  - **étudiant** : accueil avec l'action du moment, cours et pointage de présence, projets (démarrer, rendre,
    échanger avec le formateur), bibliothèque numérique, paiements et reçus PDF, certificat ;
  - **formateur** : séances et appel, corrections, bibliothèque de sa formation ;
  - **administration** : tableau de bord, préinscriptions jusqu'à l'activation du compte, étudiants,
    formations et sessions, séances, projets et disponibilité, finances et relances, bibliothèque, actualités,
    messages du site, certificats, utilisateurs et rôles, journal d'audit, paramètres.

Technologies : **Laravel 12**, **Livewire 3** (écrans dynamiques sans rechargement), Blade, Bootstrap 5,
spatie/laravel-permission (rôles), DomPDF (reçus, certificats, attestations). Le design reprend à l'identique
la maquette, conservée dans [`maquette/`](maquette/).

## Installer en local

Prérequis : PHP 8.2 ou plus (extensions `pdo_sqlite` ou `pdo_mysql`, `mbstring`, `gd`, `zip`, `intl`), Composer.

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite        # ou configurez MySQL dans .env
php artisan migrate --seed            # tables + rôles + formations + liens officiels
php artisan evc:admin
php artisan storage:link
composer run serve:local               # http://127.0.0.1:8000
```

### Première connexion

Aucun compte, étudiant, projet, paiement, séance, article ni fichier factice n'est créé par l'installation
standard. `php artisan evc:admin` permet de saisir dans votre terminal les coordonnées et le mot de passe
confidentiel du premier administrateur. L'équipe se crée ensuite dans **Utilisateurs et rôles**.

La commande `composer run serve:local` démarre le serveur avec 500 Mo par fichier et 512 Mo par requête.
Les formulaires gardent leurs propres limites, notamment 50 Mo pour la bibliothèque. N'utilisez pas le
serveur PHP intégré comme serveur de production.

Les e-mails sont journalisés uniquement si `MAIL_MAILER=log`. Configurez un transport SMTP pour les
livrer réellement. Ne versionnez jamais les identifiants, mots de passe ou le fichier `.env`.

## Mettre en ligne

1. Serveur : PHP 8.2+, MySQL 8, HTTPS. La racine web doit pointer sur le dossier **`public/`**.
2. Fichier `.env` :
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://www.ecolevirtuelledescreatifs.com
   DB_CONNECTION=mysql
   DB_HOST=…  DB_DATABASE=…  DB_USERNAME=…  DB_PASSWORD=…
   MAIL_MAILER=smtp
   MAIL_HOST=…  MAIL_PORT=587  MAIL_USERNAME=…  MAIL_PASSWORD=…
   MAIL_FROM_ADDRESS=info@ecolevirtuelledescreatifs.com
   ```
3. Installation :
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan key:generate
   php artisan migrate --seed --force     # en production : rôles et contenus uniquement, pas de démonstration
   php artisan evc:admin                  # crée le compte Super Administrateur
   php artisan storage:link
   php artisan config:cache && php artisan route:cache && php artisan view:cache
   ```
4. Tâche planifiée (relances de paiement et rappels de cours, chaque jour à 8 h 30) :
   ```
   * * * * * cd /chemin/vers/my-evc && php artisan schedule:run >> /dev/null 2>&1
   ```
5. Envoi de fichiers : dans `php.ini`, `upload_max_filesize = 500M`, `post_max_size = 512M` et
   `max_execution_time = 300` (vidéos de la Web TV jusqu'à 500 Mo ; projets et bibliothèque jusqu'à 50 Mo).
   Si l'hébergeur limite les envois, publiez les replays en lien YouTube (non répertorié) ou Vimeo.
6. Les dossiers `storage/` et `bootstrap/cache/` doivent être accessibles en écriture.

Les commandes de génération de clé ci-dessus sont réservées à une première installation. Conservez
la clé `APP_KEY` d'une instance existante afin de ne pas invalider les liens signés et les données chiffrées.
Configurez aussi `EVC_REGISTRE_URL` et `EVC_REGISTRE_TOKEN` sur le serveur, sans les versionner : les
certificats officiels sont transmis au registre, validés humainement puis publiés avant téléchargement.
Exécutez `php artisan evc:audit-production` avant publication et renseignez les informations juridiques
manquantes avec la direction. Cet audit ne remplace pas une validation juridique ni un test réel de livraison d'e-mail.

### Adresse publique, mesure d'audience et performances du site

- `APP_URL` doit contenir le domaine public en HTTPS : le sitemap, les liens canoniques, le JSON-LD et
  l'image de partage (`og:image`) sont construits à partir de cette valeur, jamais depuis l'hôte de la requête.
- `GA4_MEASUREMENT_ID=G-…` et `META_PIXEL_ID=…` activent Google Analytics 4 et Meta Pixel. Laissés vides,
  aucun outil ni bandeau cookies n'est affiché. Une fois renseignés, le bandeau s'affiche et les outils ne
  sont chargés qu'après consentement. Événements envoyés : `generate_lead` (préinscription), `whatsapp_click`
  (avec l'emplacement du lien), `contact_form_submit` et `newsletter_signup` ; côté Meta : `Lead`, `Contact`, `Subscribe`.
- Indépendamment de ces outils, la provenance (`utm_source`, `utm_medium`, `utm_campaign`, identifiants de clic
  Google/Meta/TikTok, site référent, page d'arrivée) est enregistrée avec chaque préinscription et affichée dans
  **Candidatures**. Exemple de lien de campagne :
  `https://www.ecolevirtuelledescreatifs.com/formation-design?utm_source=facebook&utm_medium=paid_social&utm_campaign=rentree-2027`
- Images : après tout ajout ou remplacement dans `public/assets/site/img`, lancer `php artisan evc:images`
  (variantes WebP 480/960/1440 px et dimensions). Les balises `<img>` du site sont alors servies en WebP avec
  `srcset`, `width` et `height`.
- CSS : le site public charge `public/assets/site/css/bootstrap.site.css`, version allégée de Bootstrap.
  Après modification des gabarits du site, la régénérer avec
  `npx --yes purgecss@8.0.0 --config build/purgecss.site.cjs`. Le portail MY EVC garde la feuille complète.

Ensuite, dans l'administration : **Paramètres** (e-mail, numéros WhatsApp, directeur, RCCM),
**Formations et sessions** (tarifs, dates), puis **Utilisateurs et rôles** (équipe et formateurs).

## Organisation du code

```
app/
  Http/Controllers/       Site (pages, actualités, formulaires), connexion, fichiers protégés, PDF
  Livewire/               Écrans MY EVC : Etudiant/, Formateur/, Admin/, Shared/ (+ Concerns/ partagés)
  Models/                 Formation, Promotion, Student, Candidature, Seance, Assignment, Payment, Resource…
  Services/               AdmissionService, PaymentService, ProjectService, PresenceService, AvailabilityService,
                          QuizService, MessagingService
  Livewire/Gestion/       écrans communs formateur + administration : Web TV, RECO 60, Quiz
  Notifications/          Avis (cloche + e-mail), CandidatureRecue, CompteActive
  Console/Commands/       evc:admin, evc:relances
database/
  migrations/  seeders/   (data/ : formations, actualités et bibliothèque issues de la maquette)
resources/views/
  layouts/                site, myevc, auth
  site/                   pages du site, actualités, suivi, vérification
  livewire/               écrans MY EVC
  components/             composants d'interface (indicateur, panneau, livre, fenêtre…)
  pdf/                    reçu, certificat, attestation
public/assets/            CSS, polices, images et scripts de la maquette (site et MY EVC)
tests/Feature/            tests des parcours (site, connexion et droits, admission, projets, paiements…)
maquette/                 maquette statique d'origine (référence visuelle)
docs/                     plan, chartes, parcours
```

Règles métier principales :

- **Paiements** : chaque inscription crée un échéancier (acompte, puis solde N jours après le début).
  Les versements sont imputés aux échéances les plus anciennes ; chaque versement produit un reçu PDF numéroté.
- **Présence** : le pointage est ouvert de 30 minutes avant le début jusqu'à la fin de la séance ;
  au-delà de 10 minutes, la présence est notée « en retard ».
- **Certificat** : formation soldée, tous les projets validés, assiduité d'au moins 80 %, séances terminées,
  quiz obligatoires réussis et, pour CM et GIA, RECO 60 évalué ; le jury délivre le certificat, vérifiable
  sur le site avec sa référence.
- **Web TV** : tutoriels, replays liés aux séances et masterclass, en fichier MP4 (lecture protégée, réservée
  aux inscrits de la formation) ou en lien YouTube / Vimeo. La progression est enregistrée ; une vidéo est
  « vue » à 90 %, et l'étudiant reprend là où il s'était arrêté.
- **RECO 60** : projet professionnel réel de 60 jours (CM et GIA, activable par formation). L'étudiant suit
  ses étapes, ses indicateurs et un journal d'activités avec preuves ; le formateur commente, puis évalue
  le rapport final (ou demande des compléments).
- **Quiz (QCM)** : chronométrés, corrigés automatiquement, nombre de tentatives et seuil de réussite
  réglables, ouverture et clôture facultatives. Les réponses sont enregistrées à chaque clic ; à la fin
  du temps (30 s de tolérance), la tentative est corrigée avec les réponses déjà données. La bonne réponse
  n'est jamais envoyée au navigateur avant la correction.
- **Messagerie** : un étudiant écrit aux formateurs de ses formations et à l'équipe ; un formateur à ses
  étudiants, aux formateurs et à l'équipe ; l'équipe à tout le monde. Pièces jointes protégées, compteur de
  non-lus dans le menu, une seule notification tant que la conversation n'a pas été relue.
- **Disponibilité** : un étudiant est « libre » quand il n'a plus de projet à faire ; l'administration
  peut donner un projet à tous les libres en un clic.

## Tests

```bash
composer test
node --test tests/*.test.cjs
php artisan evc:audit-production
```

L'audit est en lecture seule et renvoie un code non nul si des validations restent nécessaires.
Il n'affiche pas de secrets et ne supprime aucune donnée. Les contrôles de configuration doivent être
exécutés sur le serveur cible ; un environnement local HTTP avec débogage n'est pas un environnement
prêt à être publié. Les tests utilisent SQLite en mémoire, pas la base MySQL de travail.

Les tests couvrent les métadonnées SEO (titres, JSON-LD et sitemap), les pages du site, la préinscription, la connexion et les droits de chaque rôle,
l'activation d'un candidat, les paiements, le cycle complet d'un projet, le pointage, la bibliothèque,
la certification, la Web TV, les quiz (correction, tentatives, temps écoulé), le RECO 60 et la messagerie. Ils sont lancés automatiquement sur GitHub à chaque envoi.

## Phase 2

Livrés : **Web TV**, **RECO 60**, **Quiz (QCM)** et **messagerie**, dans les trois espaces.

À venir : Studio Creative, créneaux et rendez-vous, stages et alumni, support, paiement en ligne
(CinetPay ou autre agrégateur), communiqués multicanal. Voir
[`docs/PLAN-LARAVEL.md`](docs/PLAN-LARAVEL.md).
