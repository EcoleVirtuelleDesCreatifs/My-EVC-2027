<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@yield('title', 'Connexion') · MY EVC</title>
<meta name="theme-color" content="#1E1B4B">
<link rel="icon" type="image/svg+xml" href="/assets/app/img/symbole-couleur.svg">
<link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
<link rel="stylesheet" href="/assets/app/css/tokens.css">
<link rel="stylesheet" href="/assets/app/css/app.css">
<link rel="stylesheet" href="/assets/app/css/complements.css">
<script>try{const t=localStorage.getItem('myevc-theme');if(t&&t!=='auto')document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>
</head>
<body>
<div class="login2">
  <div class="l2-art">
    <a href="{{ route('site.accueil') }}" class="l2-logo" aria-label="Site de l'EVC"><x-logo variant="logo-horizontal-negatif" /></a>
    <a class="l2-back" href="{{ route('site.accueil') }}"><i class="fa-solid fa-arrow-left"></i>Retour au site de l'EVC</a>
    <div class="l2-copy"><h1>De la préinscription à la <em>certification</em>, un seul parcours.</h1>
      <p>Formations, séances, travaux pratiques, bibliothèque, paiements et certificats : tout votre parcours EVC au même endroit.</p></div>
  </div>
  <div class="l2-form"><div class="box">
    <div class="l2-mlogo"><x-logo variant="logo-horizontal-couleur" /></div>
    <a class="l2-back l2-back-m" href="{{ route('site.accueil') }}"><i class="fa-solid fa-arrow-left"></i>Site de l'EVC</a>
    @yield('content')
  </div></div>
</div>
</body>
</html>
