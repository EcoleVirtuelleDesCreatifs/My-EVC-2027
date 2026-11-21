@extends('layouts.site')
@section('title', "Admissions & tarifs · EVC — École Virtuelle des Créatifs")
@section('description', "Inscriptions 2026–2027 à l'EVC : tarifs, paiement en 2 fois, ce qui est inclus et pré-inscription en ligne.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'admissions')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/remise-2.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Admissions</span></nav>
    <h1 class="mb-3 reveal">Votre place vous attend. <span>Choisissez votre formation.</span></h1>
    <p class="mb-0 reveal d1">Inscriptions ouvertes pour la rentrée 2026–2027. Des tarifs clairs, payables en 2 fois, et un échange d'orientation avant de vous engager.</p>
    <div class="d-flex flex-wrap gap-3 mt-4 reveal d2"><a href="/preinscription" class="btn btn-evc">Préinscription en ligne</a><a href="https://wa.me/2250747259507?text=INSCRIPTION" target="_blank" rel="noopener" class="btn btn-outline-light d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Écrire « INSCRIPTION »</a></div>
  </div>
</header>
<section class="section">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:760px"><span class="kicker center reveal">Tarifs 2026–2027</span><h2 class="title reveal d1">Des tarifs clairs, <span class="hl">affichés en toute transparence</span></h2></div>
    <div class="row g-4 align-items-stretch pt-3"><div class="col-lg-4 reveal"><div class="price-card featured"><span class="ribbon">LA PLUS COMPLÈTE · 4 MOIS</span><span class="kicker light mb-2">4 mois</span><h4 class="mb-3">Infographie & design graphique</h4><div class="amount">185 000 <small>FCFA</small></div><p class="small text-muted-evc mt-2 mb-0">Paiement en 2 fois : 95 000 FCFA à l'inscription, puis 90 000 FCFA après 2 mois</p><ul><li><svg class="ico"><use href="#i-check"/></svg><span>En ligne et en présentiel</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Photoshop, Illustrator, InDesign</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat sous conditions + lettre de recommandation</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Immersion : Studio Creative</span></li></ul><div class="mt-auto d-grid gap-2"><a href="/preinscription#design" class="btn btn-evc">Choisir cette formation</a><a href="/formation-design" class="btn btn-sm" style="color:inherit">Voir le programme</a></div></div></div><div class="col-lg-4 reveal d1"><div class="price-card"><span class="kicker mb-2">2 mois</span><h4 class="mb-3">Community management & social media</h4><div class="amount">165 000 <small>FCFA</small></div><p class="small text-muted-evc mt-2 mb-0">Paiement en 2 fois : 85 000 FCFA à l'inscription, puis 80 000 FCFA après 1 mois</p><ul><li><svg class="ico"><use href="#i-check"/></svg><span>En ligne et en présentiel</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Facebook, Instagram, TikTok, Publicité sociale</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat sous conditions + lettre de recommandation</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Immersion : RECO 60</span></li></ul><div class="mt-auto d-grid gap-2"><a href="/preinscription#cm" class="btn btn-navy">Choisir cette formation</a><a href="/formation-community-management" class="btn btn-sm" style="color:inherit">Voir le programme</a></div></div></div><div class="col-lg-4 reveal d2"><div class="price-card"><span class="kicker mb-2">2 mois</span><h4 class="mb-3">Bureautique & informatique (GIA)</h4><div class="amount">265 000 <small>FCFA</small></div><p class="small text-muted-evc mt-2 mb-0">Paiement en 2 fois : 140 000 FCFA à l'inscription, puis 125 000 FCFA en cours de formation</p><ul><li><svg class="ico"><use href="#i-check"/></svg><span>En ligne et en présentiel</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Word, Excel, PowerPoint, Google Workspace, Canva</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat sous conditions + lettre de recommandation</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Immersion : RECO 60</span></li></ul><div class="mt-auto d-grid gap-2"><a href="/preinscription#gia" class="btn btn-navy">Choisir cette formation</a><a href="/formation-bureautique" class="btn btn-sm" style="color:inherit">Voir le programme</a></div></div></div></div>
    <div class="mt-5"><div class="included reveal"><div class="d-flex justify-content-between flex-wrap gap-2 mb-4"><h5 class="mb-0">Inclus dans toutes nos formations</h5><span class="chip mb-0" style="background:#fff">Toutes formations</span></div><div class="row g-3"><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-award"/></svg></span>Certificat et lettre de recommandation (sous conditions)</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-app"/></svg></span>Espace étudiant My EVC</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span>Kit étudiant et T-shirt EVC</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-users"/></svg></span>Coaching personnalisé</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-monitor"/></svg></span>Classes virtuelles</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-hand"/></svg></span>Opportunités de stage</div></div></div></div></div>
  </div>
</section>

<section class="section bg-cream">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:760px"><span class="kicker center reveal">Comment s'inscrire</span><h2 class="title reveal d1">Quatre étapes simples, <span class="hl">un conseiller à vos côtés</span></h2></div>
    <div class="row g-4 steps-line">
      <div class="col-6 col-md-3 step reveal"><div class="sn">1</div><h5>Contact</h5><p>Pré-inscription en ligne ou « INSCRIPTION » sur WhatsApp.</p></div>
      <div class="col-6 col-md-3 step reveal d1"><div class="sn">2</div><h5>Orientation</h5><p>Échange sur votre profil, vos motivations et vos objectifs.</p></div>
      <div class="col-6 col-md-3 step reveal d2"><div class="sn">3</div><h5>Admission</h5><p>Règlement de la 1ʳᵉ tranche et confirmation de votre place.</p></div>
      <div class="col-6 col-md-3 step reveal d3"><div class="sn">4</div><h5>Intégration</h5><p>Programme, planning et accès à votre espace My EVC.</p></div>
    </div>
  </div>
</section>

<section class="section" id="preinscription">
  <div class="container"><div class="pi-cta reveal">
    <div><span class="kicker light">Préinscription en ligne</span><h2 class="text-white mb-2">Réservez votre place <span class="hl">en 6 étapes.</span></h2><p class="mb-0">Choisissez une ou plusieurs formations, présentez votre profil, ajoutez votre portfolio et vos documents. Environ 8 minutes.</p></div>
    <a href="/preinscription" class="btn btn-evc btn-lg d-inline-flex align-items-center gap-2">Commencer ma préinscription <svg class="ico ico-arrow"><use href="#i-arrow"/></svg></a>
  </div></div>
</section>

<section class="section bg-cream pt-5">
  <div class="container"><div class="row g-5">
    <div class="col-lg-4"><span class="kicker reveal">Vos questions, nos réponses</span><h2 class="title reveal d1">Admissions : <span class="hl">tout savoir</span></h2></div>
    <div class="col-lg-8 reveal d1"><div class="accordion faq" id="faqA">
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#a1" aria-expanded="true">Je pars de zéro, est-ce pour moi ?</button></h3><div id="a1" class="accordion-collapse collapse show" data-bs-parent="#faqA"><div class="accordion-body">Oui. Nos formations sont ouvertes à tous les profils, même sans formation initiale. On commence par les bases, pas à pas.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">Comment fonctionne le paiement en 2 fois ?</button></h3><div id="a2" class="accordion-collapse collapse" data-bs-parent="#faqA"><div class="accordion-body">Vous réglez une première tranche à l'inscription, puis le solde en cours de formation : après 2 mois pour le design, après 1 mois pour le community management, et en cours de formation pour la bureautique.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a3">Je travaille. Aurai-je le temps ?</button></h3><div id="a3" class="accordion-collapse collapse" data-bs-parent="#faqA"><div class="accordion-body">Oui. Les cours restent disponibles dans votre espace My EVC. Beaucoup de nos apprenants se forment en travaillant.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a4">Que vaut le certificat ?</button></h3><div id="a4" class="accordion-collapse collapse" data-bs-parent="#faqA"><div class="accordion-body">Il est délivré après validation du parcours et des projets, avec une lettre de recommandation. C'est une reconnaissance crédible des compétences, et non une garantie d'emploi.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a6">Et si je ne suis pas éligible au certificat ?</button></h3><div id="a6" class="accordion-collapse collapse" data-bs-parent="#faqA"><div class="accordion-body">Vous êtes informé des motifs et pouvez suivre une session de rattrapage, payante, pour reprendre les éléments non validés. <a href="/jury-certification#rattrapage">Voir les conditions</a></div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a5">Et après la formation ?</button></h3><div id="a5" class="accordion-collapse collapse" data-bs-parent="#faqA"><div class="accordion-body">Stages, missions, mises en relation : plus d'un certifié sur trois est aujourd'hui en emploi ou en stage (176 sur 507).</div></div></div>
    </div></div>
  </div></div>
</section>
@endverbatim
@endsection
