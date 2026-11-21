@extends('layouts.site')
@section('title', "Nos formations · EVC — École Virtuelle des Créatifs")
@section('description', "Formations EVC : infographie et design graphique (4 mois), community management (2 mois), bureautique GIA (2 mois). Tarifs, comparatif et modalités.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'formations')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/atelier.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Formations</span></nav>
    <h1 class="mb-3 reveal">Trois formations, un seul objectif : <span>votre métier</span></h1>
    <p class="mb-0 reveal d1">Des formations courtes, concrètes et certifiantes pour décrocher un métier qui vous ressemble. En ligne ou en présentiel, payables en 2 fois.</p>
    <div class="d-flex flex-wrap gap-3 mt-4 reveal d2"><a href="#comparatif" class="btn btn-evc">Comparer les formations</a><a href="/admissions" class="btn btn-outline-light">Admissions & tarifs</a></div>
  </div>
</header>
<section class="section">
  <div class="container">
    <div class="row g-4"><div class="col-md-6 col-lg-4 reveal"><article class="f-card" >
  <a href="/formation-design" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/atelier.jpg" alt="Infographie & design graphique"><span class="dur">4 MOIS</span><span class="letter" style="background:var(--orange)">A</span></a>
  <div class="body"><h4><a href="/formation-design" class="text-decoration-none" style="color:inherit">Infographie & design graphique</a></h4><p>Vous aimez les couleurs, les images, les idées ? En 4 mois, apprenez à créer des logos, des affiches et des visuels de niveau professionnel.</p><div class="tools mt-auto"><span>PHOTOSHOP</span><span>ILLUSTRATOR</span><span>INDESIGN</span></div></div>
  <div class="foot"><div><b>185 000 FCFA</b><small>ou 2 × : 95 000 + 90 000 FCFA</small></div><a href="/formation-design" class="go" aria-label="Voir la formation Infographie & design graphique"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div><div class="col-md-6 col-lg-4 reveal d1"><article class="f-card" >
  <a href="/formation-community-management" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/promo-cm.jpg" alt="Community management & social media"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--royal)">B</span></a>
  <div class="body"><h4><a href="/formation-community-management" class="text-decoration-none" style="color:inherit">Community management & social media</a></h4><p>Facebook, Instagram, TikTok : apprenez à animer une communauté, à créer des contenus qui marchent et à mesurer vos résultats.</p><div class="tools mt-auto"><span>FACEBOOK</span><span>INSTAGRAM</span><span>TIKTOK</span><span>PUBLICITÉ SOCIALE</span></div></div>
  <div class="foot"><div><b>165 000 FCFA</b><small>ou 2 × : 85 000 + 80 000 FCFA</small></div><a href="/formation-community-management" class="go" aria-label="Voir la formation Community management & social media"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div><div class="col-md-6 col-lg-4 reveal d2"><article class="f-card" >
  <a href="/formation-bureautique" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/classe-4.jpg" alt="Bureautique & informatique (GIA)"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--blue)">C</span></a>
  <div class="body"><h4><a href="/formation-bureautique" class="text-decoration-none" style="color:inherit">Bureautique & informatique (GIA)</a></h4><p>Word, Excel, PowerPoint, Google : devenez la personne sur qui tout le bureau peut compter.</p><div class="tools mt-auto"><span>WORD</span><span>EXCEL</span><span>POWERPOINT</span><span>GOOGLE WORKSPACE</span></div></div>
  <div class="foot"><div><b>265 000 FCFA</b><small>ou 2 × : 140 000 + 125 000 FCFA</small></div><a href="/formation-bureautique" class="go" aria-label="Voir la formation Bureautique & informatique (GIA)"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div></div>
    <div class="mt-4 p-4 rounded-4 d-flex flex-wrap gap-3 align-items-center reveal" style="border:1.5px dashed rgba(14,26,60,.2)"><span class="d-grid" style="width:48px;height:48px;border-radius:14px;place-items:center;background:var(--cream);color:var(--royal)"><svg class="ico"><use href="#i-bulb"/></svg></span><div class="flex-grow-1"><b style="color:var(--navy)">Une offre appelée à évoluer</b><div class="small text-muted-evc">Intelligence artificielle, nouveaux métiers du numérique : nous préparons déjà les formations de demain. De nouveaux programmes ouvriront après validation par l'école.</div></div><a href="/contact" class="link-arrow">Être informé <svg class="ico"><use href="#i-arrow"/></svg></a></div>
  </div>
</section>

<section class="section bg-cream" id="comparatif">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:760px"><span class="kicker center reveal">Comparatif</span><h2 class="title reveal d1">Quelle formation <span class="hl">est faite pour vous ?</span></h2><p class="lead-evc reveal d2">Toutes les informations utiles, côte à côte.</p></div>
    <div class="table-responsive bg-white rounded-4 reveal" style="box-shadow:var(--shadow)">
      <table class="table align-middle mb-0" style="min-width:820px">
        <thead><tr style="background:var(--navy)"><th scope="col" style="background:var(--navy);color:#fff;padding:1.2rem">Critère</th><th scope="col" style="background:var(--navy);color:#fff;padding:1.2rem"><span class="d-inline-grid me-2" style="width:30px;height:30px;border-radius:8px;place-items:center;background:var(--orange);color:#fff;font-size:.8rem">A</span>Design graphique</th><th scope="col" style="background:var(--navy);color:#fff;padding:1.2rem"><span class="d-inline-grid me-2" style="width:30px;height:30px;border-radius:8px;place-items:center;background:var(--royal);color:#fff;font-size:.8rem">B</span>Community management</th><th scope="col" style="background:var(--navy);color:#fff;padding:1.2rem"><span class="d-inline-grid me-2" style="width:30px;height:30px;border-radius:8px;place-items:center;background:var(--blue);color:#fff;font-size:.8rem">C</span>Bureautique GIA</th></tr></thead>
        <tbody class="small" style="--bs-table-bg:#fff"><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Durée</th><td style="padding:1rem 1.2rem;color:var(--muted)">4 mois</td><td style="padding:1rem 1.2rem;color:var(--muted)">2 mois</td><td style="padding:1rem 1.2rem;color:var(--muted)">2 mois</td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Rythme</th><td style="padding:1rem 1.2rem;color:var(--muted)">En ligne et en présentiel</td><td style="padding:1rem 1.2rem;color:var(--muted)">En ligne et en présentiel</td><td style="padding:1rem 1.2rem;color:var(--muted)">En ligne et en présentiel</td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Outils</th><td style="padding:1rem 1.2rem;color:var(--muted)">Photoshop, Illustrator, InDesign</td><td style="padding:1rem 1.2rem;color:var(--muted)">Facebook, Instagram, TikTok, Publicité sociale</td><td style="padding:1rem 1.2rem;color:var(--muted)">Word, Excel, PowerPoint, Google Workspace, Canva</td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Dispositif pratique</th><td style="padding:1rem 1.2rem;color:var(--muted)">Studio Creative</td><td style="padding:1rem 1.2rem;color:var(--muted)">RECO 60</td><td style="padding:1rem 1.2rem;color:var(--muted)">RECO 60</td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Tarif total</th><td style="padding:1rem 1.2rem;color:var(--muted)"><b style="color:var(--navy)">185 000 FCFA</b></td><td style="padding:1rem 1.2rem;color:var(--muted)"><b style="color:var(--navy)">165 000 FCFA</b></td><td style="padding:1rem 1.2rem;color:var(--muted)"><b style="color:var(--navy)">265 000 FCFA</b></td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Paiement en 2 fois</th><td style="padding:1rem 1.2rem;color:var(--muted)">95 000 FCFA à l'inscription, puis 90 000 FCFA après 2 mois</td><td style="padding:1rem 1.2rem;color:var(--muted)">85 000 FCFA à l'inscription, puis 80 000 FCFA après 1 mois</td><td style="padding:1rem 1.2rem;color:var(--muted)">140 000 FCFA à l'inscription, puis 125 000 FCFA en cours de formation</td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Certificat + lettre de recommandation</th><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td></tr><tr><th scope="row" style="padding:1rem 1.2rem;color:var(--navy)">Accès My EVC</th><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td><td style="padding:1rem 1.2rem;color:var(--muted)"><svg class="ico"><use href="#i-check"/></svg></td></tr><tr><th></th><td><a href="/formation-design" class="btn btn-navy btn-sm">Voir le programme</a></td><td><a href="/formation-community-management" class="btn btn-navy btn-sm">Voir le programme</a></td><td><a href="/formation-bureautique" class="btn btn-navy btn-sm">Voir le programme</a></td></tr></tbody>
      </table>
    </div>
  </div>
</section>

<section class="section">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-5"><span class="kicker reveal">Comment se déroule une formation</span><h2 class="title reveal d1">Un parcours <span class="hl">clair, du premier jour au certificat</span></h2><p class="lead-evc reveal d2">Chaque formation suit le même principe : apprendre, pratiquer, produire et démontrer. Vous êtes suivi à chaque étape.</p><a href="/pedagogie#parcours" class="btn btn-navy reveal">Le parcours en 8 étapes</a></div>
    <div class="col-lg-7 reveal d1"><div class="timeline">
      <div class="tl-item" data-n="1"><h6>Orientation personnalisée</h6><p>Un échange sur votre niveau, vos motivations et vos objectifs pour choisir la bonne formation.</p></div>
      <div class="tl-item" data-n="2"><h6>Admission et intégration</h6><p>Vous recevez votre programme, votre planning et vos accès My EVC.</p></div>
      <div class="tl-item" data-n="3"><h6>Formation et projets</h6><p>Séances, exercices quotidiens, cas réels et retours de vos formateurs.</p></div>
      <div class="tl-item" data-n="4"><h6>Immersion professionnelle</h6><p>Studio Creative (design) ou RECO 60 (community management, bureautique).</p></div>
      <div class="tl-item" data-n="5"><h6>Certification et valorisation</h6><p>Certificat, lettre de recommandation, portfolio et opportunités de stage.</p></div>
    </div></div>
  </div></div>
</section>

<section class="pb-5"><div class="container"><div class="included reveal"><div class="d-flex justify-content-between flex-wrap gap-2 mb-4"><h5 class="mb-0">Inclus dans toutes nos formations</h5><span class="chip mb-0" style="background:#fff">Toutes formations</span></div><div class="row g-3"><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-award"/></svg></span>Certificat et lettre de recommandation (sous conditions)</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-app"/></svg></span>Espace étudiant My EVC</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span>Kit étudiant et T-shirt EVC</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-users"/></svg></span>Coaching personnalisé</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-monitor"/></svg></span>Classes virtuelles</div></div><div class="col-sm-6 col-lg-4"><div class="it"><span class="ic"><svg class="ico"><use href="#i-hand"/></svg></span>Opportunités de stage</div></div></div></div></div></section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/atelier.jpg" alt=""></div>
      <div class="row g-4 align-items-center">
        <div class="col-lg-7">
          <span class="kicker light">Rentrée 2026–2027 · inscriptions ouvertes</span>
          <h2 class="mb-3">Votre place vous attend.<br><span class="hl">Choisissez votre formation.</span></h2>
          <p class="mb-0">Écrivez « INSCRIPTION » sur WhatsApp ou pré-inscrivez-vous en ligne : un conseiller vous recontacte et vous oriente.</p>
        </div>
        <div class="col-lg-5 d-flex flex-wrap gap-3 justify-content-lg-end">
          <a href="/preinscription" class="btn btn-evc btn-lg d-inline-flex align-items-center gap-2">Préinscription en ligne <svg class="ico ico-arrow"><use href="#i-arrow"/></svg></a>
          <a href="https://wa.me/2250747259507?text=INSCRIPTION" target="_blank" rel="noopener" class="btn btn-outline-light btn-lg d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> WhatsApp</a>
        </div>
      </div>
    </div>
  </div>
</section>
@endverbatim
@endsection
