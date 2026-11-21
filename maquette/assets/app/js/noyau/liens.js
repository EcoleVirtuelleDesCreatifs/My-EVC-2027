/* ============ MY EVC — liens avec le site vitrine ============
   Le site vitrine (racine du dépôt) et la plateforme (dossier myevc/) forment un seul parcours :
     site  → « Espace My EVC » ouvre myevc/index.html (connexion)
     site  → « Je m'inscris » ouvre preinscription.html (formulaire unique de préinscription)
     site  → après l'envoi, « Suivre ma candidature » ouvre myevc/index.html#candidature
     myevc → « Faire ma préinscription » renvoie vers ce même formulaire du site
     myevc → « Retour au site » sur la connexion et les pages publiques
   SITE_URL est relatif pour que la maquette fonctionne en local comme en ligne.
   Si le site et MY EVC sont servis sur deux domaines, mettre ici l'adresse complète du site. */
const SITE_URL = '../';

(() => {
  const login = VIEWS.login, base = login.render;
  login.render = () => base()
    .replace('href="#preinscription"', `href="${SITE_URL}preinscription.html"`)
    .replace('<div class="l2-copy">', `<a class="l2-back" href="${SITE_URL}index.html"><i class="fa-solid fa-arrow-left"></i>Retour au site de l'EVC</a><div class="l2-copy">`)
    .replace('<h2 class="l2-t">', `<a class="l2-back l2-back-m" href="${SITE_URL}index.html"><i class="fa-solid fa-arrow-left"></i>Site de l'EVC</a><h2 class="l2-t">`);

  /* Pages publiques de la plateforme (candidature, test, activation, vérification) : lien vers le site */
  ['candidature','test-eligibilite','activation','verifier','preinscription'].forEach(id => { const v = VIEWS[id]; if (!v) return; const r = v.render;
    v.render = (...a) => r(...a).replace(/(<header class="pub-top">[\s\S]*?)(<span class="small muted ms-auto">)/, `$1<a class="small link-u ms-auto me-3 d-none d-sm-inline" href="${SITE_URL}index.html">Site de l'EVC</a><span class="small muted">`); });
})();
