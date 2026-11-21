@extends('layouts.site')
@section('title', "Contact · EVC — École Virtuelle des Créatifs")
@section('description', "Contactez l'École Virtuelle des Créatifs : WhatsApp +225 07 47 25 95 07, info@ecolevirtuelledescreatifs.com, Bloom Square Akwaba, Palmeraie, Abidjan.")
@section('og_image', '/assets/site/img/ceremonie.jpg')
@section('page', 'contact')

@section('content')
@verbatim
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/diner.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Contact</span></nav>
    <h1 class="mb-3 reveal">Parlons de <span>votre projet</span></h1>
    <p class="mb-0 reveal d1">Vous voulez vous former, recruter un talent ou construire un partenariat ? Appelez-nous, écrivez-nous ou passez nous voir : nous serons heureux de vous rencontrer.</p>
    
  </div>
</header>
<section class="section">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-md-6 col-lg-3 reveal"><a class="contact-card text-decoration-none" href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener"><span class="ic" style="background:#1FA855"><svg class="ico"><use href="#i-wa"/></svg></span><div><b>+225 07 47 25 95 07</b><small>WhatsApp · Côte d'Ivoire</small></div></a></div>
      <div class="col-md-6 col-lg-3 reveal d1"><a class="contact-card text-decoration-none" href="https://wa.me/33605668823?text=INFORMATION" target="_blank" rel="noopener"><span class="ic" style="background:var(--royal)"><svg class="ico"><use href="#i-wa"/></svg></span><div><b>+33 6 05 66 88 23</b><small>WhatsApp · France</small></div></a></div>
      <div class="col-md-6 col-lg-3 reveal d2"><div class="contact-card"><span class="ic" style="background:var(--orange)"><svg class="ico"><use href="#i-phone"/></svg></span><div><b>+225 07 17 25 86 02</b><small>Appel · Côte d'Ivoire</small></div></div></div>
      <div class="col-md-6 col-lg-3 reveal d3"><div class="contact-card"><span class="ic" style="background:var(--navy)"><svg class="ico"><use href="#i-mail"/></svg></span><div><b style="font-size:.86rem;word-break:break-all">info@ecolevirtuelledescreatifs.com</b><small>E-mail</small></div></div></div>
    </div>
    <div class="row g-4">
      <div class="col-lg-6 reveal">
        <div class="map-card">
          <svg class="map" viewBox="0 0 600 420" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <rect width="600" height="420" fill="#ECE7DB"/>
            <path d="M0 330 C120 300 200 360 320 330 S520 280 600 310 V420 H0Z" fill="#CFE0EE"/>
            <g stroke="#fff" stroke-width="14" fill="none" stroke-linecap="round"><path d="M-10 120 L610 170"/><path d="M180 -10 L260 430"/><path d="M420 -10 L380 430"/></g>
            <g stroke="#fff" stroke-width="6" fill="none" opacity=".9"><path d="M0 60 L600 90"/><path d="M0 240 L600 250"/><path d="M90 -10 L120 430"/><path d="M520 -10 L500 430"/><path d="M300 0 L330 420"/></g>
            <g fill="#DDD6C6"><rect x="40" y="140" width="110" height="80" rx="8"/><rect x="290" y="180" width="70" height="50" rx="8"/><rect x="440" y="100" width="60" height="50" rx="8"/><rect x="60" y="20" width="90" height="30" rx="6"/><rect x="440" y="190" width="120" height="40" rx="8"/></g>
            <text x="400" y="80" font-family="Montserrat" font-size="12" font-weight="700" fill="#8A8573">PALMERAIE</text>
            <text x="200" y="300" font-family="Montserrat" font-size="11" font-weight="600" fill="#8A8573">Carrefour Guiraud</text>
          </svg>
          <div class="pin"><div class="dot"><img loading="lazy" decoding="async" src="/assets/site/img/logo-white.png" alt=""></div><div class="ring"></div></div>
          <div class="addr"><div><small class="d-block text-muted-evc" style="font-size:.65rem;letter-spacing:.12em">PLAN INDICATIF</small><b style="color:var(--navy)">Bloom Square Akwaba</b><div class="small text-muted-evc">Palmeraie, Carrefour Guiraud, Abidjan, Côte d'Ivoire</div></div><a href="https://www.google.com/maps/search/?api=1&query=Bloom+Square+Akwaba+Palmeraie+Abidjan" target="_blank" rel="noopener" class="btn btn-navy btn-sm">Itinéraire</a></div>
        </div>
      </div>
      <div class="col-lg-6 reveal d1">
        <div class="form-panel h-100">
          <h4 class="mb-1">Écrivez-nous</h4><p class="text-muted-evc small mb-4">Votre message est transmis à notre équipe sur WhatsApp pour une réponse rapide.</p>
          <form class="form-evc" data-wa="Bonjour EVC, nouveau message depuis le site :" data-success="#okCt" novalidate>
            <div class="row g-3">
              <div class="col-md-6"><label class="form-label" for="cNom">Nom *</label><input class="form-control" id="cNom" name="Nom" required></div>
              <div class="col-md-6"><label class="form-label" for="cTel">Téléphone *</label><input class="form-control" id="cTel" name="Téléphone" type="tel" required></div>
              <div class="col-12"><label class="form-label" for="cObj">Objet</label><select class="form-select" id="cObj" name="Objet"><option>Information sur une formation</option><option>Inscription</option><option>Partenariat / entreprise</option><option>Espace My EVC</option><option>Autre</option></select></div>
              <div class="col-12"><label class="form-label" for="cMsg">Message *</label><textarea class="form-control" id="cMsg" name="Message" rows="4" required></textarea></div>
              <div class="col-12"><button class="btn btn-evc w-100" type="submit">Envoyer</button></div>
            </div>
          </form>
          <div class="form-success" id="okCt"><div class="ok"><svg class="ico"><use href="#i-check"/></svg></div><h4>Message prêt !</h4><p class="text-muted-evc">Cliquez pour l'envoyer à notre équipe sur WhatsApp.</p><a href="#" data-wa-link target="_blank" rel="noopener" class="btn btn-wa d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Envoyer sur WhatsApp</a></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section bg-cream" id="faq">
  <div class="container"><div class="row g-5">
    <div class="col-lg-4"><span class="kicker reveal">Questions fréquentes</span><h2 class="title reveal d1">Tout savoir <span class="hl">sur l'EVC</span></h2><p class="lead-evc reveal d2">Vous ne trouvez pas votre réponse ? Écrivez-nous sur WhatsApp.</p><a href="https://wa.me/2250747259507?text=QUESTION" target="_blank" rel="noopener" class="btn btn-wa d-inline-flex align-items-center gap-2 reveal"><svg class="ico"><use href="#i-wa"/></svg> Poser ma question</a></div>
    <div class="col-lg-8 reveal d1"><div class="accordion faq" id="faqC">
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1" aria-expanded="true">Les formations sont-elles en ligne ou en présentiel ?</button></h3><div id="c1" class="accordion-collapse collapse show" data-bs-parent="#faqC"><div class="accordion-body">Les deux : toutes nos formations se suivent en ligne et en présentiel.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2">Où se trouve l'école ?</button></h3><div id="c2" class="accordion-collapse collapse" data-bs-parent="#faqC"><div class="accordion-body">Bloom Square Akwaba, Palmeraie, Carrefour Guiraud, à Abidjan (Côte d'Ivoire).</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3">Faut-il un diplôme pour s'inscrire ?</button></h3><div id="c3" class="accordion-collapse collapse" data-bs-parent="#faqC"><div class="accordion-body">Non. Un échange d'orientation permet d'analyser votre profil, vos motivations et vos objectifs avant l'admission.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c4">L'EVC est-elle une structure officielle ?</button></h3><div id="c4" class="accordion-collapse collapse" data-bs-parent="#faqC"><div class="accordion-body">Oui. L'École Virtuelle des Créatifs est une SARL de droit ivoirien créée en 2022, dont le siège est à Abidjan. Son fondateur et directeur général est Bilé Bossombra.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c5">Qu'est-ce que My EVC ?</button></h3><div id="c5" class="accordion-collapse collapse" data-bs-parent="#faqC"><div class="accordion-body">C'est la plateforme pédagogique de l'école : ressources, travaux, évaluations, résultats et suivi de progression, accessibles partout.</div></div></div>
      <div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c6">Comment devenir partenaire ?</button></h3><div id="c6" class="accordion-collapse collapse" data-bs-parent="#faqC"><div class="accordion-body">Rendez-vous sur la page <a href="/entreprises#partenariat">Entreprises & partenaires</a> ou écrivez à info@ecolevirtuelledescreatifs.com.</div></div></div>
    </div></div>
  </div></div>
</section>

<section class="pt-4">
  <div class="container">
    <div class="cta-band reveal">
      <div class="bg"><img decoding="async" src="/assets/site/img/ceremonie.jpg" alt=""></div>
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
