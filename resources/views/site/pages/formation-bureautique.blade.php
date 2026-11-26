@extends('layouts.site')
@section('title', "Bureautique & informatique (GIA) · EVC — École Virtuelle des Créatifs")
@section('description', "Formation bureautique à Abidjan : ".$publicCourse->months." mois, ".number_format($publicCourse->price, 0, ',', ' ')." FCFA. Word, Excel, PowerPoint, programme et inscription à l'EVC.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'formations')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/classe-4.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><a href="/formations">Formations</a><span>/</span><span>Bureautique GIA</span></nav>
@endverbatim
    <h1 class="mb-3 reveal">Bureautique &amp; informatique (GIA) <span>· {{ $publicCourse->months }} mois</span></h1>
    <p class="mb-0 reveal d1">Word, Excel, PowerPoint, Google : devenez la personne sur qui tout le bureau peut compter.</p>
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
      <ul class="check-list cols mb-5 reveal d2"><li><svg class="ico"><use href="#i-check"/></svg><span>Environnement informatique</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Organisation des fichiers et dossiers</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Traitement de texte avec Word</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Données et analyse avec Excel</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Présentations PowerPoint</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Services Google en entreprise</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Courriels et communication</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Documents administratifs</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Outils collaboratifs et productivité</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Organisation de l'information</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Initiation à Canva et aux outils numériques</span></li></ul>

      <h3 class="h4 mb-3 reveal">Outils maîtrisés</h3>
      <div class="tools-row mb-5 reveal"><div class="tool"><i style="background:#2B579A;color:#fff">W</i>Word</div><div class="tool"><i style="background:#217346;color:#fff">X</i>Excel</div><div class="tool"><i style="background:#D24726;color:#fff">P</i>PowerPoint</div><div class="tool"><i style="background:#4285F4;color:#fff">G</i>Google Workspace</div><div class="tool"><i style="background:#00C4CC;color:#fff">C</i>Canva</div></div>

      <span class="kicker reveal">Programme</span>
      <h2 class="title reveal d1">Le contenu, <span class="hl">module par module</span></h2>
      <div class="accordion module mb-5 reveal" id="mods"><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mod0" aria-expanded="true"><span class="mn">01</span>Environnement informatique</button></h3><div id="mod0" class="accordion-collapse collapse show" data-bs-parent="#mods"><div class="accordion-body">Système, fichiers, dossiers, sauvegarde et bonnes pratiques.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod1" aria-expanded="false"><span class="mn">02</span>Word</button></h3><div id="mod1" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Documents administratifs, courriers, rapports et mise en forme professionnelle.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod2" aria-expanded="false"><span class="mn">03</span>Excel</button></h3><div id="mod2" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Tableaux, formules, tableaux de suivi, analyse et graphiques.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod3" aria-expanded="false"><span class="mn">04</span>PowerPoint & Canva</button></h3><div id="mod3" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Présentations claires et visuels simples et efficaces.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod4" aria-expanded="false"><span class="mn">05</span>Google & collaboration</button></h3><div id="mod4" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Gmail, Drive, Docs, Sheets, agendas et travail d'équipe.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod5" aria-expanded="false"><span class="mn">06</span>RECO 60</button></h3><div id="mod5" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Organiser des documents, structurer des données, améliorer des processus réels.</div></div></div></div>

      <div class="row g-4 mb-2">
        <div class="col-md-7 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><h5>Débouchés</h5><div class="mt-2"><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Assistant administratif</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Agent de bureau</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Assistant polyvalent</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Soutien informatique</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Fonctions exigeant la maîtrise des outils numériques</span></div></div></div>
        <div class="col-md-5 reveal d1"><div class="card-evc" style="background:var(--navy);border-color:var(--navy)"><span class="ic" style="background:rgba(255,255,255,.08);color:var(--gold)"><svg class="ico"><use href="#i-star"/></svg></span><h5 class="text-white">Immersion : RECO 60</h5><p style="color:#C3CBE7" class="mb-3">60 jours pour améliorer des processus réels : tableaux de suivi, données, présentations.</p><a href="/studio-creative#reco60" class="link-arrow" style="color:var(--gold)">En savoir plus <svg class="ico"><use href="#i-arrow"/></svg></a></div></div>
      </div>
      <div class="mt-5" data-slider><div class="d-flex flex-wrap justify-content-between align-items-end gap-4 mb-4 ">
  <div style="max-width:720px"><span class="kicker reveal">En images</span><h2 class="title mb-2 reveal d1">La formation en pratique</h2></div>
  <div class="slider-ctrl"><div class="slider-progress me-2 d-none d-md-block"><i></i></div><button data-prev aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button data-next aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button></div>
</div><div class="evc-track"><a href="/assets/site/img/classe-4.jpg" data-lb="f" data-caption="Bureautique GIA à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-4.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-3.jpg" data-lb="f" data-caption="Bureautique GIA à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-3.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-7.jpg" data-lb="f" data-caption="Bureautique GIA à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-7.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/classe-1.jpg" data-lb="f" data-caption="Bureautique GIA à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/classe-1.jpg" alt="Apprenants en formation" loading="lazy"></a><a href="/assets/site/img/remise-1.jpg" data-lb="f" data-caption="Bureautique GIA à l’EVC" class="ratio-photo d-block" style="width:min(300px,75vw);aspect-ratio:4/3;border-radius:1rem"><img decoding="async" src="/assets/site/img/remise-1.jpg" alt="Apprenants en formation" loading="lazy"></a></div></div>
    </div>
    <div class="col-lg-4">
      <aside class="sticky-card reveal">
@endverbatim
        <div class="sc-head"><span class="kicker light mb-2">Tarif · {{ $publicCourse->months }} mois</span><div class="amount">{{ number_format($publicCourse->price, 0, ',', ' ') }} <small style="font-size:1rem">FCFA</small></div><small style="color:#B5BEDD">Paiement en 2 fois : {{ number_format($publicCourse->deposit, 0, ',', ' ') }} FCFA à l'inscription, puis {{ number_format($publicCourse->balance(), 0, ',', ' ') }} FCFA en cours de formation</small></div>
        <div class="sc-body">
          <div class="row-i"><span>Durée</span><b>{{ $publicCourse->months }} mois</b></div>
@verbatim
          <div class="row-i"><span>Rythme</span><b>En ligne et en présentiel</b></div>
          <div class="row-i"><span>Lieu</span><b>Abidjan (Palmeraie) & en ligne</b></div>
          <div class="row-i"><span>Prérequis</span><b>Aucun diplôme exigé</b></div>
          <h6 class="mt-4 mb-2" style="font-size:.85rem">Inclus</h6>
          <ul class="check-list small mb-4"><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat et lettre de recommandation (sous conditions)</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Espace étudiant My EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Kit étudiant et T-shirt EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Coaching personnalisé</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Classes virtuelles</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Opportunités de stage</span></li></ul>
          <a href="/preinscription#gia" class="btn btn-evc w-100 mb-2">Préinscription à cette formation</a>
          <a href="https://wa.me/2250747259507?text=INFORMATION%20Bureautique%20GIA" target="_blank" rel="noopener" class="btn btn-wa w-100 d-inline-flex justify-content-center align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Poser une question</a>
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
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq2">Je ne suis pas à l'aise avec un ordinateur, est-ce un problème ?</button></h3><div id="fq2" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Non. Le programme commence par l'environnement informatique et l'organisation des fichiers, pas à pas.</div></div></div>
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
  <a href="/formation-community-management" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/promo-cm.jpg" alt="Community management & social media"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--royal)">B</span></a>
  <div class="body"><h4><a href="/formation-community-management" class="text-decoration-none" style="color:inherit">Community management & social media</a></h4><p>Facebook, Instagram, TikTok : apprenez à animer une communauté, à créer des contenus qui marchent et à mesurer vos résultats.</p><div class="tools mt-auto"><span>FACEBOOK</span><span>INSTAGRAM</span><span>TIKTOK</span><span>PUBLICITÉ SOCIALE</span></div></div>
  <div class="foot"><div><b>165 000 FCFA</b><small>ou 2 × : 85 000 + 80 000 FCFA</small></div><a href="/formation-community-management" class="go" aria-label="Voir la formation Community management & social media"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div></div></div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/classe-4.jpg" alt=""></div>
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
