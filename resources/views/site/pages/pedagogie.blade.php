@extends('layouts.site')
@section('title', "Pédagogie · EVC — École Virtuelle des Créatifs")
@section('description', "Le modèle pédagogique ultra-pratique de l'EVC : apprendre, pratiquer, produire, démontrer. Parcours en 8 étapes, plateforme My EVC et politique de certification.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'pedagogie')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/classe-large.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Pédagogie</span></nav>
    <h1 class="mb-3 reveal">Apprendre. Pratiquer. Produire. <span>Démontrer.</span></h1>
    <p class="mb-0 reveal d1">Apprendre un métier, pas seulement un logiciel. Ici, vous n'êtes pas spectateur : vous êtes acteur de votre réussite.</p>
    
  </div>
</header>
<section class="section" id="modele">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:760px"><span class="kicker center reveal">Notre modèle pédagogique</span><h2 class="title reveal d1">Quatre temps pour <span class="hl">devenir professionnel</span></h2></div>
    <div class="row g-4 steps-line mb-5">
      <div class="col-6 col-md-3 step reveal"><div class="sn">1</div><h5>Apprendre</h5><p>Acquérir les connaissances.</p></div>
      <div class="col-6 col-md-3 step reveal d1"><div class="sn">2</div><h5>Pratiquer</h5><p>Maîtriser les outils.</p></div>
      <div class="col-6 col-md-3 step reveal d2"><div class="sn">3</div><h5>Produire</h5><p>Livrer des résultats.</p></div>
      <div class="col-6 col-md-3 step reveal d3"><div class="sn">4</div><h5>Démontrer</h5><p>Présenter et défendre.</p></div>
    </div>
    <div class="row g-5 align-items-center">
      <div class="col-lg-5 reveal"><div class="photo-stack"><div class="main"><img loading="lazy" decoding="async" src="/assets/site/img/classe-6.jpg" alt="Atelier à l'EVC"></div><div class="sub"><img loading="lazy" decoding="async" src="/assets/site/img/classe-5.jpg" alt="Travail en groupe"></div></div></div>
      <div class="col-lg-7">
        <span class="kicker reveal">Apprendre un métier, pas seulement un logiciel</span>
        <h2 class="title reveal d1">Vous travaillez comme <span class="hl">un vrai professionnel</span></h2>
        <p class="lead-evc reveal d2">Savoir ouvrir Photoshop ou Excel ne suffit pas. Il faut savoir quoi faire, comment s'organiser et comment livrer un travail qui satisfait le client. C'est exactement ce que nous enseignons.</p>
        <div class="row g-2 reveal"><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Analyser besoins et problématiques</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Interpréter des briefs</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Rechercher des solutions adaptées</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Planifier leurs activités</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Respecter les délais</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Utiliser les outils professionnels</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Présenter et défendre leurs travaux</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Intégrer les retours et corriger</span></div></div><div class="col-sm-6 col-lg-4"><div class="d-flex gap-2 align-items-center p-3 bg-white rounded-3 h-100" style="border:1px solid var(--line)"><span style="color:var(--ok)"><svg class="ico"><use href="#i-check"/></svg></span><span class="small fw-semibold">Mesurer et documenter les résultats</span></div></div></div>
        <div class="p-4 rounded-4 mt-4 reveal" style="background:var(--gold)"><b style="color:var(--navy)">Un suivi à chaque étape.</b> <span style="color:var(--navy)">Exercices, projets, évaluations : vous avancez pas à pas, avec un retour à chaque étape.</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-cream" id="parcours">
  <div class="container"><div class="row g-5">
    <div class="col-lg-5"><div class="position-sticky" style="top:120px">
      <span class="kicker reveal">Orientation et parcours de l'étudiant</span>
      <h2 class="title reveal d1">Un parcours accompagné <span class="hl">jusqu'à la certification</span></h2>
      <p class="lead-evc reveal d2">Vous n'êtes jamais seul. De votre premier appel à votre certificat, nous vous accompagnons en huit étapes.</p>
      <div class="ratio-photo mt-4 reveal" style="aspect-ratio:16/9"><img loading="lazy" decoding="async" src="/assets/site/img/ceremonie.jpg" alt="Promotion certifiée de l'EVC"></div>
    </div></div>
    <div class="col-lg-7 reveal d1"><div class="bg-white rounded-4 p-4 p-lg-5" style="box-shadow:var(--shadow-sm)"><div class="timeline"><div class="tl-item" data-n="1"><h6>Information et préinscription</h6><p>Découverte des programmes, des conditions d'admission, des modalités et des débouchés.</p></div><div class="tl-item" data-n="2"><h6>Analyse du profil et orientation</h6><p>Échange sur le niveau, les motivations et les objectifs. Le dispositif SAOP évalue les prédispositions.</p></div><div class="tl-item" data-n="3"><h6>Admission et intégration</h6><p>L'étudiant reçoit son programme, son planning et l'accès à son environnement pédagogique.</p></div><div class="tl-item" data-n="4"><h6>Formation et compétences</h6><p>Séances, exercices, ressources pédagogiques et accompagnement des formateurs.</p></div><div class="tl-item" data-n="5"><h6>Travaux pratiques et projets</h6><p>Missions et activités pratiques vérifient l'application des connaissances.</p></div><div class="tl-item" data-n="6"><h6>Immersion professionnelle</h6><p>Selon la formation suivie : Studio Creative ou RECO 60.</p></div><div class="tl-item" data-n="7"><h6>Évaluation finale et éligibilité</h6><p>Résultats pédagogiques et critères institutionnels déterminent l'éligibilité à la certification.</p></div><div class="tl-item" data-n="8"><h6>Valorisation professionnelle</h6><p>Portfolio, réseau, présentation des réalisations et recherche d'opportunités.</p></div></div></div></div>
  </div></div>
</section>

<section class="section" id="myevc">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-6">
      <span class="kicker reveal">My EVC · notre écosystème numérique</span>
      <h2 class="title reveal d1">Votre école, <span class="hl">partout avec vous</span></h2>
      <p class="lead-evc reveal d2">My EVC, c'est votre école dans la poche : formations, devoirs, notes et messages, accessibles partout et à tout moment.</p>
      <div class="row g-3 my-3">
        <div class="col-sm-6 reveal"><div class="card-evc p-3"><b class="d-block mb-1" style="color:var(--navy)">Pour les étudiants</b><small class="text-muted-evc">Ressources, plannings, dépôt des réalisations, évaluations, suivi de progression.</small></div></div>
        <div class="col-sm-6 reveal d1"><div class="card-evc p-3"><b class="d-block mb-1" style="color:var(--navy)">Pour les formateurs</b><small class="text-muted-evc">Organisation pédagogique, suivi des activités, attribution de travaux et évaluation.</small></div></div>
        <div class="col-sm-6 reveal"><div class="card-evc p-3"><b class="d-block mb-1" style="color:var(--navy)">Pour l'institution</b><small class="text-muted-evc">Centralisation, traçabilité et gestion de l'éligibilité à la certification.</small></div></div>
        <div class="col-sm-6 reveal d1"><div class="card-evc p-3"><b class="d-block mb-1" style="color:var(--navy)">Après le parcours</b><small class="text-muted-evc">Accès en lecture seule au compte et aux ressources archivées.</small></div></div>
      </div>
      <p class="small reveal"><span class="chip">Bientôt</span> Messagerie, suivi encore plus précis, gestion des documents et assistant basé sur l'intelligence artificielle.</p>
    </div>
    <div class="col-lg-6 reveal d1">
      <div class="mx-auto position-relative" style="max-width:520px">
        <div class="rounded-4 p-3" style="background:var(--navy);box-shadow:var(--shadow)">
          <div class="d-flex gap-2 mb-3"><i style="width:10px;height:10px;border-radius:50%;background:#ff5f57"></i><i style="width:10px;height:10px;border-radius:50%;background:#febc2e"></i><i style="width:10px;height:10px;border-radius:50%;background:#28c840"></i></div>
          <div class="bg-white rounded-3 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3"><div><small class="text-muted-evc">Bonjour 👋</small><h5 class="mb-0">Mon parcours · Design graphique</h5></div><img loading="lazy" decoding="async" src="/assets/site/img/logo.png" alt="" style="height:30px"></div>
            <div class="mb-3"><div class="d-flex justify-content-between small fw-semibold"><span>Progression</span><span>78 %</span></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:78%;background:var(--orange)"></div></div></div>
            <div class="mb-3"><div class="d-flex justify-content-between small fw-semibold"><span>Projets livrés</span><span>9 / 12</span></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:75%;background:var(--royal)"></div></div></div>
            <div class="mb-3"><div class="d-flex justify-content-between small fw-semibold"><span>Assiduité</span><span>96 %</span></div><div class="progress" style="height:8px"><div class="progress-bar" style="width:96%;background:var(--ok)"></div></div></div>
            <div class="d-flex gap-2 align-items-center p-2 rounded-3 small" style="background:var(--cream)"><span style="color:var(--orange)"><svg class="ico"><use href="#i-award"/></svg></span>Votre affiche a été validée par le formateur.</div>
          </div>
        </div>
        <p class="small text-muted-evc text-center mt-2 mb-0">Illustration de l'interface (données fictives)</p>
      </div>
    </div>
  </div></div>
</section>

<section class="section bg-cream" id="certification">
  <div class="container">
    <div class="row g-5 align-items-center mb-5">
      <div class="col-lg-6">
        <span class="kicker reveal">Notre politique de certification</span>
        <h2 class="title reveal d1">Un certificat <span class="hl">qui a de la valeur</span></h2>
        <p class="lead-evc reveal d2">Ici, le certificat ne s'achète pas et ne se donne pas : il se mérite. C'est pour cela qu'il a de la valeur aux yeux des employeurs.</p>
        <div class="feature-row reveal"><span class="n">01</span><div><b>La régularité administrative et financière</b><span>Satisfaire aux obligations prévues dans le cadre de la formation.</span></div></div><div class="feature-row reveal"><span class="n">02</span><div><b>La validation des acquis pédagogiques</b><span>Travaux pratiques, exercices, projets et évaluations obligatoires validés.</span></div></div><div class="feature-row reveal"><span class="n">03</span><div><b>L'assiduité et l'engagement</b><span>Ponctualité, participation, régularité et implication.</span></div></div><div class="feature-row reveal"><span class="n">04</span><div><b>La validation des dispositifs pratiques</b><span>Selon la spécialité : Studio Creative, RECO ou tout autre dispositif applicable.</span></div></div><div class="feature-row reveal"><span class="n">05</span><div><b>Le respect du règlement intérieur</b><span>Discipline, éthique et comportement professionnel.</span></div></div>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-12 reveal"><div class="card-evc" style="background:var(--navy);border-color:var(--navy)"><span class="kicker light mb-2">Notre principe</span><h4 class="text-white mb-0">On ne certifie pas simplement un parcours. <span class="hl">On certifie des compétences.</span></h4></div></div>
          <div class="col-sm-6 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-doc"/></svg></span><h5>Un examen individualisé</h5><p>Au terme du parcours, chaque dossier est examiné par l'algorithme de My EVC. Les éligibles sont informés par e-mail ou dans leur espace.</p></div></div>
          <div class="col-sm-6 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-shield"/></svg></span><h5>Qualité et responsabilité</h5><p>Une reconnaissance crédible des compétences, et non une garantie d'emploi : maîtrise technique, réalisations, comportement responsable.</p></div></div>
          <div class="col-12 reveal"><a href="/jury-certification#verifier" class="reg-link"><svg class="ico"><use href="#i-shield"/></svg><span><b>Registre officiel de vérification</b><small>Employeurs : vérifiez l'authenticité d'un certificat EVC en ligne</small></span><svg class="ico"><use href="#i-arrow"/></svg></a></div>
        </div>
      </div>
    </div>
    <div data-slider><div class="d-flex flex-wrap justify-content-between align-items-end gap-4 mb-4 ">
  <div style="max-width:720px"><span class="kicker reveal">Certificats et lettres de recommandation</span><h2 class="title mb-2 reveal d1">Promo 2026 : <span class='hl'>ils l'ont mérité</span></h2></div>
  <div class="slider-ctrl"><div class="slider-progress me-2 d-none d-md-block"><i></i></div><button data-prev aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button data-next aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button></div>
</div><div class="evc-track"><a href="/assets/site/img/certif-1.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-1.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-2.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-2.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-3.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-3.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-4.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-4.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-5.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-5.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-6.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-6.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-7.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-7.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-8.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-8.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-9.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-9.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-10.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-10.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-11.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-11.jpg" alt="Remise de certificat" loading="lazy"></a><a href="/assets/site/img/certif-12.jpg" data-lb="cert" data-caption="Remise des certificats · Promo 2026" class="ratio-photo d-block" style="width:min(200px,60vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-12.jpg" alt="Remise de certificat" loading="lazy"></a></div></div>
  </div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/remise-1.jpg" alt=""></div>
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
