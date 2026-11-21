# Structure du projet

```
my-evc-2027/
├── README.md                         Présentation, ouverture, parcours, à compléter
│
├── index.html                        SITE VITRINE — accueil
├── ecole.html                        L'école : présentation, fondateur, vision, gouvernance, Cap 2027
├── formations.html                   Les formations
│   ├── formation-design.html         Design graphique
│   ├── formation-community-management.html
│   └── formation-bureautique.html    Bureautique GIA
├── pedagogie.html                    Modèle pédagogique, parcours, My EVC, certification
├── studio-creative.html              Studio Creative et RECO 60
├── admissions.html                   Conditions et étapes d'admission
├── preinscription.html               Formulaire UNIQUE de préinscription → « Suivre ma candidature » dans MY EVC
├── jury-certification.html           Jury et vérification d'un certificat
├── realisations.html                 Réalisations, témoignages, galerie
├── phototheque.html                  Photothèque
├── entreprises.html                  Entreprises et partenaires
├── actualites.html                   Liste des actualités
├── actualite-*.html                  Une page par actualité
├── contact.html                      Contact
├── mentions-legales.html, confidentialite.html, conditions-utilisation.html,
│   conditions-generales-vente.html, reglement-interieur.html     Pages légales
├── robots.txt, sitemap.xml           Référencement
│
├── myevc/                            PLATEFORME MY EVC
│   ├── index.html                    Point d'entrée (connexion → espaces étudiant / formateur / administration)
│   └── myevc-autonome.html           Même plateforme en un seul fichier (généré, ne pas modifier à la main)
│
├── assets/
│   ├── site/                         SITE VITRINE
│   │   ├── css/site.css              Styles du site (charte bleu nuit / orange, Montserrat)
│   │   ├── js/site.js                Navigation, apparitions, carrousels, visionneuse, newsletter…
│   │   ├── js/preinscription.js      Formulaire de préinscription en étapes (envoi WhatsApp)
│   │   └── img/                      Photos, œuvres des étudiants, logos, illustrations (illus/)
│   ├── app/                          PLATEFORME MY EVC
│   │   ├── css/tokens.css            Jetons de design de MY EVC (source unique des couleurs, polices, rayons)
│   │   ├── css/app.css               Charte UI de la plateforme (coque, panneaux, tableaux, formulaires…)
│   │   ├── css/complements.css       v11 : accueil étudiant épuré, disponibilité, liens avec le site
│   │   ├── css/bibliotheque.css      Bibliothèque (étudiant, administration, formateur)
│   │   ├── fonts/                    Inter, Space Grotesk, JetBrains Mono (woff2, servies en local)
│   │   ├── img/                      Logos MY EVC (SVG : horizontal, logotype, symbole)
│   │   └── js/                       Voir le détail ci-dessous
│   └── vendor/
│       ├── bootstrap/                Bootstrap 5.3.3 (CSS pour MY EVC, JS pour le site et MY EVC)
│       ├── bootswatch-cerulean/      Thème Bootstrap du site vitrine (Bootswatch Cerulean 5.3)
│       └── fontawesome/              Font Awesome 6.5.2 (MY EVC)
│
├── gabarits/                         BLOCS COMMUNS DU SITE (source unique)
│   ├── styles.html                   Feuilles de style et polices (<head>)
│   ├── entete.html                   Icônes SVG, barre du haut, menu principal, menu mobile
│   ├── pied.html                     Pied de page, barre mobile, WhatsApp, visionneuse, scripts
│   └── modele-page.html              Point de départ d'une nouvelle page
│
├── docs/                             Cette documentation
├── outils/
│   ├── construire-site.py            Recopie les gabarits dans les pages (--verifier : contrôle seul)
│   └── construire-autonome.py        Génère myevc/myevc-autonome.html
└── .github/workflows/verifier.yml    Vérifie à chaque envoi que les pages sont à jour
```

Dans chaque page du site, les blocs communs sont encadrés par `<!-- bloc:nom -->` et
`<!-- /bloc:nom -->` : ils sont générés depuis `gabarits/`, ne pas les modifier dans la page.

Le site charge Montserrat depuis Google Fonts ; MY EVC fonctionne entièrement hors ligne.

## Scripts de MY EVC (`assets/app/js`)

Les scripts sont chargés **dans cet ordre** par `myevc/index.html`. Chaque écran est déclaré par
`view(id, espace, libellé, icône, rendu)` ; un fichier chargé plus tard peut compléter ou remplacer un écran.

