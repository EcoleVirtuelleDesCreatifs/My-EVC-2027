# Parcours

## 1. Du visiteur à l'étudiant

```
Site vitrine                       MY EVC
────────────                       ──────
index.html ── « Préinscription » ▶ preinscription.html (formulaire unique, en étapes)
                                     │ Envoi → « Envoyer au conseiller » (WhatsApp)
                                     │       → « Suivre ma candidature »
                                     ▼
                                   myevc/#candidature
                                     1. Dossier reçu — étude du profil (48 h)
                                     2. Profil éligible — test envoyé (e-mail + WhatsApp)
                                     3. Test en ligne (10 questions, 20 min, 2 tentatives)
                                     4. 1er versement (Mobile Money ou guichet)
                                     5. Promotion affectée → lien d'activation → mot de passe
                                     ▼
index.html ── « Espace My EVC » ─▶ myevc/#login → espace étudiant
```

## 2. L'étudiant au quotidien (espace épuré)

L'accueil affiche **une seule action principale**, choisie dans cet ordre :

1. un cours en direct aujourd'hui → « Rejoindre le cours » (pointage puis Google Meet) ;
2. un projet en retard → « Rendre mon projet » ;
3. un paiement en retard → « Payer maintenant » ;
4. un nouveau projet reçu → « Voir et démarrer » ;
5. un paiement dans les 7 jours → « Payer » ;
6. sinon, le prochain cours.

En dessous, trois cartes : **Prochain cours**, **Mon projet**, **Paiements** ; puis la progression par formation
et, pour un nouvel étudiant, « Bien démarrer » (3 étapes).

## 3. Les projets, côté administration

```
Créer / choisir un projet ──▶ Attribuer ──▶ Étudiant : « Projet reçu »
                                               │ clique « Je démarre »   (admin notifié)
                                               ▼
                                            « En cours »  ── corrections en classe virtuelle WhatsApp
                                               │ « Terminer » : dépose son travail (admin notifié)
                                               ▼
                                            Rendu → l'étudiant est LIBRE
                                               │ l'admin valide ou demande une reprise
                                               ▼
                                            Validé  (ou « À reprendre » → de nouveau « En cours »)
```

**Écran Disponibilité** (`#a-disponibilite`) :

- 4 tuiles cliquables : Libres · Projet reçu · En cours · En retard (servent aussi de filtre) ;
- répartition par formation (barre empilée) ;
- « À valider de votre côté » : rendus en attente, avec accès direct à la validation ;
- tableau : état, projet actuel et échéance, parcours (un carré par projet), action
  — **Attribuer** si l'étudiant est libre, **Rappeler / Relancer** sinon ;
- sélection groupée : attribuer un projet en lot, relancer, écrire ;
- bouton **« Donner un projet aux libres »** : sélectionne tous les libres (selon les filtres) et ouvre l'attribution ;
- export CSV.

Le même état apparaît sur le tableau de bord (« Projets : qui est libre ? »), dans le module Projets
et en tête de chaque fiche étudiant.
