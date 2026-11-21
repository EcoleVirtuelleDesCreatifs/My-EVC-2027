@php
  $u = auth()->user();
  $groups = \App\Support\Menu::groups($u);
  $current = request()->route()?->getName();
  $parents = ['etudiant.projet' => 'etudiant.projets', 'admin.candidature' => 'admin.candidatures', 'admin.etudiant' => 'admin.etudiants', 'formateur.appel' => 'formateur.seances'];
  $isOn = fn ($r) => $current === $r || ($parents[$current] ?? null) === $r;
  $unread = $u->unreadNotifications()->count();
  $spaceName = ['etudiant' => 'Espace étudiant', 'formateur' => 'Espace formateur', 'admin' => 'Administration'][$u->space()];
@endphp
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'MY EVC' }} · MY EVC</title>
<meta name="theme-color" content="#1E1B4B">
<link rel="icon" type="image/svg+xml" href="/assets/app/img/symbole-couleur.svg">
<link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="/assets/vendor/fontawesome/css/all.min.css">
<link rel="stylesheet" href="/assets/app/css/tokens.css">
<link rel="stylesheet" href="/assets/app/css/app.css">
<link rel="stylesheet" href="/assets/app/css/complements.css">
<link rel="stylesheet" href="/assets/app/css/bibliotheque.css">
<link rel="stylesheet" href="/assets/app/css/myevc.css">
<script data-navigate-once>try{const t=localStorage.getItem('myevc-theme');if(t&&t!=='auto')document.documentElement.setAttribute('data-theme',t)}catch(e){}</script>
<script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js" data-navigate-once></script>
<script src="/assets/app/js/myevc.js" data-navigate-once></script>
</head>
<body>
<div class="app">
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Menu principal"><div class="side-inner">
    <div class="d-flex align-items-center">
      <a class="brand" href="{{ route($u->homeRoute()) }}" wire:navigate aria-label="MY EVC — accueil"><x-logo variant="symbole-negatif" class="logo-sym u-sym" /><x-logo variant="logotype-court-negatif" class="logo-type" /></a>
      <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Fermer"></button>
    </div>
    <div class="space-tag">{{ $spaceName }}</div>
    <ul class="menu">
      @foreach ($groups as [$label, $items, $fold])
        @php($links = collect($items)->map(fn ($i) => ['r' => $i[0], 'l' => $i[1], 'ic' => $i[2], 'n' => $i[3] ?? 0, 'on' => $isOn($i[0])]))
        @if (! $fold)
          @foreach ($links as $i)
            <li><a href="{{ route($i['r']) }}" wire:navigate data-tip="{{ $i['l'] }}" class="{{ $i['on'] ? 'active' : '' }}" @if ($i['on']) aria-current="page" @endif><i class="fa-solid {{ $i['ic'] }}"></i><span>{{ $i['l'] }}</span>@if ($i['n'])<span class="count">{{ $i['n'] }}</span>@endif</a></li>
          @endforeach
        @else
          @php($open = $links->contains('on', true))
          <li class="menu-fold"><details {{ $open ? 'open' : '' }}><summary class="menu-label"><span>{{ $label }}</span>@if (! $open && $links->sum('n'))<span class="count">{{ $links->sum('n') }}</span>@endif<i class="fa-solid fa-chevron-down chev"></i></summary>
            <ul class="menu sub">@foreach ($links as $i)<li><a href="{{ route($i['r']) }}" wire:navigate data-tip="{{ $i['l'] }}" class="{{ $i['on'] ? 'active' : '' }}"><i class="fa-solid {{ $i['ic'] }}"></i><span>{{ $i['l'] }}</span>@if ($i['n'])<span class="count">{{ $i['n'] }}</span>@endif</a></li>@endforeach</ul></details></li>
        @endif
      @endforeach
    </ul>
    <div class="u-user">
      <a class="uu-top text-decoration-none" href="{{ route('myevc.profil') }}" wire:navigate title="Mon profil">
        <span class="ini">@if ($u->avatarUrl())<img src="{{ $u->avatarUrl() }}" alt="">@else{{ $u->initials() }}@endif</span>
        <span style="min-width:0"><b>{{ $u->name }}</b><small>{{ $u->space() === 'etudiant' ? $u->student?->matricule.' · '.$u->student?->formations->pluck('code')->implode(' + ') : $u->roleLabel() }}</small></span>
      </a>
      <div class="uu-act">
        <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf<button class="uu-out" type="submit"><i class="fa-solid fa-right-from-bracket"></i>Se déconnecter</button></form>
        <button class="uu-ic" type="button" data-theme-toggle title="Changer de thème" aria-label="Changer de thème"><i class="fa-solid fa-circle-half-stroke"></i></button>
      </div>
    </div>
  </div></aside>

  <div class="main">
    <header class="topbar">
      <button class="btn btn-quiet btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Ouvrir le menu"><i class="fa-solid fa-bars"></i></button>
      <div class="crumb"><small>{!! $crumb ?? e($spaceName) !!}</small><h1>{{ $title ?? '' }}</h1></div>
      <div class="top-actions">
        <a class="btn btn-icon bell" href="{{ route('myevc.notifications') }}" wire:navigate aria-label="Notifications{{ $unread ? ' ('.$unread.' non lues)' : '' }}"><i class="fa-solid fa-bell"></i>@if ($unread)<span class="dot"></span>@endif</a>
        @isset($cta)<span class="d-none d-sm-inline-flex">{!! $cta !!}</span>@endisset
      </div>
    </header>
    <main class="content" id="view">
      @if (session('toast'))<div data-flash-toast="{{ session('toast') }}" hidden></div>@endif
      {{ $slot }}
    </main>
  </div>

  <nav class="bottom-nav" aria-label="Navigation rapide">
    @foreach (\App\Support\Menu::bottom($u) as [$r, $l, $ic])
      <a href="{{ route($r) }}" wire:navigate class="{{ $isOn($r) ? 'on' : '' }}"><i class="fa-solid {{ $ic }}"></i><span class="lbl">{{ $l }}</span></a>
    @endforeach
    <button type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-label="Tout le menu"><i class="fa-solid fa-grip"></i><span class="lbl">Menu</span></button>
  </nav>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toasts" style="z-index:1100"></div>
</body>
</html>