| Ordre | Fichier | Rôle |
|---|---|---|
| 1 | `noyau/brand-assets.js` | Logos SVG intégrés (générés depuis `assets/app/img`) |
| 2 | `noyau/brand.js` | Identité : logo, étoile, motif « trajectoire », frise, icônes au trait |
| 3 | `noyau/data.js` | Données de démonstration : formations, profils, étudiants, sessions, candidats |
| 4 | `noyau/core.js` | État `S`, routeur (`#e-dashboard`…), gabarit, modales, notifications, graphiques |
| 5 | `etudiant/student.js` | Espace étudiant : 18 écrans de base |
| 6 | `formateur/trainer.js` | Espace formateur : séances, appel, corrections, planning |
| 7 | `admin/admin.js` | Administration : écrans de base, rôles et permissions |
| 8 | `admin/billing.js` | Factures et reçus |
| 9 | `admin/admission.js` | Admission : candidature, test d'éligibilité, activation |
| 10 | `admin/live.js` | Activité en direct |
| 11 | `noyau/ui-core.js` | Charte UI v9 : coque, barre latérale, composants `uKpi`, `uCard`, `uSeg`… |
| 12 | `etudiant/ui-student.js` | Écrans étudiants v9 (séances, travaux, paiements, certification…) |
| 13 | `formateur/ui-trainer.js` | Écrans formateur v9 |
| 14 | `admin/ui-admin.js` | Écrans admin v9 : tableau de bord, étudiants, fiche, finances… |
| 15 | `public/ui-public.js` | Connexion et préinscription de la plateforme |
| 16 | `admin/ui-projects.js` | Projets sur la fiche étudiant, catalogue par formation |
| 17 | `admin/ui-admission.js` | Dossier complet d'un candidat |
| 18 | `admin/ui-promo.js` | Promotions par session et par niveau |
| 19 | `admin/ui-projets.js` | Module Projets : création, attribution, dépôts, validation |
| 20 | `etudiant/ui-qcm.js` | QCM dans les projets |
| 21 | `admin/ui-prog.js` | Studio Creative et RECO 60 |
| 22 | `admin/ui-webtv.js` | Web TV (administration) |
| 23 | `admin/ui-presence.js` | Pointage, Google Meet, présences |
| 24 | `admin/ui-pro.js` | Vue d'ensemble, compte à rebours de fin de formation |
| 25 | **`admin/disponibilite.js`** | **v11 — Disponibilité des étudiants** (reçu / en cours / libre) |
| 26 | **`etudiant/epure.js`** | **v11 — Espace étudiant minimaliste** (menu à 5 entrées, accueil) |
| 27 | **`etudiant/bibliotheque.js`** | **Bibliothèque de l'étudiant** : logiciels, e-books, polices, modèles… à télécharger |
| 28 | **`admin/bibliotheque.js`** | **Bibliothèque : gestion** par l'administration (`a-bibliotheque`) et les formateurs (`t-bibliotheque`) |
| 29 | **`noyau/liens.js`** | **v11 — Liens entre le site et la plateforme** |

## Écrans principaux (identifiants de route)

**Étudiant** — menu : `e-dashboard` Accueil · `e-seances` Mes cours · `e-todo` Mes projets · `e-bibliotheque` Bibliothèque · `e-paiements` Paiements ·
`e-certification` Certificat. Rangés dans « Plus » : `e-formations`, `e-webtv`, `e-evaluations`, `e-messagerie`,
`e-creneaux`, `e-insertion`, `e-assistance`. Profil : carte utilisateur en bas du menu. Notifications : cloche.

**Formateur** — `t-dashboard`, `t-seances`, `t-corrections`, `t-notes`, puis `t-planning`, `t-etudiants`, `t-messagerie`, `t-bibliotheque`…

**Administration** — `a-dashboard`, `a-supervision`, `a-etudiants` (+ `a-fiche`), `a-projets`, **`a-disponibilite`**,
`a-preinscriptions`, `a-formations`, `a-seances`, `a-bibliotheque`, `a-finances`, `a-certifications`, `a-rapports`, `a-roles`,
puis les groupes Suivi, Programmes, Gestion, Système.

**Public (MY EVC)** — `login`, `candidature`, `test-eligibilite`, `activation`, `verifier`.
