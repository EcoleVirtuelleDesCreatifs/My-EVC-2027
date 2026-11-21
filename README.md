# EVC — Site vitrine + plateforme MY EVC

Un seul dépôt pour l'École Virtuelle des Créatifs :

- **le site vitrine** (racine du dépôt) : présentation de l'école, formations, pédagogie, réalisations,
  actualités, préinscription, pages légales ;
- **la plateforme MY EVC** (dossier `myevc/`) : espace étudiant, espace formateur et administration.

Tout est statique (HTML, CSS, JavaScript), sans étape de compilation.

## Ouvrir la maquette

| Pour voir…                 | Ouvrir                         |
|----------------------------|--------------------------------|
| Le site vitrine (accueil)  | `index.html`                   |
| La plateforme MY EVC       | `myevc/index.html`             |
| MY EVC en un seul fichier  | `myevc/myevc-autonome.html` (hors ligne, à envoyer tel quel) |

Pour tester les liens entre les deux comme en ligne, servez la racine du dépôt :

```
python3 -m http.server 8000
# puis http://localhost:8000/
```

**Démo MY EVC** : sur la connexion, choisissez Étudiant, Formateur ou Administration, puis « Se connecter ».
Le bouton « Mode démo » (en bas de la barre latérale) permet de changer d'espace ou de profil à tout moment.

## Un seul parcours

| Depuis | Action | Destination |
|---|---|---|
| Site | « Espace My EVC » (barre de navigation) | `myevc/index.html` (connexion) |
| Site | « Préinscription » | `preinscription.html` (formulaire unique) |
| Site | Après l'envoi : « Suivre ma candidature » | `myevc/index.html#candidature` |
| MY EVC | « Faire ma préinscription » | `preinscription.html` du site |
| MY EVC | « Retour au site de l'EVC » | `index.html` du site |

Les liens de MY EVC vers le site passent par la constante `SITE_URL` de `assets/app/js/noyau/liens.js`.

## Organisation

```
/                     Pages du site vitrine (index.html, formations, actualités, légal…)
myevc/                Plateforme MY EVC
assets/site/          CSS, JS et images du site vitrine
assets/app/           CSS, JS, polices et logos de MY EVC
assets/vendor/        Bootstrap, thème Bootswatch du site, Font Awesome
docs/                 Structure, chartes, parcours, intégration
outils/               Génération de la version autonome de MY EVC
```

Détail fichier par fichier : [`docs/STRUCTURE.md`](docs/STRUCTURE.md).

## Documentation

- [`docs/STRUCTURE.md`](docs/STRUCTURE.md) : arborescence et rôle de chaque fichier
- [`docs/CHARTE.md`](docs/CHARTE.md) : chartes graphiques (site vitrine et MY EVC)
- [`docs/PARCOURS.md`](docs/PARCOURS.md) : parcours visiteur → candidat → étudiant, et parcours admin des projets
- [`docs/INTEGRATION.md`](docs/INTEGRATION.md) : correspondance pour le développement (Laravel / Vue.js)

## Mettre à jour la version autonome de MY EVC

Après une modification de `assets/app` ou de `myevc/index.html` :

```
python3 outils/construire-autonome.py
```

## À compléter avant la mise en ligne

- Formulaires du site (préinscription, newsletter) et de MY EVC : à brancher sur le serveur
  (la préinscription envoie aujourd'hui un récapitulatif sur WhatsApp, voir `docs/INTEGRATION.md`)
- Connexion réelle de MY EVC (la connexion actuelle est une démonstration)
- Vrais témoignages (avec accord) et textes définitifs des actualités
