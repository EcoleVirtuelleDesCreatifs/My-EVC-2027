@extends('layouts.site')
@section('title', "Community management & social media · EVC — École Virtuelle des Créatifs")
@section('description', "Formation community management à Abidjan : ".$publicCourse->months." mois, ".number_format($publicCourse->price, 0, ',', ' ')." FCFA. Réseaux sociaux, programme et inscription à l'EVC.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'formations')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/promo-cm.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><a href="/formations">Formations</a><span>/</span><span>Community management</span></nav>
@endverbatim
    <h1 class="mb-3 reveal">Community management &amp; social media <span>· {{ $publicCourse->months }} mois</span></h1>
    <p class="mb-0 reveal d1">Facebook, Instagram, TikTok : apprenez à animer une communauté, à créer des contenus qui marchent et à mesurer vos résultats.</p>
    <div class="d-flex flex-wrap gap-2 mt-4 reveal d2"><span class="pill"><b>{{ $publicCourse->months }} MOIS</b>En ligne et en présentiel</span><span class="pill"><b>{{ number_format($publicCourse->price, 0, ',', ' ') }} F</b>Paiement en 2 fois</span><span class="pill"><b>CERTIFIANT</b>Certificat + recommandation</span></div>
    @unless ($publicCourse->active)<p class="mt-3 mb-0">Les préinscriptions à cette formation sont actuellement fermées.</p>@endunless
@verbatim
  </div>
</header>
<section class="section">
  <div class="container"><div class="row g-5">
    <div class="col-lg-8">
      <span class="kicker reveal">Compétences développées</span>
      <h2 class="title reveal d1">Ce que vous saurez <span class="hl">faire en sortant</span></h2>
      <ul class="check-list cols mb-5 reveal d2"><li><svg class="ico"><use href="#i-check"/></svg><span>Écosystème des réseaux sociaux</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Création de pages professionnelles</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Analyse des audiences et objectifs</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Stratégie social media</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Calendriers éditoriaux</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Production de contenus numériques</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Animation et modération</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Initiation à la publicité sociale</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Indicateurs de performance</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Gestion de l'e-réputation</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Reporting et recommandations</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Campagnes de communication</span></li></ul>

      <h3 class="h4 mb-3 reveal">Outils maîtrisés</h3>
      <div class="tools-row mb-5 reveal"><div class="tool"><i style="background:#1877F2;color:#fff">f</i>Facebook</div><div class="tool"><i style="background:#E1306C;color:#fff">Ig</i>Instagram</div><div class="tool"><i style="background:#111;color:#fff">Tk</i>TikTok</div><div class="tool"><i style="background:#3563AD;color:#fff">Ads</i>Publicité sociale</div></div>

      <span class="kicker reveal">Programme</span>
      <h2 class="title reveal d1">Le contenu, <span class="hl">module par module</span></h2>
      <div class="accordion module mb-5 reveal" id="mods"><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mod0" aria-expanded="true"><span class="mn">01</span>Écosystème & audiences</button></h3><div id="mod0" class="accordion-collapse collapse show" data-bs-parent="#mods"><div class="accordion-body">Panorama des plateformes, personas, objectifs SMART.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod1" aria-expanded="false"><span class="mn">02</span>Stratégie social media</button></h3><div id="mod1" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Positionnement, ligne éditoriale, calendrier éditorial.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod2" aria-expanded="false"><span class="mn">03</span>Production de contenus</button></h3><div id="mod2" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Visuels, vidéos courtes, rédaction de publications qui engagent.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod3" aria-expanded="false"><span class="mn">04</span>Animation & modération</button></h3><div id="mod3" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Gérer une communauté, répondre, protéger l'e-réputation.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod4" aria-expanded="false"><span class="mn">05</span>Publicité sociale</button></h3><div id="mod4" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Initiation aux campagnes sponsorisées et au ciblage.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod5" aria-expanded="false"><span class="mn">06</span>Mesure & reporting · RECO 60</button></h3><div id="mod5" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Indicateurs, rapports, recommandations et 60 jours de projet terrain.</div></div></div></div>

      <div class="row g-4 mb-2">
        <div class="col-md-7 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><h5>Débouchés</h5><div class="mt-2"><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Community manager</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Social media manager junior</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Chargé de communication digitale</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Créateur de contenus</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Consultant indépendant</span></div></div></div>
        <div class="col-md-5 reveal d1"><div class="card-evc" style="background:var(--navy);border-color:var(--navy)"><span class="ic" style="background:rgba(255,255,255,.08);color:var(--gold)"><svg class="ico"><use href="#i-star"/></svg></span><h5 class="text-white">Immersion : RECO 60</h5><p style="color:#C3CBE7" class="mb-3">60 jours pour gérer une vraie présence digitale et analyser une campagne.</p><a href="/studio-creative#reco60" class="link-arrow" style="color:var(--gold)">En savoir plus <svg class="ico"><use href="#i-arrow"/></svg></a></div></div>
      </div>
      <div class="mt-5" data-slider><div class="d-flex flex-wrap justify-content-between align-items-end gap-4 mb-4 ">
  <div style="max-width:720px"><span class="kicker reveal">En images</span><h2 class="title mb-2 reveal d1">La formation en pratique</h2></div>
  <div class="slider-ctrl"><div class="slider-progress me-2 d-none d-md-block"><i></i></div><button data-prev aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button data-next aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button></div>
</div><div class="evc-track"><a href="/assets/site/img/promo-cm.jpg" data-lb="f" data-caption="Community management à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/promo-cm.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-5.jpg" data-lb="f" data-caption="Community management à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-5.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-6.jpg" data-lb="f" data-caption="Community management à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-6.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-8.jpg" data-lb="f" data-caption="Community management à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-8.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/remise-3.jpg" data-lb="f" data-caption="Community management à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/remise-3.jpg" alt="Apprenants en formation" loading="lazy"></a></div></div>
    </div>
    <div class="col-lg-4">
      <aside class="sticky-card reveal">
@endverbatim
        <div class="sc-head"><span class="kicker light mb-2">Tarif · {{ $publicCourse->months }} mois</span><div class="amount">{{ number_format($publicCourse->price, 0, ',', ' ') }} <small style="font-size:1rem">FCFA</small></div><small style="color:#B5BEDD">Paiement en 2 fois : {{ number_format($publicCourse->deposit, 0, ',', ' ') }} FCFA à l'inscription, puis {{ number_format($publicCourse->balance(), 0, ',', ' ') }} FCFA après 1 mois</small></div>
        <div class="sc-body">
          <div class="row-i"><span>Durée</span><b>{{ $publicCourse->months }} mois</b></div>
@verbatim
          <div class="row-i"><span>Rythme</span><b>En ligne et en présentiel</b></div>
          <div class="row-i"><span>Lieu</span><b>Abidjan (Palmeraie) & en ligne</b></div>
          <div class="row-i"><span>Prérequis</span><b>Aucun diplôme exigé</b></div>
          <h6 class="mt-4 mb-2" style="font-size:.85rem">Inclus</h6>
          <ul class="check-list small mb-4"><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat et lettre de recommandation (sous conditions)</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Espace étudiant My EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Kit étudiant et T-shirt EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Coaching personnalisé</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Classes virtuelles</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Opportunités de stage</span></li></ul>
          <a href="/preinscription#cm" class="btn btn-evc w-100 mb-2">Préinscription à cette formation</a>
          <a href="https://wa.me/2250747259507?text=INFORMATION%20Community%20management" target="_blank" rel="noopener" class="btn btn-wa w-100 d-inline-flex justify-content-center align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Poser une question</a>
          <p class="small text-muted-evc text-center mt-3 mb-0">Un conseiller vous oriente avant toute inscription</p>
        </div>
      </aside>
    </div>
  </div></div>
</section>

<section class="section bg-cream pt-5">
  <div class="container"><div class="row g-5">
    <div class="col-lg-4"><span class="kicker reveal">Questions fréquentes</span><h2 class="title reveal d1">Avant de <span class="hl">vous lancer</span></h2></div>
    <div class="col-lg-8 reveal d1"><div class="accordion faq" id="faqF">
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#fq1" aria-expanded="true">Je pars de zéro, est-ce pour moi ?</button></h3><div id="fq1" class="accordion-collapse collapse show" data-bs-parent="#faqF"><div class="accordion-body">Oui. Nos formations sont ouvertes à tous les profils, même sans formation initiale. On commence par les bases, pas à pas.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq2">Comment la formation est-elle organisée ?</button></h3><div id="fq2" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Elle se suit en ligne et en présentiel, avec des ressources accessibles à tout moment dans My EVC.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq3">Comment obtient-on le certificat ?</button></h3><div id="fq3" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Il est délivré après validation du parcours, des projets et du dispositif pratique (RECO 60), avec une lettre de recommandation. Il se mérite : c'est ce qui lui donne de la valeur auprès des employeurs.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq4">Puis-je suivre la formation depuis l'étranger ?</button></h3><div id="fq4" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Oui. Nos apprenants viennent de 18 pays. Les cours, ressources et évaluations sont accessibles dans My EVC. Contact France : +33 6 05 66 88 23.</div></div></div>
    </div></div>
  </div></div>
</section>

<section class="section">
  <div class="container"><span class="kicker reveal">Découvrir aussi</span><h2 class="title mb-4 reveal d1">Nos autres formations</h2><div class="row g-4"><div class="col-md-6 reveal"><article class="f-card" >
  <a href="/formation-design" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/atelier.jpg" alt="Infographie & design graphique"><span class="dur">4 MOIS</span><span class="letter" style="background:var(--orange)">A</span></a>
  <div class="body"><h4><a href="/formation-design" class="text-decoration-none" style="color:inherit">Infographie & design graphique</a></h4><p>Vous aimez les couleurs, les images, les idées ? En 4 mois, apprenez à créer des logos, des affiches et des visuels de niveau professionnel.</p><div class="tools mt-auto"><span>PHOTOSHOP</span><span>ILLUSTRATOR</span><span>INDESIGN</span></div></div>
  <div class="foot"><div><b>185 000 FCFA</b><small>ou 2 × : 95 000 + 90 000 FCFA</small></div><a href="/formation-design" class="go" aria-label="Voir la formation Infographie & design graphique"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div><div class="col-md-6 reveal"><article class="f-card" >
  <a href="/formation-bureautique" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/classe-4.jpg" alt="Bureautique & informatique (GIA)"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--blue)">C</span></a>
  <div class="body"><h4><a href="/formation-bureautique" class="text-decoration-none" style="color:inherit">Bureautique & informatique (GIA)</a></h4><p>Word, Excel, PowerPoint, Google : devenez la personne sur qui tout le bureau peut compter.</p><div class="tools mt-auto"><span>WORD</span><span>EXCEL</span><span>POWERPOINT</span><span>GOOGLE WORKSPACE</span></div></div>
  <div class="foot"><div><b>265 000 FCFA</b><small>ou 2 × : 140 000 + 125 000 FCFA</small></div><a href="/formation-bureautique" class="go" aria-label="Voir la formation Bureautique & informatique (GIA)"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div></div></div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/promo-cm.jpg" alt=""></div>
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
