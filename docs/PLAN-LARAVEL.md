# Plan Laravel — Phase 1 (cœur)

Ce document sert de cahier des charges technique pour transformer la maquette (site vitrine + MY EVC)
en application Laravel fonctionnelle. Il est écrit pour être repris tel quel dans une nouvelle session de travail.

## 1. Décisions validées

| Sujet | Choix |
|---|---|
| Périmètre | **Phase 1 cœur** (détail § 4) ; les autres modules en phases suivantes (§ 9) |
| Interface | **Laravel + Livewire 3 + Blade**, Alpine.js (fourni par Livewire) |
| Design | Reprise **à l'identique** du CSS de la maquette : `assets/site/css/site.css` (site) et `assets/app/css/*.css` (MY EVC). Pas de Tailwind. |
| Paiements | **Saisie manuelle** par l'administration, échéancier, reçus PDF, relances. Paiement en ligne plus tard. |

## 2. Pile technique

| Élément | Version / paquet | Rôle |
|---|---|---|
| PHP | 8.3 (8.2 minimum) | |
| Laravel | 12 | Framework |
| Livewire | 3 | Écrans dynamiques (formulaires en étapes, filtres, tableaux, modales) |
| Base de données | MySQL 8 en production, SQLite pour le développement et les tests | |
| Authentification | `laravel/fortify` (sans interface) + vues Blade de la maquette | Connexion, mot de passe oublié, activation de compte |
| Rôles et droits | `spatie/laravel-permission` | Étudiant, formateur, 10 rôles d'administration de la maquette |
| PDF | `barryvdh/laravel-dompdf` | Reçus, attestation d'inscription |
| Journal d'audit | `spatie/laravel-activitylog` | Équivalent du « Journal d'audit » de la maquette |
| Front | Vite, Bootstrap 5.3 (npm), Font Awesome 6 (npm), polices locales de la maquette | |
| Tests | Pest | Tests de fonctionnalités par parcours |
| Files d'attente | driver `database` (ou `sync` en hébergement mutualisé) | E-mails, notifications |

## 3. Organisation du dépôt

- Nouvelle branche **`laravel`** créée depuis `evc-site-complete`.
- Projet Laravel **à la racine** ; la maquette statique est déplacée dans **`maquette/`** pour référence visuelle.
- CSS et polices de la maquette copiés dans `resources/css/site/` et `resources/css/app/`, compilés par Vite.
- Gabarits du site (`gabarits/entete.html`, `pied.html`) → composants Blade `layouts/site.blade.php`, `partials/site/*`.
- Écrans MY EVC (rendus aujourd'hui en JavaScript) → vues Blade + composants Livewire, même balisage et mêmes classes CSS.

```
app/
  Models/            User, Formation, Session, Enrollment, Student, Candidature, Seance, Presence,
                     Project, Assignment, Submission, PaymentSchedule, Payment, Resource, Certificate, News…
  Livewire/
    Site/            Preinscription (étapes), SuivreCandidature, VerifierCertificat, Contact, Newsletter
    Etudiant/        Accueil, Cours, Projets, ProjetDetail, Bibliotheque, Paiements, Certificat, Profil
    Formateur/       Accueil, Seances, Appel, Corrections, Bibliotheque
    Admin/           Dashboard, Etudiants, FicheEtudiant, Candidatures, Formations, Sessions, Seances,
                     Projets, Disponibilite, Finances, Bibliotheque, Actualites, Utilisateurs, Audit, Parametres
  Http/Controllers/Site/   Pages statiques du site (Blade)
  Policies/          Une policy par modèle sensible
  Notifications/     CandidatureRecue, CompteActive, ProjetAttribue, PaiementEnregistre, RessourcePubliee…
database/
  migrations/  seeders/ (reprise des données de démonstration de assets/app/js/noyau/data.js)
resources/views/
  layouts/ (site, myevc)  site/ (30 pages)  myevc/ (etudiant, formateur, admin, auth)  pdf/
routes/web.php
tests/Feature/
```

## 4. Périmètre de la phase 1

### A. Site vitrine (public)
- Les 30 pages de la maquette en Blade, avec en-tête et pied communs.
- **Dynamiques** : formations (tarifs, durées, modules depuis la base), calendrier des sessions, actualités
  (liste + article, gérées dans l'administration), sitemap généré.
- **Préinscription** en étapes (Livewire) : enregistrement de la candidature, pièces jointes, e-mail de
  confirmation avec référence ; le bouton WhatsApp « Envoyer au conseiller » est conservé.
- **Suivre ma candidature** (référence + e-mail) et **Vérifier un certificat** (code).
- Formulaire de contact (enregistré + e-mail), inscription à la newsletter.

### B. Connexion et rôles
- Connexion unique `/myevc/connexion`, redirection selon le rôle.
- Mot de passe oublié par e-mail ; **lien d'activation** envoyé à l'étudiant activé.
- Rôles : `etudiant`, `formateur`, et les rôles d'administration de la maquette (Super Administrateur,
  Direction Générale, Responsable pédagogique, administratif, financier, certification, communication,
  Formateur principal / adjoint, Support technique) avec la matrice de droits de `PERM_DEFAULT` (core.js).

### C. Admission
- Pipeline : reçue → profil éligible → test → 1er versement → activée (étapes de la maquette).
- Écran d'administration des candidatures (filtres, dossier complet, changement d'étape, notes).
- Activation : création du compte étudiant, matricule `EVC-AA-NNNN`, inscriptions aux formations, session.

