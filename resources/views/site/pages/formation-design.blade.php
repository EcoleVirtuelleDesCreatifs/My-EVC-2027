@extends('layouts.site')
@section('title', "Infographie & design graphique · EVC — École Virtuelle des Créatifs")
@section('description', "Formation infographie et design graphique à Abidjan : ".$publicCourse->months." mois, ".number_format($publicCourse->price, 0, ',', ' ')." FCFA. Programme, outils, débouchés et inscription à l'EVC.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'formations')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/atelier.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><a href="/formations">Formations</a><span>/</span><span>Design graphique</span></nav>
@endverbatim
    <h1 class="mb-3 reveal">Infographie &amp; design graphique <span>· {{ $publicCourse->months }} mois</span></h1>
    <p class="mb-0 reveal d1">Vous aimez les couleurs, les images, les idées ? En {{ $publicCourse->months }} mois, apprenez à créer des logos, des affiches et des visuels de niveau professionnel.</p>
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
      <ul class="check-list cols mb-5 reveal d2"><li><svg class="ico"><use href="#i-check"/></svg><span>Fondamentaux du design et de la communication visuelle</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Photoshop, Illustrator et InDesign</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Logos et identités visuelles</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Chartes graphiques</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Affiches et supports publicitaires</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Supports imprimés et numériques</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Typographie, composition, couleurs</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Projets professionnels</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Portfolio ou pressbook</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Présentation et défense de projets</span></li></ul>

      <h3 class="h4 mb-3 reveal">Outils maîtrisés</h3>
      <div class="tools-row mb-5 reveal"><div class="tool"><i style="background:#31A8FF;color:#001E36">Ps</i>Photoshop</div><div class="tool"><i style="background:#FF9A00;color:#330000">Ai</i>Illustrator</div><div class="tool"><i style="background:#FF3366;color:#49021F">Id</i>InDesign</div></div>

      <span class="kicker reveal">Programme</span>
      <h2 class="title reveal d1">Le contenu, <span class="hl">module par module</span></h2>
      <div class="accordion module mb-5 reveal" id="mods"><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#mod0" aria-expanded="true"><span class="mn">01</span>Fondamentaux du design</button></h3><div id="mod0" class="accordion-collapse collapse show" data-bs-parent="#mods"><div class="accordion-body">Communication visuelle, lecture d'un brief, règles de composition, couleur et typographie.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod1" aria-expanded="false"><span class="mn">02</span>Photoshop</button></h3><div id="mod1" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Retouche, photomontage, affiches et visuels pour les réseaux sociaux.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod2" aria-expanded="false"><span class="mn">03</span>Illustrator</button></h3><div id="mod2" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Logos, pictogrammes, illustrations vectorielles et identités visuelles.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod3" aria-expanded="false"><span class="mn">04</span>InDesign</button></h3><div id="mod3" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Mise en page de flyers, brochures, catalogues et supports imprimés.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod4" aria-expanded="false"><span class="mn">05</span>Identité & charte graphique</button></h3><div id="mod4" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Concevoir un système visuel cohérent et le documenter.</div></div></div><div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mod5" aria-expanded="false"><span class="mn">06</span>Studio Creative & portfolio</button></h3><div id="mod5" class="accordion-collapse collapse" data-bs-parent="#mods"><div class="accordion-body">Brief réel, jury de professionnels, constitution du portfolio ou pressbook.</div></div></div></div>

      <div class="row g-4 mb-2">
        <div class="col-md-7 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><h5>Débouchés</h5><div class="mt-2"><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Infographiste</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Graphiste</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Designer graphique junior</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Assistant directeur artistique</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Créatif publicitaire</span><span class="job"><svg class="ico"><use href="#i-brief"/></svg>Prestataire indépendant</span></div></div></div>
        <div class="col-md-5 reveal d1"><div class="card-evc" style="background:var(--navy);border-color:var(--navy)"><span class="ic" style="background:rgba(255,255,255,.08);color:var(--gold)"><svg class="ico"><use href="#i-star"/></svg></span><h5 class="text-white">Immersion : Studio Creative</h5><p style="color:#C3CBE7" class="mb-3">Un vrai brief, un vrai délai, un jury de 26 professionnels venus de 7 pays.</p><a href="/studio-creative" class="link-arrow" style="color:var(--gold)">En savoir plus <svg class="ico"><use href="#i-arrow"/></svg></a></div></div>
      </div>
      <div class="mt-5" data-slider><div class="d-flex flex-wrap justify-content-between align-items-end gap-4 mb-4 ">
  <div style="max-width:720px"><span class="kicker reveal">Réalisations d'apprenants</span><h2 class="title mb-2 reveal d1">Ce que vous saurez créer</h2></div>
  <div class="slider-ctrl"><div class="slider-progress me-2 d-none d-md-block"><i></i></div><button data-prev aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button data-next aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button></div>
</div><div class="evc-track"><a href="/assets/site/img/oeuvre-dabali-chaud.jpg" data-lb="f" data-caption="Dabali Chaud, menu — Affiche · Restauration" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-dabali-chaud.jpg" alt="Dabali Chaud, menu" loading="lazy"></a><a href="/assets/site/img/oeuvre-mood-vendredi.jpg" data-lb="f" data-caption="Mood du Vendredi — Affiche · Événementiel" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-mood-vendredi.jpg" alt="Mood du Vendredi" loading="lazy"></a><a href="/assets/site/img/oeuvre-revenu-en-ligne.jpg" data-lb="f" data-caption="Gagner un revenu en ligne — Affiche · Conférence" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-revenu-en-ligne.jpg" alt="Gagner un revenu en ligne" loading="lazy"></a><a href="/assets/site/img/oeuvre-elephant-paix.jpg" data-lb="f" data-caption="65 ans de l'Indépendance — Visuel institutionnel · EVC" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-elephant-paix.jpg" alt="65 ans de l'Indépendance" loading="lazy"></a><a href="/assets/site/img/oeuvre-jeune-priere.jpg" data-lb="f" data-caption="03 jours de jeûne et prière — Affiche · Événement" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-jeune-priere.jpg" alt="03 jours de jeûne et prière" loading="lazy"></a><a href="/assets/site/img/oeuvre-fete-independance.jpg" data-lb="f" data-caption="Bonne fête de l'Indépendance — Visuel institutionnel · EVC" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-fete-independance.jpg" alt="Bonne fête de l'Indépendance" loading="lazy"></a><a href="/assets/site/img/oeuvre-zouglou.jpg" data-lb="f" data-caption="Zouglou Acoustic Live — Affiche en situation" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-zouglou.jpg" alt="Zouglou Acoustic Live" loading="lazy"></a><a href="/assets/site/img/oeuvre-adoration.jpg" data-lb="f" data-caption="Journée d'adoration — Réseaux sociaux" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-adoration.jpg" alt="Journée d'adoration" loading="lazy"></a><a href="/assets/site/img/oeuvre-consommer-local.jpg" data-lb="f" data-caption="Mois du Consommer Local — Campagne institutionnelle" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-consommer-local.jpg" alt="Mois du Consommer Local" loading="lazy"></a><a href="/assets/site/img/oeuvre-force-priere.jpg" data-lb="f" data-caption="La force de la prière — Réseaux sociaux" class="ratio-photo d-block" style="width:min(240px,70vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/oeuvre-force-priere.jpg" alt="La force de la prière" loading="lazy"></a></div></div>
    </div>
    <div class="col-lg-4">
      <aside class="sticky-card reveal">
@endverbatim
        <div class="sc-head"><span class="kicker light mb-2">Tarif · {{ $publicCourse->months }} mois</span><div class="amount">{{ number_format($publicCourse->price, 0, ',', ' ') }} <small style="font-size:1rem">FCFA</small></div><small style="color:#B5BEDD">Paiement en 2 fois : {{ number_format($publicCourse->deposit, 0, ',', ' ') }} FCFA à l'inscription, puis {{ number_format($publicCourse->balance(), 0, ',', ' ') }} FCFA après 2 mois</small></div>
        <div class="sc-body">
          <div class="row-i"><span>Durée</span><b>{{ $publicCourse->months }} mois</b></div>
@verbatim
          <div class="row-i"><span>Rythme</span><b>En ligne et en présentiel</b></div>
          <div class="row-i"><span>Lieu</span><b>Abidjan (Palmeraie) & en ligne</b></div>
          <div class="row-i"><span>Prérequis</span><b>Aucun diplôme exigé</b></div>
          <h6 class="mt-4 mb-2" style="font-size:.85rem">Inclus</h6>
          <ul class="check-list small mb-4"><li><svg class="ico"><use href="#i-check"/></svg><span>Certificat et lettre de recommandation (sous conditions)</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Espace étudiant My EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Kit étudiant et T-shirt EVC</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Coaching personnalisé</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Classes virtuelles</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Opportunités de stage</span></li></ul>
          <a href="/preinscription#design" class="btn btn-evc w-100 mb-2">Préinscription à cette formation</a>
          <a href="https://wa.me/2250747259507?text=INFORMATION%20Design%20graphique" target="_blank" rel="noopener" class="btn btn-wa w-100 d-inline-flex justify-content-center align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Poser une question</a>
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
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq2">Ai-je besoin d'un ordinateur puissant ?</button></h3><div id="fq2" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Un ordinateur capable de faire tourner les logiciels Adobe est recommandé. Notre équipe vous conseille lors de l'orientation.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq3">Comment obtient-on le certificat ?</button></h3><div id="fq3" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Il est délivré après validation du parcours, des projets et du dispositif pratique (Studio Creative), avec une lettre de recommandation. Il se mérite : c'est ce qui lui donne de la valeur auprès des employeurs.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq4">Puis-je suivre la formation depuis l'étranger ?</button></h3><div id="fq4" class="accordion-collapse collapse" data-bs-parent="#faqF"><div class="accordion-body">Oui. Nos apprenants viennent de 18 pays. Les cours, ressources et évaluations sont accessibles dans My EVC. Contact France : +33 6 05 66 88 23.</div></div></div>
    </div></div>
  </div></div>
</section>

<section class="section">
  <div class="container"><span class="kicker reveal">Découvrir aussi</span><h2 class="title mb-4 reveal d1">Nos autres formations</h2><div class="row g-4"><div class="col-md-6 reveal"><article class="f-card" >
  <a href="/formation-community-management" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/promo-cm.jpg" alt="Community management & social media"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--royal)">B</span></a>
  <div class="body"><h4><a href="/formation-community-management" class="text-decoration-none" style="color:inherit">Community management & social media</a></h4><p>Facebook, Instagram, TikTok : apprenez à animer une communauté, à créer des contenus qui marchent et à mesurer vos résultats.</p><div class="tools mt-auto"><span>FACEBOOK</span><span>INSTAGRAM</span><span>TIKTOK</span><span>PUBLICITÉ SOCIALE</span></div></div>
  <div class="foot"><div><b>165 000 FCFA</b><small>ou 2 × : 85 000 + 80 000 FCFA</small></div><a href="/formation-community-management" class="go" aria-label="Voir la formation Community management & social media"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div><div class="col-md-6 reveal"><article class="f-card" >
  <a href="/formation-bureautique" class="media d-block"><img loading="lazy" decoding="async" src="/assets/site/img/classe-4.jpg" alt="Bureautique & informatique (GIA)"><span class="dur">2 MOIS</span><span class="letter" style="background:var(--blue)">C</span></a>
  <div class="body"><h4><a href="/formation-bureautique" class="text-decoration-none" style="color:inherit">Bureautique & informatique (GIA)</a></h4><p>Word, Excel, PowerPoint, Google : devenez la personne sur qui tout le bureau peut compter.</p><div class="tools mt-auto"><span>WORD</span><span>EXCEL</span><span>POWERPOINT</span><span>GOOGLE WORKSPACE</span></div></div>
  <div class="foot"><div><b>265 000 FCFA</b><small>ou 2 × : 140 000 + 125 000 FCFA</small></div><a href="/formation-bureautique" class="go" aria-label="Voir la formation Bureautique & informatique (GIA)"><svg class="ico"><use href="#i-arrow"/></svg></a></div>
</article></div></div></div>
</section>

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
