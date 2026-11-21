# Intégration (Laravel / Vue.js)

## Principes

- Un écran `view('a-disponibilite', …)` = une route + un composant Vue.
- `assets/app/css/tokens.css` devient le fichier de variables du thème MY EVC (ou la config Tailwind / SCSS).
- Les données de `noyau/data.js` reprennent les entités du cahier des charges (§39).
- Le site vitrine et MY EVC peuvent vivre sur deux domaines : seuls la constante `SITE_URL`
  (`noyau/liens.js`) et les liens `myevc/index.html` du site (bouton « Espace My EVC »,
  « Suivre ma candidature » de `preinscription.html`) sont à ajuster.
- La préinscription du site (`assets/site/js/preinscription.js`) prépare aujourd'hui un message WhatsApp :
  en production, l'envoi du formulaire crée la candidature côté serveur.

## Entités utiles aux projets

```
etudiants(id, matricule, nom, statut, session_id, niveau, …)
inscriptions(etudiant_id, formation_code)                 -- un étudiant, plusieurs formations
projets(id, formation_code, titre, type, consignes, echeance, statut[brouillon|publié], cible_mode, …)
projet_cibles(projet_id, etudiant_id | session_id | formation_code, niveau)
attributions(id, projet_id, etudiant_id, attribue_le, echeance,
             statut[afaire|encours|soumis|reprendre|valide], demarre_le, rendu_le, valide_le)
depots(attribution_id, fichier | lien | video, message, cree_le)
```

Un projet visant « toute une formation » ou « une session » crée une ligne `attributions` par étudiant concerné :
l'état de chaque étudiant se lit alors dans une seule table.

## Disponibilité : la requête

```sql
SELECT e.id, e.nom,
       SUM(a.statut = 'afaire')                         AS recus_non_demarres,
       SUM(a.statut IN ('encours','reprendre'))         AS en_cours,
       SUM(a.statut IN ('afaire','encours','reprendre')
           AND a.echeance < CURRENT_DATE)               AS en_retard,
       SUM(a.statut = 'soumis')                         AS a_valider,
       SUM(a.statut = 'valide')                         AS valides,
       MAX(COALESCE(a.rendu_le, a.valide_le))           AS dernier_rendu,
       CASE WHEN SUM(a.statut IN ('encours','reprendre')) > 0 THEN 'encours'
            WHEN SUM(a.statut = 'afaire') > 0           THEN 'recu'
            ELSE 'libre' END                            AS etat
FROM etudiants e
LEFT JOIN attributions a ON a.etudiant_id = e.id
WHERE e.statut IN ('Actif','À activer')
GROUP BY e.id, e.nom;
```

« Libre depuis N jours » = aujourd'hui − `dernier_rendu`. Un étudiant sans aucune attribution est « libre, aucun projet reçu ».

## Points d'API suggérés

| Méthode | Route | Usage |
|---|---|---|
| GET | `/api/admin/disponibilite?formation=&session=&niveau=&etat=` | Tableau et compteurs |
| POST | `/api/admin/attributions` `{projet_id, etudiant_ids[], echeance, notifier}` | Attribution en lot |
| POST | `/api/admin/attributions/relance` `{etudiant_ids[]}` | Rappel (notification, e-mail, WhatsApp) |
| PATCH | `/api/etudiant/attributions/{id}` `{statut:'encours'}` | « Je démarre » |
| POST | `/api/etudiant/attributions/{id}/depot` | « Terminer » : dépôt du travail |
| PATCH | `/api/admin/attributions/{id}` `{statut:'valide'|'reprendre', commentaire}` | Validation |

Chaque changement de statut envoie une notification (cloche admin, espace étudiant) et alimente le journal d'audit.

## Formulaire de préinscription

Le formulaire du site (`preinscription.html`, géré par `assets/site/js/site.js`) est le seul point d'entrée.
À brancher sur `POST /api/candidatures`. La réponse renvoie la référence `PRE-xxxx` utilisée par
`myevc/#candidature` pour le suivi.