### D. Espace étudiant
| Écran | Contenu |
|---|---|
| Accueil | Une action principale (ordre de priorité de `eAction`, epure.js), cartes Prochain cours / Mon projet / Paiements, progression |
| Mes cours | Séances à venir et passées, lien Google Meet, pointage de présence |
| Mes projets | Projets attribués : démarrer, déposer (fichiers, liens), statut, historique, commentaires |
| Bibliothèque | Étagères par rayon, fiche du livre, téléchargement par lien signé, « Mon étagère » |
| Paiements | Échéancier, versements, reste à payer, reçus PDF |
| Certificat | Conditions, état d'éligibilité, téléchargement une fois délivré |
| Notifications, Profil | Cloche, préférences, photo, mot de passe |

Sélecteur de formation (Toutes / DG / CM / GIA) pour les étudiants inscrits à plusieurs formations.

### E. Espace formateur (essentiel)
Tableau de bord, séances et appel (présences), corrections (valider / à reprendre avec commentaire),
bibliothèque de sa formation.

### F. Administration
Tableau de bord (chiffres clés), étudiants (liste, fiche), candidatures, formations / sessions / séances,
projets (catalogue, attribution individuelle et en lot, validation) et **disponibilité** (libre / reçu / en cours / en retard),
finances (enregistrer un versement, échéanciers, relances, reçus), bibliothèque, actualités,
utilisateurs et rôles, journal d'audit, paramètres (coordonnées, numéros WhatsApp, textes légaux).

## 5. Modèle de données (tables principales)

```
users(id, name, email, phone, password, photo, status, last_login_at)          + rôles (spatie)
formations(code[DG|CM|GIA], name, pitch, price, deposit, months, pay_rule, modules JSON, active)
sessions(id, name, starts_on, ends_on, level, status)                            -- promotions
students(id, user_id, matricule, birth_date, city, country, session_id, status)
enrollments(student_id, formation_code, session_id, status, progress)
trainers(id, user_id, formation_code, title)
candidatures(id, reference, identity JSON, profile JSON, formations JSON, mode, stage, test_score,
             notes, student_id NULL, created_at)          + candidature_files(candidature_id, kind, path)
seances(id, formation_code, session_id, title, starts_at, ends_at, mode[virtuel|presentiel], meet_url, trainer_id)
presences(seance_id, student_id, status[present|retard|absent], checked_at)
projects(id, formation_code, title, type, brief, published)                      -- catalogue
assignments(id, project_id, student_id, due_on, status[afaire|encours|soumis|reprendre|valide],
            started_at, submitted_at, validated_at, grade_label, assigned_by)
submissions(id, assignment_id, files JSON, links JSON, message, created_at)
assignment_comments(assignment_id, user_id, body, created_at)
payment_schedules(id, student_id, formation_code, label, amount, due_on, status)
payments(id, student_id, schedule_id NULL, amount, method, reference, paid_at, recorded_by)
resources(id, formation_code NULL, category, title, description, format, size, licence, compatible,
          url NULL, path NULL, essential, published, created_by)
resource_downloads(resource_id, student_id, downloaded_at)
certificates(id, student_id, formation_code, code, issued_on, path)
news(id, slug, title, excerpt, body, cover, published_at)
contacts(id, name, email, phone, subject, message, handled_at)
newsletter_subscribers(email, confirmed_at)
settings(key, value)          notifications (Laravel)          activity_log (spatie)
```

