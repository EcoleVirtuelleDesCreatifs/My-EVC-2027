@extends('layouts.site')
@section('title', "L'école · EVC — École Virtuelle des Créatifs")
@section('description', "Présentation de l'École Virtuelle des Créatifs : histoire, mot du fondateur Bilé Bossombra, vision, valeurs, gouvernance, rayonnement dans 18 pays et Cap 2027.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'ecole')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/ceremonie-2.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>L'école</span></nav>
    <h1 class="mb-3 reveal">Une école ivoirienne, <span>ouverte sur le monde</span></h1>
    <p class="mb-0 reveal d1">Depuis 2022, l'École Virtuelle des Créatifs forme aux métiers du numérique avec une conviction : chacun a un talent, et ce talent peut devenir un métier.</p>
    
  </div>
</header><div class="page-tabs"><div class="container"><nav class="nav"><a class="nav-link active" href="#presentation">Présentation</a><a class="nav-link" href="#fondateur">Fondateur</a><a class="nav-link" href="#vision">Vision & mission</a><a class="nav-link" href="#valeurs">Valeurs</a><a class="nav-link" href="#gouvernance">Gouvernance</a><a class="nav-link" href="#rayonnement">Rayonnement</a><a class="nav-link" href="#engagement">Engagement</a><a class="nav-link" href="#cap2027">Cap 2027</a></nav></div></div>

<!-- PRÉSENTATION -->
<section class="section" id="presentation">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-6">
      <span class="kicker reveal">Présentation générale</span>
      <h2 class="title reveal d1">Une école née pour <span class="hl">révéler les talents</span></h2>
      <p class="lead-evc reveal d2">L'École Virtuelle des Créatifs (EVC) forme aux métiers du numérique : design graphique, réseaux sociaux et informatique. En ligne et en présentiel, elle accueille étudiants, jeunes diplômés, salariés, entrepreneurs et personnes en reconversion.</p>
      <div class="p-4 rounded-4 my-4 reveal" style="background:var(--cream);border-left:4px solid var(--orange)"><span class="kicker mb-2">Notre conviction</span><p class="mb-0 fw-semibold" style="color:var(--navy);font-size:1.1rem">Une bonne formation ne se mesure pas aux heures passées en présentiel, mais à ce que vous savez faire en sortant.</p></div>
      <p class="reveal"><b style="color:var(--navy)">Notre positionnement :</b> <span class="text-muted-evc">une école 100 % ultra-pratique, où l'on apprend un métier en le faisant, comme en entreprise.</span></p>
    </div>
    <div class="col-lg-6 reveal d1">
      <div class="card-evc p-0 overflow-hidden" style="box-shadow:var(--shadow)">
        <div class="p-4" style="background:var(--navy)"><h5 class="text-white mb-0 d-flex gap-2 align-items-center"><svg class="ico"><use href="#i-doc"/></svg> Fiche d'identité</h5></div>
        <div class="p-4"><div class="row g-3 small">
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">DÉNOMINATION</span><b>École Virtuelle des Créatifs (EVC)</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">ANNÉE DE CRÉATION</span><b>2022</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">STATUT JURIDIQUE</span><b>SARL de droit ivoirien</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">SIÈGE</span><b>Abidjan, Côte d'Ivoire</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">LIEU DE FORMATION</span><b>Bloom Square Akwaba, Palmeraie, Carrefour Guiraud</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">MODALITÉS</span><b>En ligne et en présentiel</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">SECTEUR</span><b>Formation professionnelle : numérique, création graphique, communication digitale, informatique</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">FONDATEUR & DG</span><b>Bilé Bossombra</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">PLATEFORME</span><b>My EVC</b></div>
          <div class="col-6"><span class="text-muted-evc d-block" style="font-size:.7rem;letter-spacing:.12em">SITE OFFICIEL</span><b>ecolevirtuelledescreatifs.com</b></div>
        </div></div>
      </div>
    </div>
  </div></div>
</section>

<!-- FONDATEUR -->
<section class="section bg-cream" id="fondateur">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-5 reveal">
      <div class="position-relative" style="max-width:440px">
        <div class="ratio-photo" style="aspect-ratio:4/5;box-shadow:var(--shadow)"><img loading="lazy" decoding="async" src="/assets/site/img/fondateur.jpg" alt="Bilé Bossombra, fondateur et directeur général de l'EVC"></div> 
      </div>
    </div>
    <div class="col-lg-7">
      <span class="kicker reveal">Mot du fondateur et directeur général</span>
      <h2 class="title reveal d1">Former autrement pour préparer <span class="hl">les professionnels de demain</span></h2>
      <blockquote class="reveal d2 my-4 ps-4" style="border-left:4px solid var(--orange);font-size:1.25rem;font-weight:600;color:var(--navy);line-height:1.5">« L'avenir numérique de l'Afrique se construit dès aujourd'hui, à travers les compétences que nous développons et les opportunités que nous contribuons à créer. »</blockquote>
      <div class="text-muted-evc reveal">
        <p>Le monde professionnel connaît une profonde transformation. Les entreprises recherchent désormais des collaborateurs qui savent utiliser les outils avec méthode, intelligence, créativité et efficacité. Dès la création de l'EVC, nous avons fait un choix : placer la compétence pratique au cœur de notre modèle pédagogique.</p>
        <p>Nous sommes convaincus que le potentiel d'un individu ne doit pas être limité par son parcours initial, son niveau académique ou sa situation géographique. Avec une orientation adaptée, une formation exigeante et un accompagnement approprié, chaque talent peut développer des compétences utiles à son avenir.</p>
        <p class="mb-0">Notre ambition est aussi de valoriser les compétences africaines. L'Afrique dispose d'un potentiel créatif et numérique considérable ; pour le transformer en opportunités, il faut investir dans une formation adaptée aux réalités du marché et fondée sur des standards professionnels exigeants.</p>
      </div>
    </div>
  </div></div>
</section>

<!-- VISION -->
<section class="section" id="vision">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:820px"><span class="kicker center reveal">Vision, mission, ambition</span><h2 class="title reveal d1">Ce qui nous guide et <span class="hl">ce que nous construisons</span></h2></div>
    <div class="row g-4">
      <div class="col-lg-4 reveal"><div class="card-evc" style="background:var(--navy);border-color:var(--navy)"><span class="ic" style="background:rgba(255,255,255,.08);color:var(--gold)"><svg class="ico"><use href="#i-eye"/></svg></span><h5 class="text-white">Notre vision</h5><p style="color:#C3CBE7">Devenir l'école de référence ultra-pratique en Afrique pour apprendre les métiers du numérique, reconnue pour la qualité de ses formations et la réussite de ses apprenants.</p></div></div>
      <div class="col-lg-4 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-target"/></svg></span><h5>Notre mission</h5><p class="mb-3">Former, accompagner et faire briller les talents, avec des compétences qu'ils peuvent utiliser dès le lendemain au travail.</p><ul class="check-list small"><li><svg class="ico"><use href="#i-check"/></svg><span>Rendre les formations digitales accessibles à tous.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Enseigner ce que les entreprises recherchent vraiment.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Développer l'autonomie, la créativité et le goût du résultat.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Apprendre à travers des projets concrets.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Préparer chacun à la vie en entreprise.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Mettre en relation les talents et les entreprises.</span></li><li><svg class="ico"><use href="#i-check"/></svg><span>Faire rayonner les talents africains du numérique.</span></li></ul></div></div>
      <div class="col-lg-4 reveal d2"><div class="card-evc" style="background:var(--orange);border-color:var(--orange)"><span class="ic" style="background:rgba(255,255,255,.35);color:var(--navy)"><svg class="ico"><use href="#i-rocket"/></svg></span><h5>Notre ambition</h5><p style="color:var(--navy)">Créer un lieu où l'on apprend, où l'on pratique, où l'on est évalué avec justesse, et d'où l'on repart avec de vraies chances de réussir.</p></div></div>
    </div>
  </div>
</section>

<!-- VALEURS -->
<section class="section bg-cream" id="valeurs">
  <div class="container">
    <div class="row align-items-end mb-5 g-4"><div class="col-lg-7"><span class="kicker reveal">Nos valeurs fondamentales</span><h2 class="title mb-0 reveal d1">Sept valeurs simples, <span class="hl">vécues chaque jour</span></h2></div><div class="col-lg-5"><p class="lead-evc mb-0 reveal d2">Les principes qui guident notre action avec nos apprenants, nos formateurs et nos partenaires.</p></div></div>
    <div class="row g-4"><div class="col-md-6 col-lg-3 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-star"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">01</span><h5>L'excellence</h5><p>Rigueur, qualité du travail, professionnalisme et amélioration permanente.</p></div></div><div class="col-md-6 col-lg-3 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-bulb"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">02</span><h5>L'innovation</h5><p>Les outils, méthodes et technologies utiles aux professionnels de demain.</p></div></div><div class="col-md-6 col-lg-3 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-target"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">03</span><h5>La compétence</h5><p>La maîtrise réelle des connaissances, des méthodes et des outils.</p></div></div><div class="col-md-6 col-lg-3 reveal d3"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">04</span><h5>La pratique</h5><p>Expérimentation, livrables, exercices professionnels et projets concrets.</p></div></div><div class="col-md-6 col-lg-3 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-shield"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">05</span><h5>La responsabilité</h5><p>Discipline, ponctualité, autonomie et sens des engagements.</p></div></div><div class="col-md-6 col-lg-3 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-globe"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">06</span><h5>L'accessibilité</h5><p>Une formation ouverte à tous les profils grâce au modèle hybride.</p></div></div><div class="col-md-6 col-lg-3 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-heart"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">07</span><h5>L'accompagnement</h5><p>Écoute, suivi pédagogique et progression individuelle au cœur de l'expérience.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal d3"><div class="ratio-photo h-100" style="min-height:240px"><img loading="lazy" decoding="async" src="/assets/site/img/atelier.jpg" alt="Atelier pratique à l'EVC"></div></div>
    </div>
  </div>
</section>

<!-- GOUVERNANCE -->
<section class="section" id="gouvernance">
  <div class="container">
    <div class="row g-5 align-items-center mb-5">
      <div class="col-lg-6 reveal"><div class="ratio-photo" style="aspect-ratio:16/9;box-shadow:var(--shadow)"><img loading="lazy" decoding="async" src="/assets/site/img/direction.jpg" alt="La direction de l'EVC"></div><p class="small text-muted-evc mt-2 mb-0">La direction de l'EVC, à votre écoute pour bâtir des partenariats durables.</p></div>
      <div class="col-lg-6"><span class="kicker reveal">Organisation et gouvernance</span><h2 class="title reveal d1">Trois directions, <span class="hl">une même exigence de qualité</span></h2><p class="lead-evc reveal d2">Une organisation claire, des responsabilités définies et des fonctions d'appui au service de chaque apprenant.</p>
        <div class="d-flex flex-wrap gap-2 reveal"><span class="chip">Formateurs</span><span class="chip">Relations étudiants</span><span class="chip">Numérique et My EVC</span><span class="chip">Amélioration continue</span></div></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4 reveal"><div class="card-evc"><span class="num-badge">01</span><h5>Direction générale</h5><p class="fw-semibold mb-2" style="color:var(--orange)">Elle fixe le cap.</p><p>Définit la stratégie et les grandes orientations, pilote le développement, supervise les activités et construit les partenariats.</p></div></div>
      <div class="col-md-4 reveal d1"><div class="card-evc"><span class="num-badge">02</span><h5>Direction pédagogique</h5><p class="fw-semibold mb-2" style="color:var(--orange)">Elle garantit la qualité de la formation.</p><p>Conçoit et coordonne les programmes, encadre les formateurs et suit chaque apprenant, des évaluations jusqu'à la certification.</p></div></div>
      <div class="col-md-4 reveal d2"><div class="card-evc"><span class="num-badge">03</span><h5>Création et imprimerie</h5><p class="fw-semibold mb-2" style="color:var(--orange)">Elle donne vie aux talents.</p><p>Supervise la production créative et l'impression, pilote les projets pratiques et veille à la qualité des réalisations.</p></div></div>
    </div>
  </div>
</section>

<!-- RAYONNEMENT -->
<section class="section bg-navy" id="rayonnement">
  <div class="container">
    <div class="row g-5 align-items-center mb-5">
      <div class="col-lg-6"><span class="kicker light reveal">Une institution à rayonnement panafricain</span><h2 class="title text-white reveal d1">Des compétences <span class="hl">sans frontières</span></h2><p class="reveal d2" style="color:#B5BEDD">De la Côte d'Ivoire au Canada, de la France à l'Inde, nos apprenants se forment où qu'ils soient. Le talent n'a pas de frontières.</p></div>
      <div class="col-lg-6"><div class="row g-3 text-center">
        <div class="col-4 reveal"><div class="stat"><b data-count="18">0</b><span>pays représentés</span></div></div>
        <div class="col-4 reveal d1"><div class="stat"><b data-count="4">0</b><span>continents</span></div></div>
        <div class="col-4 reveal d2"><div class="stat"><b><span class="plus">+</span><span data-count="2042">0</span></b><span>apprenants</span></div></div>
      </div></div>
    </div>
    <div class="reveal"><div class="ctry-wrap dark"><div class="ctry-group"><h6>Afrique <small>12 pays</small></h6><div class="ctry-list"><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Côte d'Ivoire"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#F77F00"/><rect x="20.0" width="20.5" height="60" fill="#fff"/><rect x="40.0" width="20.5" height="60" fill="#009E60"/></svg></span><span>Côte d'Ivoire</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Bénin"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="60" height="60" fill="#FCD116"/><rect y="30" width="60" height="30" fill="#E8112D"/><rect width="24" height="60" fill="#008751"/></svg></span><span>Bénin</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Burkina Faso"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="30.5" fill="#EF2B2D"/><rect y="30.0" width="60" height="30.5" fill="#009E49"/><polygon points="30.0,20.0 27.5,26.6 20.5,26.9 26.0,31.3 24.1,38.1 30.0,34.2 35.9,38.1 34.0,31.3 39.5,26.9 32.5,26.6" fill="#FCD116"/></svg></span><span>Burkina Faso</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Gabon"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="20.5" fill="#009E60"/><rect y="20.0" width="60" height="20.5" fill="#FCD116"/><rect y="40.0" width="60" height="20.5" fill="#3A75C4"/></svg></span><span>Gabon</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Ghana"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="20.5" fill="#CE1126"/><rect y="20.0" width="60" height="20.5" fill="#FCD116"/><rect y="40.0" width="60" height="20.5" fill="#006B3F"/><polygon points="30.0,22.0 28.0,27.3 22.4,27.5 26.8,31.0 25.3,36.5 30.0,33.4 34.7,36.5 33.2,31.0 37.6,27.5 32.0,27.3" fill="#000"/></svg></span><span>Ghana</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Guinée"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#CE1126"/><rect x="20.0" width="20.5" height="60" fill="#FCD116"/><rect x="40.0" width="20.5" height="60" fill="#009460"/></svg></span><span>Guinée</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Mali"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#14B53A"/><rect x="20.0" width="20.5" height="60" fill="#FCD116"/><rect x="40.0" width="20.5" height="60" fill="#CE1126"/></svg></span><span>Mali</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Maroc"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="60" height="60" fill="#C1272D"/><polygon points="30,17 33.8,28.6 46,28.6 36.1,35.8 39.9,47.4 30,40.2 20.1,47.4 23.9,35.8 14,28.6 26.2,28.6" fill="none" stroke="#006233" stroke-width="2.4"/></svg></span><span>Maroc</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : RD Congo"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="60" height="60" fill="#007FFF"/><path d="M-6 54 L54 -6 L66 6 L6 66Z" fill="#F7D618"/><path d="M-3 57 L57 -3 L63 3 L3 63Z" fill="#CE1021"/><polygon points="16.0,9.0 14.3,13.6 9.3,13.8 13.2,16.9 11.9,21.7 16.0,18.9 20.1,21.7 18.8,16.9 22.7,13.8 17.7,13.6" fill="#F7D618"/></svg></span><span>RD Congo</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Sénégal"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#00853F"/><rect x="20.0" width="20.5" height="60" fill="#FDEF42"/><rect x="40.0" width="20.5" height="60" fill="#E31B23"/><polygon points="30.0,23.0 28.3,27.6 23.3,27.8 27.2,30.9 25.9,35.7 30.0,32.9 34.1,35.7 32.8,30.9 36.7,27.8 31.7,27.6" fill="#00853F"/></svg></span><span>Sénégal</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Tchad"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#002664"/><rect x="20.0" width="20.5" height="60" fill="#FECB00"/><rect x="40.0" width="20.5" height="60" fill="#C60C30"/></svg></span><span>Tchad</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Togo"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="12.5" fill="#006A4E"/><rect y="12.0" width="60" height="12.5" fill="#FFCE00"/><rect y="24.0" width="60" height="12.5" fill="#006A4E"/><rect y="36.0" width="60" height="12.5" fill="#FFCE00"/><rect y="48.0" width="60" height="12.5" fill="#006A4E"/><rect width="30" height="36" fill="#D21034"/><polygon points="15.0,11.0 13.3,15.6 8.3,15.8 12.2,18.9 10.9,23.7 15.0,20.9 19.1,23.7 17.8,18.9 21.7,15.8 16.7,15.6" fill="#fff"/></svg></span><span>Togo</span></span></div></div><div class="ctry-group"><h6>Europe <small>2 pays</small></h6><div class="ctry-list"><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Belgique"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#000"/><rect x="20.0" width="20.5" height="60" fill="#FDDA24"/><rect x="40.0" width="20.5" height="60" fill="#EF3340"/></svg></span><span>Belgique</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : France"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#0055A4"/><rect x="20.0" width="20.5" height="60" fill="#fff"/><rect x="40.0" width="20.5" height="60" fill="#EF4135"/></svg></span><span>France</span></span></div></div><div class="ctry-group"><h6>Amérique <small>2 pays</small></h6><div class="ctry-list"><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Canada"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect width="60" height="60" fill="#fff"/><rect width="16" height="60" fill="#D52B1E"/><rect x="44" width="16" height="60" fill="#D52B1E"/><path d="M30 18 l3 6 4-2-1 9 5-4 1 3 5-1-2 6 3 2-11 8 1 5h-2.5v5h-2v-5H31l1-5-11-8 3-2-2-6 5 1 1-3 5 4-1-9 4 2z" fill="#D52B1E"/></svg></span><span>Canada</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : États-Unis"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="9.071428571428571" fill="#B22234"/><rect y="8.571428571428571" width="60" height="9.071428571428571" fill="#fff"/><rect y="17.142857142857142" width="60" height="9.071428571428571" fill="#B22234"/><rect y="25.714285714285715" width="60" height="9.071428571428571" fill="#fff"/><rect y="34.285714285714285" width="60" height="9.071428571428571" fill="#B22234"/><rect y="42.857142857142854" width="60" height="9.071428571428571" fill="#fff"/><rect y="51.42857142857143" width="60" height="9.071428571428571" fill="#B22234"/><rect width="28" height="30" fill="#3C3B6E"/><circle cx="5" cy="6" r="1.6" fill="#fff"/><circle cx="14" cy="6" r="1.6" fill="#fff"/><circle cx="23" cy="6" r="1.6" fill="#fff"/><circle cx="5" cy="15" r="1.6" fill="#fff"/><circle cx="14" cy="15" r="1.6" fill="#fff"/><circle cx="23" cy="15" r="1.6" fill="#fff"/><circle cx="5" cy="24" r="1.6" fill="#fff"/><circle cx="14" cy="24" r="1.6" fill="#fff"/><circle cx="23" cy="24" r="1.6" fill="#fff"/></svg></span><span>États-Unis</span></span></div></div><div class="ctry-group"><h6>Asie <small>2 pays</small></h6><div class="ctry-list"><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Émirats arabes unis"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="20.5" fill="#00732F"/><rect y="20.0" width="60" height="20.5" fill="#fff"/><rect y="40.0" width="60" height="20.5" fill="#000"/><rect width="18" height="60" fill="#FF0000"/></svg></span><span>Émirats arabes unis</span></span><span class="ctry"><span class="flag" style="width:30px;height:30px" role="img" aria-label="Drapeau : Inde"><svg width="30" height="30" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect y="0.0" width="60" height="20.5" fill="#FF9933"/><rect y="20.0" width="60" height="20.5" fill="#fff"/><rect y="40.0" width="60" height="20.5" fill="#138808"/><circle cx="30" cy="30" r="6" fill="none" stroke="#000080" stroke-width="1.6"/></svg></span><span>Inde</span></span></div></div></div></div>
  </div>
  </div>
</section>

<!-- ENGAGEMENT -->
<section class="section" id="engagement">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-6">
      <span class="kicker reveal">Engagement sociétal et inclusion numérique</span>
      <h2 class="title reveal d1">Faire du numérique un <span class="hl">levier d'opportunités</span></h2>
      <p class="lead-evc reveal d2">Le numérique change le monde. Nous voulons que personne ne reste au bord du chemin.</p>
      <div class="p-4 rounded-4 mb-4 reveal" style="background:var(--gold)"><span class="kicker mb-1" style="color:var(--navy)">Initiative · Caravane Digitale</span><h5 class="mb-1">Aller à la rencontre des jeunes et des communautés</h5><p class="mb-0 small" style="color:var(--navy)">La Caravane Digitale porte la culture numérique, l'orientation et l'information sur les métiers dans différentes localités.</p></div>
      <div class="row g-3">
        <div class="col-6 reveal"><b class="d-block" style="color:var(--navy)">Inclusion numérique</b><small class="text-muted-evc">Les compétences digitales ouvertes à tous les profils.</small></div>
        <div class="col-6 reveal d1"><b class="d-block" style="color:var(--navy)">Sensibilisation aux métiers</b><small class="text-muted-evc">Informer et orienter vers le numérique.</small></div>
        <div class="col-6 reveal"><b class="d-block" style="color:var(--navy)">Autonomisation</b><small class="text-muted-evc">Aider à lancer une activité ou à entreprendre.</small></div>
        <div class="col-6 reveal d1"><b class="d-block" style="color:var(--navy)">Développement durable</b><small class="text-muted-evc">Éducation, emploi, innovation et égalité des chances.</small></div>
      </div>
    </div>
    <div class="col-lg-6 reveal d1">
      <div class="row g-3">
        <div class="col-12"><a href="/assets/site/img/caravane.jpg" data-lb="eng" data-caption="L'EVC sur le terrain · CX Meet Up Côte d'Ivoire" class="ratio-photo d-block" style="aspect-ratio:16/10"><img loading="lazy" decoding="async" src="/assets/site/img/caravane.jpg" alt="L'EVC au CX Meet Up Côte d'Ivoire"></a></div>
        <div class="col-6"><a href="/assets/site/img/conference.jpg" data-lb="eng" data-caption="Prise de parole, échanges et visibilité" class="ratio-photo d-block" style="aspect-ratio:4/3"><img loading="lazy" decoding="async" src="/assets/site/img/conference.jpg" alt="Conférence"></a></div>
        <div class="col-6"><a href="/assets/site/img/diner.jpg" data-lb="eng" data-caption="Moments partagés" class="ratio-photo d-block" style="aspect-ratio:4/3"><img loading="lazy" decoding="async" src="/assets/site/img/diner.jpg" alt="Moment partagé"></a></div>
      </div>
    </div>
  </div></div>
</section>

<!-- CAP 2027 -->
<section class="section bg-cream" id="cap2027">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:860px"><span class="kicker center reveal">Vision de développement 2027 et au-delà</span><h2 class="title reveal d1">Construire un écosystème intégré de formation, <span class="hl">d'innovation et de valorisation des talents</span></h2></div>
    <div class="row g-4"><div class="col-md-6 col-lg-3 reveal"><div class="card-evc"><span class="num-badge">01</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-award"/></svg></span>Excellence pédagogique</h5><p>Renforcer la qualité des formations, actualiser les programmes et développer une pédagogie fondée sur la pratique.</p></div></div><div class="col-md-6 col-lg-3 reveal d1"><div class="card-evc"><span class="num-badge">02</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-app"/></svg></span>My EVC</h5><span class="chip">Transformation technologique</span><p>Faire évoluer la plateforme, automatiser les processus et enrichir l'expérience numérique.</p></div></div><div class="col-md-6 col-lg-3 reveal d2"><div class="card-evc"><span class="num-badge">03</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-mic"/></svg></span>Vox Tech Global</h5><span class="chip">Média international</span><p>Un média dédié à l'actualité technologique, à l'innovation et à la valorisation des talents africains.</p></div></div><div class="col-md-6 col-lg-3 reveal d3"><div class="card-evc"><span class="num-badge">04</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-store"/></svg></span>EVC Store</h5><span class="chip">Commerce numérique</span><p>Une plateforme commerciale d'équipements, accessoires et solutions numériques pour les créatifs.</p></div></div><div class="col-md-6 col-lg-3 reveal"><div class="card-evc"><span class="num-badge">05</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-play"/></svg></span>EVC Play</h5><span class="chip">Innovation pédagogique</span><p>Un jeu de cartes éducatif et interactif pour apprendre, réviser et relever des défis pratiques.</p></div></div><div class="col-md-6 col-lg-3 reveal d1"><div class="card-evc"><span class="num-badge">06</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-brief"/></svg></span>Employabilité & insertion</h5><p>Renforcer les passerelles entre formation et entreprise et favoriser les opportunités.</p></div></div><div class="col-md-6 col-lg-3 reveal d2"><div class="card-evc"><span class="num-badge">07</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-globe"/></svg></span>Partenariats & rayonnement</h5><p>Développer les collaborations en Afrique et à l'international.</p></div></div><div class="col-md-6 col-lg-3 reveal d3"><div class="card-evc"><span class="num-badge">08</span><h5 class="d-flex align-items-center gap-2"><span style="color:var(--royal)"><svg class="ico"><use href="#i-heart"/></svg></span>Responsabilité sociale</h5><p>Multiplier les initiatives d'orientation et d'accès aux compétences numériques.</p></div></div></div>
  </div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/direction.jpg" alt=""></div>
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
