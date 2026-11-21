@extends('layouts.site')
@section('title', "Entreprises & partenaires · EVC — École Virtuelle des Créatifs")
@section('description', "Recrutez des talents formés à la pratique, accueillez des stagiaires ou devenez partenaire de l'EVC. Partenaire institutionnel : ONP.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'entreprises')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/direction.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Entreprises & partenaires</span></nav>
    <h1 class="mb-3 reveal">Ensemble, ouvrons <span>des portes aux talents</span></h1>
    <p class="mb-0 reveal d1">Seul, on va vite. Ensemble, on va loin. Entreprises, institutions, associations : construisons avec nous l'avenir des talents.</p>
    <div class="d-flex flex-wrap gap-3 mt-4 reveal d2"><a href="#partenariat" class="btn btn-evc">Devenir partenaire</a><a href="#employabilite" class="btn btn-outline-light">Recruter nos talents</a></div>
  </div>
</header>
<section class="section" id="employabilite">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6">
        <span class="kicker reveal">Employabilité et insertion professionnelle</span>
        <h2 class="title reveal d1">Du certificat <span class="hl">au premier emploi</span></h2>
        <p class="lead-evc reveal d2">Notre mission ne s'arrête pas au certificat : nous voulons voir nos apprenants réussir au travail. Nous formons des profils opérationnels, évalués sur leurs réalisations.</p>
        <h6 class="mt-4 mb-3 reveal">Les aptitudes qui font la différence</h6>
        <div class="p-3 rounded-4 reveal" style="background:var(--cream)"><span class="chip" style="background:#fff">Compréhension des consignes</span><span class="chip" style="background:#fff">Organisation du travail</span><span class="chip" style="background:#fff">Autonomie et initiative</span><span class="chip" style="background:#fff">Respect des délais</span><span class="chip" style="background:#fff">Communication professionnelle</span><span class="chip" style="background:#fff">Gestion des priorités</span><span class="chip" style="background:#fff">Travail collaboratif</span><span class="chip" style="background:#fff">Capacité de présentation</span><span class="chip" style="background:#fff">Adaptabilité</span><span class="chip" style="background:#fff">Résolution de problèmes</span></div>
      </div>
      <div class="col-lg-6">
        <div class="row g-3">
          <div class="col-6 reveal"><div class="card-evc text-center"><b class="d-block" style="font-size:2.6rem;font-weight:800;color:var(--navy);line-height:1" data-count="102">0</b><small class="text-muted-evc">stagiaires intégrés en entreprise</small></div></div>
          <div class="col-6 reveal d1"><div class="card-evc text-center"><b class="d-block" style="font-size:2.6rem;font-weight:800;color:var(--navy);line-height:1" data-count="74">0</b><small class="text-muted-evc">apprenants en emploi</small></div></div>
          <div class="col-6 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-doc"/></svg></span><h5>Des preuves de compétences</h5><p>Portfolios, pressbooks, rapports et études de cas pour convaincre un recruteur.</p></div></div>
          <div class="col-6 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-share"/></svg></span><h5>Des connexions pros</h5><p>Agences, entreprises, institutions et structures du numérique.</p></div></div>
        </div>
      </div>
    </div>
    <div class="mt-5" data-slider><div class="d-flex flex-wrap justify-content-between align-items-end gap-4 mb-4 ">
  <div style="max-width:720px"><span class="kicker reveal">Certificats et lettres de recommandation</span><h2 class="title mb-2 reveal d1">Des profils certifiés, <span class='hl'>prêts à contribuer</span></h2></div>
  <div class="slider-ctrl"><div class="slider-progress me-2 d-none d-md-block"><i></i></div><button data-prev aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button data-next aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button></div>
</div><div class="evc-track"><a href="/assets/site/img/certif-1.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-1.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-2.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-2.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-3.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-3.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-4.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-4.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-5.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-5.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-6.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-6.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-7.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-7.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-8.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-8.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-9.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-9.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-10.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-10.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-11.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-11.jpg" alt="Certifié EVC" loading="lazy"></a><a href="/assets/site/img/certif-12.jpg" data-lb="ent" data-caption="Certificats et lettres de recommandation · Promo 2026" class="ratio-photo d-block" style="width:min(190px,58vw);aspect-ratio:3/4;border-radius:1rem"><img decoding="async" src="/assets/site/img/certif-12.jpg" alt="Certifié EVC" loading="lazy"></a></div></div>
  </div>
</section>

<section class="section bg-cream">
  <div class="container">
    <div class="text-center mx-auto mb-5" style="max-width:760px"><span class="kicker center reveal">Partenariats et coopération</span><h2 class="title reveal d1">Quatre façons de <span class="hl">collaborer avec l'EVC</span></h2></div>
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><h5>Entreprises</h5><p>Projets, stages, recrutements, experts et études de cas.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-home"/></svg></span><h5>Institutions</h5><p>Structures publiques, organismes de l'emploi, collectivités.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-bulb"/></svg></span><h5>Pédagogie</h5><p>Professionnels et experts enrichissent nos contenus.</p></div></div>
      <div class="col-md-6 col-lg-3 reveal d3"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-globe"/></svg></span><h5>International</h5><p>Des échanges au-delà des frontières ivoiriennes.</p></div></div>
    </div>
    <div class="partner-card flex-column flex-md-row align-items-start reveal">
      <div class="partner-logo">ONP</div>
      <div>
        <span class="kicker mb-2">Ils nous font confiance · partenaire institutionnel</span>
        <h4 class="mb-1">Office National de la Population (ONP)</h4>
        <p class="small fw-semibold" style="color:var(--blue)">Établissement public sous tutelle du Ministère du Plan et du Développement</p>
        <p class="text-muted-evc">En février 2026, l'ONP a adressé à l'EVC une offre de stages académiques et professionnels au sein de ses services, en vue d'un partenariat pour l'orientation et le placement de nos étudiants. Spécialités concernées :</p>
        <div><span class="chip">Infographie</span><span class="chip">Communication digitale</span><span class="chip">Référencement et gestion digitale</span><span class="chip">Webmastering</span><span class="chip">Développement informatique</span><span class="chip">Systèmes d'information</span></div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row align-items-end mb-5 g-4"><div class="col-lg-7"><span class="kicker reveal">Pourquoi collaborer avec l'EVC ?</span><h2 class="title mb-0 reveal d1">Une école, des talents, <span class="hl">une ambition commune</span></h2></div><div class="col-lg-5"><p class="lead-evc mb-0 reveal d2">Ensemble, transformons les potentiels en compétences, les compétences en réalisations et les réalisations en opportunités.</p></div></div>
    <div class="row g-4"><div class="col-md-6 col-lg-4 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-brief"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">01</span><h5>Une expertise pratique</h5><p>Des formations tournées vers les outils, les méthodes et les situations réelles de travail.</p></div></div><div class="col-md-6 col-lg-4 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-target"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">02</span><h5>La professionnalisation</h5><p>Projets, évaluations et dispositifs pensés pour rendre les apprenants autonomes.</p></div></div><div class="col-md-6 col-lg-4 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-app"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">03</span><h5>Un environnement numérique</h5><p>My EVC facilite l'organisation et le suivi pédagogique.</p></div></div><div class="col-md-6 col-lg-4 reveal"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-users"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">04</span><h5>Une communauté de talents</h5><p>Des apprenants et anciens de plusieurs pays et spécialités.</p></div></div><div class="col-md-6 col-lg-4 reveal d1"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-award"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">05</span><h5>Une certification exigeante</h5><p>Les compétences sont reconnues après validation du parcours.</p></div></div><div class="col-md-6 col-lg-4 reveal d2"><div class="card-evc"><span class="ic"><svg class="ico"><use href="#i-rocket"/></svg></span><span class="num-badge or" style="position:absolute;right:1.4rem;top:1.4rem;margin:0">06</span><h5>Une ambition d'impact</h5><p>Des professionnels acteurs de la transformation numérique.</p></div></div></div>
  </div>
</section>

<section class="section bg-navy" id="partenariat">
  <div class="container"><div class="row g-5 align-items-center">
    <div class="col-lg-5">
      <span class="kicker light reveal">Construire ensemble</span>
      <h2 class="title text-white reveal d1">Parlons de <span class="hl">votre projet</span></h2>
      <p class="reveal d2" style="color:#B5BEDD">La direction de l'EVC est à votre écoute pour bâtir des partenariats durables. Nous sommes ouverts aux échanges sur :</p>
      <div class="reveal"><span class="chip dark">Formations sur mesure</span><span class="chip dark">Projets d'insertion</span><span class="chip dark">Partenariats entreprises-école</span><span class="chip dark">Sensibilisation numérique</span><span class="chip dark">Interventions d'experts</span><span class="chip dark">Développement des compétences</span><span class="chip dark">Innovation pédagogique</span><span class="chip dark">Coopérations nationales et internationales</span></div>
      <div class="ratio-photo mt-4 reveal" style="aspect-ratio:16/9"><img loading="lazy" decoding="async" src="/assets/site/img/direction.jpg" alt="Direction de l'EVC"></div>
    </div>
    <div class="col-lg-7 reveal d1">
      <div class="form-panel">
        <form class="form-evc" data-wa="Bonjour EVC, demande de PARTENARIAT :" data-success="#okPart" novalidate>
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label" for="pOrg">Organisation *</label><input class="form-control" id="pOrg" name="Organisation" required></div>
            <div class="col-md-6"><label class="form-label" for="pNom">Nom et fonction *</label><input class="form-control" id="pNom" name="Contact" required></div>
            <div class="col-md-6"><label class="form-label" for="pTel">Téléphone *</label><input class="form-control" id="pTel" name="Téléphone" type="tel" required></div>
            <div class="col-md-6"><label class="form-label" for="pMail">E-mail</label><input class="form-control" id="pMail" name="E-mail" type="email"></div>
            <div class="col-12"><label class="form-label" for="pType">Type de collaboration</label><select class="form-select" id="pType" name="Objet"><option>Recruter des talents / accueillir des stagiaires</option><option>Proposer un brief ou un cas réel</option><option>Formation sur mesure pour mes équipes</option><option>Partenariat institutionnel</option><option>Intervention d'experts</option><option>Autre</option></select></div>
            <div class="col-12"><label class="form-label" for="pMsg">Votre message</label><textarea class="form-control" id="pMsg" name="Message" rows="4"></textarea></div>
            <div class="col-12"><button class="btn btn-evc btn-lg w-100" type="submit">Envoyer ma demande</button><p class="small text-muted-evc text-center mt-2 mb-0">Votre demande est transmise à la direction via WhatsApp. Vous pouvez aussi écrire à info@ecolevirtuelledescreatifs.com</p></div>
          </div>
        </form>
        <div class="form-success" id="okPart"><div class="ok"><svg class="ico"><use href="#i-check"/></svg></div><h4>Merci, votre demande est prête !</h4><p class="text-muted-evc">Cliquez ci-dessous pour l'envoyer à la direction sur WhatsApp.</p><a href="#" data-wa-link target="_blank" rel="noopener" class="btn btn-wa d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Envoyer sur WhatsApp</a></div>
      </div>
    </div>
  </div></div>
</section>
@endverbatim
@endsection
