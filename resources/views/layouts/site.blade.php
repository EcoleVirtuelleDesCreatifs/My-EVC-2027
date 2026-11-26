<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php($seo = \App\Support\SiteSeo::make($__env->yieldContent('title', 'École Virtuelle des Créatifs'), $__env->yieldContent('description'), $__env->yieldContent('content'), isset($news) && $news instanceof \App\Models\News ? $news : null, $publicCourse ?? null))
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}">
<meta property="og:title" content="{{ $seo['title'] }}">
<meta property="og:description" content="{{ $seo['description'] }}">
<meta property="og:type" content="{{ isset($news) && $news instanceof \App\Models\News ? 'article' : 'website' }}">
<meta property="og:url" content="{{ \App\Support\SiteSeo::absolute(request()->path()) }}">
@if (isset($news) && $news instanceof \App\Models\News)
<meta name="author" content="Rédaction EVC">
<meta property="article:published_time" content="{{ $news->published_at->toAtomString() }}">
<meta property="article:modified_time" content="{{ ($news->updated_at ?? $news->published_at)->max($news->published_at)->toAtomString() }}">
@endif
<script type="application/ld+json">{!! json_encode($seo['graph'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) !!}</script>
@php($ogImage = trim($__env->yieldContent('og_image', '/assets/site/img/ceremonie.jpg')))
<meta property="og:image" content="{{ \App\Support\SiteSeo::absolute($ogImage) }}">
@if ($ogSize = \App\Support\ResponsiveImages::find($ogImage))
<meta property="og:image:width" content="{{ $ogSize['width'] }}">
<meta property="og:image:height" content="{{ $ogSize['height'] }}">
@endif
<meta property="og:locale" content="fr_FR">
<meta property="og:site_name" content="École Virtuelle des Créatifs">
<meta name="twitter:card" content="summary_large_image">
<link rel="canonical" href="{{ \App\Support\SiteSeo::absolute(request()->path()) }}">
<link rel="icon" href="/assets/site/img/logo.png">
<meta name="theme-color" content="#0E1A3C">
<link rel="stylesheet" href="/assets/site/css/bootstrap.site.css?v={{ filemtime(public_path('assets/site/css/bootstrap.site.css')) }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,500;1,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/site/css/site.css?v={{ filemtime(public_path('assets/site/css/site.css')) }}">
</head>
<body data-page="@yield('page', 'accueil')" class="pg-@yield('page', 'accueil')" data-wa="{{ \App\Models\Setting::get('whatsapp_ci') }}">
@verbatim
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol><symbol id="i-phone" viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></symbol><symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 6-10 7L2 6"/></symbol><symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></symbol><symbol id="i-user" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></symbol><symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></symbol><symbol id="i-pen" viewBox="0 0 24 24"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.6 7.6"/><circle cx="11" cy="11" r="2"/></symbol><symbol id="i-share" viewBox="0 0 24 24"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.6 13.5l6.8 4M15.4 6.5l-6.8 4"/></symbol><symbol id="i-monitor" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></symbol><symbol id="i-book" viewBox="0 0 24 24"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/><path d="M6.5 17A2.5 2.5 0 0 0 4 19.5 2.5 2.5 0 0 0 6.5 22H20v-5"/></symbol><symbol id="i-wallet" viewBox="0 0 24 24"><rect x="2" y="6" width="20" height="14" rx="2"/><path d="M16 13h2M2 10h20M6 6V4h12v2"/></symbol><symbol id="i-app" viewBox="0 0 24 24"><rect x="6" y="2" width="12" height="20" rx="2.5"/><path d="M11 18h2"/></symbol><symbol id="i-chat" viewBox="0 0 24 24"><path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 8.5 8.5 0 0 1-3.8-.9L3 21l1.9-5.2A8.4 8.4 0 1 1 21 11.5z"/></symbol><symbol id="i-check" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></symbol><symbol id="i-award" viewBox="0 0 24 24"><circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/></symbol><symbol id="i-target" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></symbol><symbol id="i-cal" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></symbol><symbol id="i-brief" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></symbol><symbol id="i-users" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/></symbol><symbol id="i-hand" viewBox="0 0 24 24"><path d="M11 17l-1.5 1.5a2.1 2.1 0 0 1-3-3L11 11"/><path d="M14 14l2.5 2.5a2.1 2.1 0 0 0 3-3L15 9l-3-3-5 5"/><path d="M3 12l3-3M21 12l-3-3"/></symbol><symbol id="i-globe" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 0 1 4 10 15 15 0 0 1-4 10 15 15 0 0 1-4-10 15 15 0 0 1 4-10z"/></symbol><symbol id="i-up" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></symbol><symbol id="i-left" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></symbol><symbol id="i-right" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></symbol><symbol id="i-wa" viewBox="0 0 24 24"><path d="M3 21l1.7-4.6A9 9 0 1 1 8 19.6L3 21z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1.2-1.2-1.8-1-.8.7a4 4 0 0 1-2.1-2.1l.7-.8-1-1.8L9.5 9"/></symbol><symbol id="i-shield" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></symbol><symbol id="i-star" viewBox="0 0 24 24"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/></symbol><symbol id="i-bulb" viewBox="0 0 24 24"><path d="M9 18h6M10 22h4M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/></symbol><symbol id="i-layers" viewBox="0 0 24 24"><path d="m12 2 10 5-10 5L2 7l10-5z"/><path d="m2 17 10 5 10-5M2 12l10 5 10-5"/></symbol><symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></symbol><symbol id="i-flag" viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><path d="M4 22v-7"/></symbol><symbol id="i-heart" viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21.2l8.8-8.8a5.5 5.5 0 0 0 0-7.8z"/></symbol><symbol id="i-rocket" viewBox="0 0 24 24"><path d="M4.5 16.5c-1.5 1.3-2 5-2 5s3.7-.5 5-2c.7-.8.7-2.1-.1-2.9a2.2 2.2 0 0 0-2.9-.1z"/><path d="M12 15l-3-3a22 22 0 0 1 2-3.9A12.9 12.9 0 0 1 22 2c0 2.7-.8 7.5-6 11a22.4 22.4 0 0 1-4 2z"/><path d="M9 12H4s.6-3 2-4c1.6-1.1 5 0 5 0M12 15v5s3-.6 4-2c1.1-1.6 0-5 0-5"/></symbol><symbol id="i-play" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="m10 8 6 4-6 4z"/></symbol><symbol id="i-doc" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M8 13h8M8 17h5"/></symbol><symbol id="i-chart" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="m7 15 4-4 3 3 5-6"/></symbol><symbol id="i-home" viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></symbol><symbol id="i-eye" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></symbol><symbol id="i-zoom" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v6M8 11h6"/></symbol><symbol id="i-x" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></symbol><symbol id="i-store" viewBox="0 0 24 24"><path d="M3 9l1.5-5h15L21 9M3 9h18v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1zM9 21v-6h6v6"/></symbol><symbol id="i-mic" viewBox="0 0 24 24"><rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10a7 7 0 0 0 14 0M12 17v5"/></symbol></svg>
<div class="progress-top" id="progressTop"></div>
<div class="topbar py-2 d-none d-lg-block">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex gap-4">
      <a href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener" class="d-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg>CI · +225 07 47 25 95 07</a>
      <a href="https://wa.me/33605668823?text=INFORMATION" target="_blank" rel="noopener" class="d-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg>France · +33 6 05 66 88 23</a>
      <span class="d-none d-xl-flex align-items-center gap-2"><svg class="ico"><use href="#i-mail"/></svg>info@ecolevirtuelledescreatifs.com</span>
    </div>
    <div class="d-flex gap-4 align-items-center">
      <a href="/actualites" data-nav="actualites" class="topbar-news d-flex align-items-center gap-2"><svg class="ico"><use href="#i-doc"/></svg>Actualités</a>
      <a href="/entreprises" class="tb-hide-md">Entreprises & partenaires</a>
      <a href="https://verification.ecolevirtuelledescreatifs.com/" class="d-flex align-items-center gap-2"><svg class="ico"><use href="#i-shield"/></svg>Vérifier un certificat</a>
      <a href="/contact#faq">FAQ</a>
      <a href="/contact" class="topbar-contact d-flex align-items-center gap-2" data-nav="contact"><svg class="ico"><use href="#i-chat"/></svg>Contact</a>
    </div>
  </div>
</div>

<nav class="navbar navbar-expand-lg navbar-evc sticky-top" id="mainNav" aria-label="Navigation principale">
  <div class="container">
    <a class="navbar-brand" href="/"><img decoding="async" src="/assets/site/img/logo.png" alt="EVC — École Virtuelle des Créatifs, accueil"></a>
    <div class="collapse navbar-collapse justify-content-center">
      <ul class="navbar-nav align-items-lg-center">
        <li class="nav-item"><a class="nav-link" data-nav="accueil" href="/">Accueil</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-nav="ecole" href="/ecole" role="button" data-bs-toggle="dropdown" aria-expanded="false">L'école</a>
          <ul class="dropdown-menu dd">
            <li><a class="dropdown-item" href="/ecole#presentation"><svg class="ico"><use href="#i-home"/></svg>Présentation générale</a></li>
            <li><a class="dropdown-item" href="/ecole#fondateur"><svg class="ico"><use href="#i-user"/></svg>Mot du fondateur</a></li>
            <li><a class="dropdown-item" href="/ecole#vision"><svg class="ico"><use href="#i-target"/></svg>Vision, mission & valeurs</a></li>
            <li><a class="dropdown-item" href="/ecole#gouvernance"><svg class="ico"><use href="#i-layers"/></svg>Organisation & gouvernance</a></li>
            <li><a class="dropdown-item" href="/ecole#rayonnement"><svg class="ico"><use href="#i-globe"/></svg>Rayonnement & engagement</a></li>
            <li><a class="dropdown-item" href="/ecole#cap2027"><svg class="ico"><use href="#i-rocket"/></svg>Cap 2027</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown dropdown-mega">
          <a class="nav-link dropdown-toggle" data-nav="formations" href="/formations" role="button" data-bs-toggle="dropdown" aria-expanded="false">Formations</a>
          <div class="dropdown-menu dd mega">
            <div class="row g-2">
              <div class="col-lg-8">
                <a class="mega-item" href="/formation-design"><span class="thumb"><img sizes="78px" loading="lazy" decoding="async" src="/assets/site/img/oeuvre-mood-vendredi.jpg" alt=""></span><span><b>Infographie & design graphique</b><small>Photoshop, Illustrator, InDesign · logos, affiches, identités visuelles</small><span class="price">4 mois · 185 000 FCFA</span></span></a>
                <a class="mega-item" href="/formation-community-management"><span class="thumb"><img sizes="78px" loading="lazy" decoding="async" src="/assets/site/img/promo-cm.jpg" alt=""></span><span><b>Community management & social media</b><small>Facebook, Instagram, TikTok · stratégie, contenus, publicité sociale</small><span class="price">2 mois · 165 000 FCFA</span></span></a>
                <a class="mega-item" href="/formation-bureautique"><span class="thumb"><img sizes="78px" loading="lazy" decoding="async" src="/assets/site/img/classe-4.jpg" alt=""></span><span><b>Bureautique & informatique (GIA)</b><small>Word, Excel, PowerPoint, Google, Canva · productivité en entreprise</small><span class="price">2 mois · 265 000 FCFA</span></span></a>
              </div>
              <div class="col-lg-4">
                <div class="mega-aside">
                  <span class="kicker light mb-2">Rentrée 2026–2027</span>
                  <h6>Inscriptions ouvertes</h6>
                  <p class="small mb-3">Paiement en 2 fois. Kit étudiant et accès My EVC inclus. Certificat délivré sous conditions.</p>
                  <a href="/formations" class="link-arrow mb-3" style="color:#fff">Comparer les formations <svg class="ico"><use href="#i-arrow"/></svg></a>
                  <a href="/preinscription" class="btn btn-evc btn-sm mt-auto align-self-start">Préinscription</a>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-nav="pedagogie" href="/pedagogie" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pédagogie</a>
          <ul class="dropdown-menu dd">
            <li><a class="dropdown-item" href="/pedagogie#modele"><svg class="ico"><use href="#i-bulb"/></svg>Notre modèle pédagogique</a></li>
            <li><a class="dropdown-item" href="/pedagogie#parcours"><svg class="ico"><use href="#i-flag"/></svg>Parcours en 8 étapes</a></li>
            <li><a class="dropdown-item" href="/pedagogie#myevc"><svg class="ico"><use href="#i-app"/></svg>Plateforme My EVC</a></li>
            <li><a class="dropdown-item" href="/pedagogie#certification"><svg class="ico"><use href="#i-award"/></svg>Certification</a></li>
            <li><a class="dropdown-item" href="https://verification.ecolevirtuelledescreatifs.com/"><svg class="ico"><use href="#i-shield"/></svg>Vérifier un certificat</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" data-nav="studio" href="/studio-creative"><svg class="ico"><use href="#i-pen"/></svg>Studio Creative & jury</a></li>
            <li><a class="dropdown-item" href="/studio-creative#reco60"><svg class="ico"><use href="#i-cal"/></svg>RECO 60</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-nav="realisations" href="/realisations" role="button" data-bs-toggle="dropdown" aria-expanded="false">Réalisations</a>
          <ul class="dropdown-menu dd">
            <li><a class="dropdown-item" href="/realisations#galerie"><svg class="ico"><use href="#i-pen"/></svg>Créations des apprenants</a></li>
            <li><a class="dropdown-item" href="/realisations#temoignages"><svg class="ico"><use href="#i-chat"/></svg>Témoignages</a></li>
            <li><a class="dropdown-item" href="/phototheque"><svg class="ico"><use href="#i-eye"/></svg>Photothèque · La vie à l'EVC</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" data-nav="jury" href="/jury-certification">Jury & certification</a></li>
      </ul>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="/myevc/connexion" class="btn btn-myevc d-none d-lg-inline-flex align-items-center gap-2" title="Se connecter à mon espace My EVC"><svg class="ico"><use href="#i-user"/></svg><span class="lg-full">Espace My EVC</span><span class="lg-short">My EVC</span></a>
      <a href="/preinscription" class="btn btn-evc d-none d-sm-inline-flex align-items-center gap-2">Préinscription <svg class="ico ico-arrow"><use href="#i-arrow"/></svg></a>
      <a href="/myevc/connexion" class="btn btn-myevc d-inline-flex d-lg-none align-items-center gap-2 btn-myevc-m" aria-label="Espace My EVC"><svg class="ico"><use href="#i-user"/></svg><span class="d-none d-sm-inline">My EVC</span></a>
      <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offMenu" aria-controls="offMenu" aria-label="Ouvrir le menu"><svg class="ico"><use href="#i-menu"/></svg></button>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-end offcanvas-evc" tabindex="-1" id="offMenu" aria-label="Menu">
  <div class="offcanvas-header"><img loading="lazy" decoding="async" src="/assets/site/img/logo-white.png" alt="EVC" style="height:44px"><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fermer"></button></div>
  <div class="offcanvas-body d-flex flex-column">
    <div class="oc-cta"><a href="/preinscription" class="btn btn-evc">Préinscription</a><a href="/myevc/connexion" class="btn btn-light" style="color:var(--navy)"><svg class="ico"><use href="#i-user"/></svg> My EVC</a></div>
    <a class="solo" href="/">Accueil</a>
    <div class="accordion" id="mAcc">
      <div class="accordion-item"><p class="accordion-header mb-0"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m1">L'école</button></p><div id="m1" class="accordion-collapse collapse" data-bs-parent="#mAcc"><div class="accordion-body"><a href="/ecole#presentation">Présentation</a><a href="/ecole#fondateur">Mot du fondateur</a><a href="/ecole#vision">Vision & valeurs</a><a href="/ecole#gouvernance">Gouvernance</a><a href="/ecole#cap2027">Cap 2027</a></div></div></div>
      <div class="accordion-item"><p class="accordion-header mb-0"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m2">Formations</button></p><div id="m2" class="accordion-collapse collapse" data-bs-parent="#mAcc"><div class="accordion-body"><a href="/formations">Toutes les formations</a><a href="/formation-design">Design graphique · 4 mois</a><a href="/formation-community-management">Community management · 2 mois</a><a href="/formation-bureautique">Bureautique GIA · 2 mois</a></div></div></div>
      <div class="accordion-item"><p class="accordion-header mb-0"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m3">Pédagogie</button></p><div id="m3" class="accordion-collapse collapse" data-bs-parent="#mAcc"><div class="accordion-body"><a href="/pedagogie">Modèle pédagogique</a><a href="/pedagogie#parcours">Parcours en 8 étapes</a><a href="/pedagogie#myevc">My EVC</a><a href="/pedagogie#certification">Certification</a><a href="/studio-creative">Studio Creative</a><a href="/studio-creative#reco60">RECO 60</a></div></div></div>
    </div>
    <div class="accordion" id="mAcc2">
      <div class="accordion-item"><p class="accordion-header mb-0"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#m4">Réalisations</button></p><div id="m4" class="accordion-collapse collapse" data-bs-parent="#mAcc2"><div class="accordion-body"><a href="/realisations#galerie">Créations des apprenants</a><a href="/realisations#temoignages">Témoignages</a><a href="/phototheque">Photothèque · La vie à l'EVC</a></div></div></div>
    </div>
    <a class="solo" href="/actualites">Actualités</a>
    <a class="solo" href="/jury-certification">Jury & certification</a>
    <a class="solo" href="https://verification.ecolevirtuelledescreatifs.com/">Vérifier un certificat</a>
    <a class="solo" href="/entreprises">Entreprises & partenaires</a>
    <a class="solo" href="/contact">Contact</a>
    <div class="mt-auto pt-4">
      <a href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener" class="btn btn-outline-light w-100 mb-3"><svg class="ico"><use href="#i-wa"/></svg> WhatsApp</a>
      <p class="small mb-0" style="color:#AEB7D8">+225 07 47 25 95 07<br>info@ecolevirtuelledescreatifs.com</p>
    </div>
  </div>
</div>
@endverbatim
<main id="contenu">
@yield('content')
</main>
@verbatim
<footer class="foot">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <img loading="lazy" decoding="async" src="/assets/site/img/logo-white.png" alt="EVC" style="height:60px" class="mb-4">
        <p style="max-width:36ch">L'école ultra-pratique des métiers du numérique. Former, accompagner et faire briller les talents, en ligne et en présentiel depuis 2022.</p>
        <div class="foot-trust"><span><svg class="ico"><use href="#i-shield"/></svg>SARL de droit ivoirien</span><span><svg class="ico"><use href="#i-award"/></svg>Certification exigeante</span><span><svg class="ico"><use href="#i-globe"/></svg>18 pays</span></div>
        <form class="newsletter mt-4"><input type="email" placeholder="Votre e-mail pour nos actualités" aria-label="Votre e-mail" required><button class="btn btn-evc btn-sm" type="submit">S'abonner</button></form>
      </div>
      <div class="col-6 col-lg-2"><h6>L'école</h6><ul><li><a href="/ecole">Présentation</a></li><li><a href="/ecole#fondateur">Mot du fondateur</a></li><li><a href="/ecole#vision">Vision & valeurs</a></li><li><a href="/ecole#cap2027">Cap 2027</a></li><li><a href="/realisations">Réalisations</a></li><li><a href="/actualites">Actualités</a></li><li><a href="/realisations#temoignages">Témoignages</a></li><li><a href="/phototheque">Photothèque</a></li></ul></div>
      <div class="col-6 col-lg-2"><h6>Formations</h6><ul><li><a href="/formation-design">Design graphique</a></li><li><a href="/formation-community-management">Community management</a></li><li><a href="/formation-bureautique">Bureautique GIA</a></li><li><a href="/studio-creative">Studio Creative</a></li><li><a href="/studio-creative#reco60">RECO 60</a></li><li><a href="/jury-certification">Jury & certification</a></li><li><a href="https://verification.ecolevirtuelledescreatifs.com/">Vérifier un certificat</a></li><li><a href="/admissions">Admissions & tarifs</a></li></ul></div>
      <div class="col-lg-4"><h6>Nous contacter</h6>
        <div class="foot-ct"><span class="foot-flag"><span class="flag" style="width:38px;height:38px" role="img" aria-label="Drapeau : Côte d'Ivoire"><svg width="38" height="38" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#F77F00"/><rect x="20.0" width="20.5" height="60" fill="#fff"/><rect x="40.0" width="20.5" height="60" fill="#009E60"/></svg></span></span><div><b>Côte d'Ivoire</b>
          <a href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener"><svg class="ico"><use href="#i-wa"/></svg>WhatsApp · +225 07 47 25 95 07</a>
          <a href="tel:+2250717258602"><svg class="ico"><use href="#i-phone"/></svg>Appel · +225 07 17 25 86 02</a></div></div>
        <div class="foot-ct"><span class="foot-flag"><span class="flag" style="width:38px;height:38px" role="img" aria-label="Drapeau : France"><svg width="38" height="38" viewBox="0 0 60 60" preserveAspectRatio="xMidYMid slice" aria-hidden="true"><rect x="0.0" width="20.5" height="60" fill="#0055A4"/><rect x="20.0" width="20.5" height="60" fill="#fff"/><rect x="40.0" width="20.5" height="60" fill="#EF4135"/></svg></span></span><div><b>France</b>
          <a href="https://wa.me/33605668823?text=INFORMATION" target="_blank" rel="noopener"><svg class="ico"><use href="#i-wa"/></svg>WhatsApp · +33 6 05 66 88 23</a></div></div>
        <div class="foot-ct"><span class="foot-flag"><svg class="ico"><use href="#i-mail"/></svg></span><div><b>E-mail</b><span>info@ecolevirtuelledescreatifs.com</span></div></div>
      </div>
    </div>
    <div class="legal d-flex flex-wrap justify-content-between gap-2">
      <span>© EVC 2026–2027 · École Virtuelle des Créatifs · SARL de droit ivoirien · Fondateur et DG : Bilé Bossombra</span>
      <span class="foot-legal"><a href="/mentions-legales">Mentions légales</a><a href="/confidentialite">Confidentialité</a><a href="/conditions-utilisation">CGU</a><a href="/conditions-generales-vente">Conditions d'inscription</a><a href="/reglement-interieur">Règlement intérieur</a><a href="/confidentialite#s9" data-ck-open hidden>Gestion des cookies</a></span>
    </div>
  </div>
</footer>

<nav class="m-bar d-lg-none" aria-label="Actions rapides"><a href="/formations"><svg class="ico"><use href="#i-book"/></svg><span>Formations</span></a><a href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener"><svg class="ico"><use href="#i-wa"/></svg><span>WhatsApp</span></a><a href="/preinscription" class="m-bar-cta"><svg class="ico"><use href="#i-arrow"/></svg><span>Préinscription</span></a></nav>
<a class="wa-float d-none d-lg-flex" href="https://wa.me/2250747259507?text=INFORMATION" target="_blank" rel="noopener" aria-label="Écrire sur WhatsApp"><span class="pulse"></span><svg class="ico"><use href="#i-wa"/></svg><span class="d-none d-sm-inline">Une question ?</span></a>
<button class="to-top" id="toTop" aria-label="Retour en haut"><svg class="ico"><use href="#i-up"/></svg></button>

<div class="modal fade" id="lightbox" tabindex="-1" aria-label="Visionneuse" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content"><div class="modal-body">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
    <img loading="lazy" decoding="async" src="" alt=""><div class="lb-cap"></div>
    <button class="lb-btn lb-prev" aria-label="Précédent"><svg class="ico"><use href="#i-left"/></svg></button><button class="lb-btn lb-next" aria-label="Suivant"><svg class="ico"><use href="#i-right"/></svg></button>
  </div></div></div>
</div>

<script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
@endverbatim
@php($analytics = array_filter(['ga4' => config('services.analytics.ga4'), 'pixel' => config('services.analytics.meta_pixel')]))
@if ($analytics)
<script>window.EVC_ANALYTICS = @json($analytics);</script>
<div class="ck-banner" id="ckBanner" role="dialog" aria-labelledby="ckTitle" aria-describedby="ckText" hidden>
  <p class="ck-title" id="ckTitle">Vos choix sur les cookies</p>
  <p class="ck-text" id="ckText">Avec votre accord, nous mesurons la fréquentation du site et l’efficacité de nos campagnes. Aucun de ces outils n’est chargé sans votre accord, et votre refus n’empêche pas l’utilisation du site. <a href="/confidentialite#s9">En savoir plus</a></p>
  <div class="ck-prefs" hidden>
    <label class="ck-opt"><input type="checkbox" checked disabled><span><b>Essentiels</b><small>Session, sécurité des formulaires et préférences d’affichage. Toujours actifs.</small></span></label>
    <label class="ck-opt" data-ck-tool="analytics"><input type="checkbox" data-ck="analytics"><span><b>Mesure d’audience — Google Analytics 4</b><small>Pages consultées, provenance des visites, préinscriptions et clics WhatsApp.</small></span></label>
    <label class="ck-opt" data-ck-tool="marketing"><input type="checkbox" data-ck="marketing"><span><b>Campagnes — Meta Pixel</b><small>Mesure des résultats de nos publicités Facebook et Instagram.</small></span></label>
  </div>
  <div class="ck-actions">
    <button type="button" class="btn btn-outline-secondary btn-sm" data-ck-act="refuse">Tout refuser</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" data-ck-act="custom">Personnaliser</button>
    <button type="button" class="btn btn-outline-secondary btn-sm" data-ck-act="save" hidden>Enregistrer mes choix</button>
    <button type="button" class="btn btn-evc btn-sm" data-ck-act="accept">Tout accepter</button>
  </div>
</div>
@endif
<script src="/assets/site/js/consent.js?v={{ filemtime(public_path('assets/site/js/consent.js')) }}" defer></script>
<script src="/assets/site/js/site.js?v={{ filemtime(public_path('assets/site/js/site.js')) }}" defer></script>
@stack('scripts')
</body>
</html>