## 6. Routes (extrait)

```
GET  /                         site.accueil          … une route par page de la maquette
GET  /formations/{code}        site.formation
GET  /actualites, /actualites/{slug}
GET  /preinscription           Livewire Site\Preinscription
GET  /candidature/suivi        Livewire Site\SuivreCandidature
GET  /certificats/verifier     Livewire Site\VerifierCertificat

GET  /myevc/connexion          auth (Fortify)
GET  /myevc/activation/{token}

/myevc/etudiant/*              middleware role:etudiant      accueil, cours, projets, bibliotheque, paiements, certificat, profil
/myevc/formateur/*             middleware role:formateur
/myevc/admin/*                 middleware permission:<module>
GET  /myevc/bibliotheque/{resource}/telecharger   (URL signée, compte le téléchargement)
GET  /myevc/recus/{payment}.pdf
```

## 7. Ordre de réalisation (chaque étape testée avant la suivante)

1. Squelette Laravel 12, Livewire, Fortify, permissions, Vite + CSS et polices de la maquette, maquette déplacée dans `maquette/`.
2. Layout du site + les 30 pages ; formations, sessions et actualités en base ; sitemap.
3. Préinscription en étapes, suivi de candidature, vérification de certificat, contact, newsletter.
4. Connexion, rôles et droits, activation, mot de passe oublié ; layout MY EVC (barre latérale, barre du haut, mobile).
5. Administration : formations, sessions, séances, candidatures → activation, étudiants, utilisateurs et rôles.
6. Espace étudiant : accueil, cours (présence), profil, notifications.
7. Projets : catalogue, attribution (lot), démarrage, dépôt, correction, disponibilité.
8. Paiements : échéanciers, versements, reçus PDF, relances.
9. Bibliothèque : gestion (admin, formateur) et étagères étudiant, téléchargements signés.
10. Espace formateur ; journal d'audit ; paramètres.
11. Données de démonstration (seeders), tests Pest, README d'installation et de déploiement.

## 8. Critères de validation

- `php artisan test` : tous les tests passent (un test par parcours clé ci-dessous).
- Parcours complet vérifié dans un navigateur : préinscription → candidature en administration → activation →
  connexion étudiant → projet attribué → dépôt → validation → versement → reçu PDF → ressource téléchargée.
- Chaque page s'affiche sans erreur, sur ordinateur et sur mobile, thème clair et sombre (MY EVC).
- Comptes de démonstration documentés (étudiant, formateur, admin).

## 9. Phases suivantes (hors phase 1)

Web TV, Studio Creative, RECO 60, QCM et évaluations, messagerie, créneaux et rendez-vous, stages et alumni,
support (tickets), activité en direct, communiqués multicanal (e-mail, WhatsApp), rapports avancés,
**paiement en ligne** (CinetPay ou autre agrégateur).

## 10. Hébergement

PHP 8.2+, MySQL 8, extensions standard Laravel ; `php artisan storage:link` ; tâche cron pour le planificateur
(relances, rappels) ; un worker de file d'attente (ou driver `sync` sur hébergement mutualisé) ; HTTPS obligatoire.
