@php
  $myevc = request()->is('myevc', 'myevc/*');
  $user = auth()->user();
  $links = $myevc
      ? array_filter([
          $user ? [route($user->homeRoute()), 'fa-house', 'Mon tableau de bord', 'Retour à votre espace MY EVC'] : [route('login'), 'fa-right-to-bracket', 'Se connecter', 'Accéder à votre espace MY EVC'],
          $user ? [route('myevc.messagerie'), 'fa-message', 'Messagerie', 'Écrire à l’équipe de l’EVC'] : null,
          ['/', 'fa-globe', 'Site de l’école', 'Découvrir l’École Virtuelle des Créatifs'],
      ])
      : [
          ['/formations', 'fa-graduation-cap', 'Nos formations', 'Design graphique, community management, bureautique'],
          ['/preinscription', 'fa-pen-to-square', 'Préinscription', 'Rejoindre la prochaine rentrée'],
          ['/actualites', 'fa-newspaper', 'Actualités', 'La vie de l’école et nos événements'],
          ['/contact', 'fa-headset', 'Contact', 'Une question ? Nous vous répondons'],
      ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>Page introuvable | {{ $myevc ? 'MY EVC' : 'EVC Abidjan' }}</title>
<link rel="icon" href="/assets/site/img/logo.png">
<link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;font-family:Montserrat,system-ui,sans-serif;color:#fff;background:#0E1A3C;display:flex;flex-direction:column;overflow-x:hidden}
  .bg{position:fixed;inset:0;z-index:-1;background:radial-gradient(circle at 85% 105%,rgba(243,152,34,.45),transparent 45%),radial-gradient(circle at 8% 12%,rgba(53,99,173,.55),transparent 40%),linear-gradient(135deg,#0E1A3C,#17265A 55%,#22337A)}
  .bg::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(120deg,rgba(255,255,255,.03) 0 2px,transparent 2px 28px)}
  header{padding:1.5rem clamp(1.25rem,5vw,4rem)}
  header img{height:46px}
  main{flex:1;display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:clamp(2rem,5vw,5rem);align-items:center;padding:1rem clamp(1.25rem,5vw,4rem) 3rem;max-width:1280px;margin:0 auto;width:100%}
  .code{font-size:clamp(6rem,18vw,13rem);font-weight:900;line-height:.9;letter-spacing:-.06em;margin:0}
  .code span{display:inline-block;animation:float 4s ease-in-out infinite;background:linear-gradient(160deg,#fff 15%,#FFB04A 60%,#F39822);-webkit-background-clip:text;background-clip:text;color:transparent}
  .code span:nth-child(2){animation-delay:.4s}.code span:nth-child(3){animation-delay:.8s}
  @keyframes float{50%{transform:translateY(-10px)}}
  .kicker{display:inline-block;margin:1.5rem 0 .6rem;padding:.35rem .8rem;border-radius:99px;background:rgba(243,152,34,.15);border:1px solid rgba(243,152,34,.4);color:#FFB04A;font-size:.75rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
  h1{font-size:clamp(1.6rem,3.4vw,2.6rem);line-height:1.12;margin:0 0 .8rem;font-weight:800;letter-spacing:-.02em}
  p.lead{color:#C2CAE6;font-size:1rem;line-height:1.65;max-width:52ch;margin:0 0 1.8rem}
  .path{font-family:ui-monospace,Menlo,monospace;font-size:.82rem;color:#AEB7D8;background:rgba(255,255,255,.06);padding:.15rem .45rem;border-radius:6px;word-break:break-all}
  .actions{display:flex;flex-wrap:wrap;gap:.75rem}
  .btn{display:inline-flex;align-items:center;gap:.55rem;padding:.85rem 1.4rem;border-radius:99px;font-weight:700;font-size:.92rem;text-decoration:none;transition:transform .2s,box-shadow .2s,background .2s;border:0;cursor:pointer;font-family:inherit}
  .btn:hover{transform:translateY(-2px)}
  .btn-main{background:#F39822;color:#0E1A3C;box-shadow:0 14px 30px -12px rgba(243,152,34,.8)}
  .btn-ghost{background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.22)}
  .btn-ghost:hover{background:rgba(255,255,255,.14)}
  .cards{display:grid;gap:.75rem}
  .card{display:flex;align-items:center;gap:1rem;padding:1rem 1.1rem;border-radius:18px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:#fff;text-decoration:none;backdrop-filter:blur(8px);transition:transform .25s,background .25s,border-color .25s}
  .card:hover{transform:translateX(6px);background:rgba(255,255,255,.1);border-color:rgba(243,152,34,.5)}
  .card i.ic{width:46px;height:46px;border-radius:14px;display:grid;place-items:center;flex:none;background:linear-gradient(135deg,#F39822,#FFB04A);color:#0E1A3C;font-size:1.15rem}
  .card b{display:block;font-size:.98rem}.card small{color:#AEB7D8;font-size:.8rem}
  .card .go{margin-left:auto;color:#FFB04A}
  .cards-t{font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#AEB7D8;margin:0 0 .25rem}
  footer{padding:1.25rem clamp(1.25rem,5vw,4rem);color:#8F9AC4;font-size:.8rem;display:flex;flex-wrap:wrap;gap:.5rem 1.5rem;justify-content:space-between}
  footer a{color:#C2CAE6;text-decoration:none}footer a:hover{color:#FFB04A}
  @media(max-width:880px){main{grid-template-columns:1fr}}
  @media(prefers-reduced-motion:reduce){.code span{animation:none}.btn,.card{transition:none}}
</style>
</head>
<body>
<div class="bg" aria-hidden="true"></div>
<header><a href="{{ $myevc && $user ? route($user->homeRoute()) : '/' }}"><img src="/assets/site/img/logo-white.png" alt="EVC — École Virtuelle des Créatifs"></a></header>
<main>
  <section>
    <p class="code" aria-hidden="true"><span>4</span><span>0</span><span>4</span></p>
    <span class="kicker">Page introuvable</span>
    <h1>{{ $myevc ? 'Cette page n’existe pas dans votre espace MY EVC.' : 'Oups, cette page s’est envolée.' }}</h1>
    <p class="lead">L’adresse <span class="path">/{{ ltrim(request()->path(), '/') }}</span> ne correspond à aucune page. Le lien est peut-être ancien, incomplet ou la page a été déplacée.</p>
    <div class="actions">
      @if ($myevc)
        <a class="btn btn-main" href="{{ $user ? route($user->homeRoute()) : route('login') }}"><i class="fa-solid fa-arrow-left"></i>{{ $user ? 'Revenir à mon espace' : 'Se connecter à MY EVC' }}</a>
      @else
        <a class="btn btn-main" href="/"><i class="fa-solid fa-house"></i>Retour à l’accueil</a>
        <a class="btn btn-ghost" href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_ci') }}?text={{ rawurlencode('Bonjour EVC, je cherche une information sur votre site.') }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>Nous écrire</a>
      @endif
      <button class="btn btn-ghost" type="button" onclick="history.length > 1 ? history.back() : location.href='/'"><i class="fa-solid fa-rotate-left"></i>Page précédente</button>
    </div>
  </section>
  <nav class="cards" aria-label="Pages utiles">
    <p class="cards-t">Vous cherchiez peut-être</p>
    @foreach ($links as [$href, $icon, $title, $sub])
      <a class="card" href="{{ $href }}"><i class="ic fa-solid {{ $icon }}"></i><span><b>{{ $title }}</b><small>{{ $sub }}</small></span><i class="go fa-solid fa-arrow-right"></i></a>
    @endforeach
  </nav>
</main>
<footer><span>© EVC · École Virtuelle des Créatifs · Abidjan</span><span><a href="/">Accueil</a> · <a href="/contact">Contact</a> · <a href="{{ route('login') }}">MY EVC</a></span></footer>
</body>
</html>
