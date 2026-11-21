# Chartes graphiques

Le site vitrine et la plateforme MY EVC ont chacun leur charte.

## Site vitrine

Source : variables `:root` de `assets/site/css/site.css`, sur le thème Bootswatch Cerulean.

| Rôle | Variable | Valeur |
|---|---|---|
| Bleu nuit : titres, barre de navigation, pied de page | `--navy` | `#0E1A3C` |
| Bleu royal : liens, couleur primaire Bootstrap | `--royal` | `#3563AD` |
| Orange : action principale (Préinscription) | `--orange` | `#F39822` |
| Or : accents sur fond sombre | `--gold` | `#FDC967` |
| Crème : fonds de section | `--cream` | `#F7F4EE` |
| Encre / texte secondaire | `--ink` / `--muted` | `#18203A` / `#5D6681` |

- Police : **Montserrat** (Google Fonts), titres en 800.
- Icônes : sprite SVG au trait intégré en tête de chaque page (`<use href="#i-…">`), classe `.ico`.
- Logos : `assets/site/img/logo.png` (fonds clairs) et `logo-white.png` (fonds sombres).

## MY EVC

Source unique : `assets/app/css/tokens.css`. `app.css` et `complements.css` lisent ces jetons.

### Couleurs

| Rôle | Jeton | Valeur |
|---|---|---|
| Indigo : structure, titres, barre latérale, fonds sombres | `--evc-indigo-900` | `#1E1B4B` |
| Indigo profond : pied de page, dégradés du hero | `--evc-indigo-950` | `#110F2E` |
| Lavande : fonds doux, bordures | `--evc-indigo-100` | `#E4E2F3` |
| Brume : fond des interfaces | `--evc-indigo-50` | `#F4F3F8` |
| **Corail : action principale** (un seul bouton corail par zone) | `--evc-corail-500` | `#EE5A36` |
| Corail lisible sur fond clair (liens, petits textes) | `--evc-corail-700` | `#C2401F` |
| Corail clair : accent sur fond indigo | `--evc-corail-300` | `#FF8A6B` |
| Ardoise : textes secondaires | `--evc-ardoise` | `#5E6470` |
| Succès / Alerte / Erreur / Info | `--evc-succes` … | `#1F8F5A` / `#B26A00` / `#C9302C` / `#3A3591` |
| Formations DG / CM / GIA (repères) | `--evc-dg` … | `#C2255C` / `#2F6BD8` / `#1F8F5A` |

Règle : le corail sert à **agir**, pas à décorer. Texte corail sur fond clair : toujours `--evc-corail-700` (contraste AA).

### Typographie

- **Space Grotesk** (500–700) : titres, chiffres clés, logotype.
- **Inter** (400–700) : textes et interfaces.
- **JetBrains Mono** : matricules, références, codes.

### Formes

- Boutons et champs : rayon **12 px** (`--evc-r-sm`).
- Cartes et panneaux : **18 px** (`--evc-r`). Grands visuels : **28 px** (`--evc-r-lg`).
- Pastilles (statuts, filtres) : entièrement arrondies.
- Ombres : `--evc-ombre-sm`, `--evc-ombre`, `--evc-ombre-lg` (teintées indigo, jamais noires).

### Icônes

- Font Awesome, converties à l'affichage en icônes au trait 1,8 px (`ICONS` dans `brand.js`).

### Logos

- Dans `assets/app/img` : `logo-horizontal-*`, `logotype-*`, `symbole-*` en `couleur` (fond clair), `negatif` (fond indigo), `blanc` (photo, corail).

### États de disponibilité (administration)

| État | Couleur | Signification |
|---|---|---|
| Libre | Succès (vert) | Plus aucun projet à faire : prêt pour un nouveau projet |
| Projet reçu | Ambre `#E9A23B` | Projet attribué, pas encore démarré |
| En cours | Info (indigo) | Projet démarré ou à reprendre |
| En retard | Erreur (rouge) | Échéance dépassée sur un projet en attente |

### Principes pour l'espace étudiant

1. **Une action principale par écran**, en corail.
2. Pas plus de 6 entrées visibles dans le menu (Accueil, Mes cours, Mes projets, Bibliothèque, Paiements, Certificat) ; le reste dans « Plus ».
3. Des mots simples : « Mes cours », « Mes projets », « Payer », « Rendre mon projet ».
4. Chaque carte de l'accueil mène à un seul écran.
5. Pas de notes chiffrées : appréciations (Excellent, Très bien, Acquis, À renforcer).
