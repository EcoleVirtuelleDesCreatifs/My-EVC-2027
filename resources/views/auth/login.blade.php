@extends('layouts.auth')
@section('title', 'Connexion')
@section('content')
<h2 class="l2-t">Connexion à votre espace</h2>
<p class="muted mb-4">Étudiants, formateurs et équipe administrative.</p>
@if (session('status'))<div class="alert alert-success small">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('login.attempt') }}" class="d-flex flex-column gap-3" novalidate>
  @csrf
  <div><label class="form-label" for="login">Adresse e-mail ou matricule</label>
    <input class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" required autocomplete="username" autofocus placeholder="prenom.nom@exemple.com ou EVC-26-0142">
    @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div><div class="d-flex justify-content-between align-items-baseline"><label class="form-label" for="password">Mot de passe</label><a href="{{ route('password.request') }}" class="link-u small">Mot de passe oublié ?</a></div>
    <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="current-password">
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <label class="d-flex gap-2 align-items-center small"><input class="form-check-input mt-0" type="checkbox" name="remember" value="1" checked>Rester connecté sur cet appareil</label>
  <button class="btn btn-primary btn-lg w-100" type="submit">Se connecter</button>
  <div class="divider-t">Nouveau à l'EVC ?</div>
  <a class="btn btn-ghost btn-lg w-100" href="/preinscription">Faire ma préinscription</a>
  <a class="small link-u text-center" href="{{ route('candidature.suivi') }}">Suivre ma candidature</a>
</form>
@if (app()->environment('local'))
  <details class="l2-demo mt-4"><summary><i class="fa-solid fa-sliders"></i>Comptes de démonstration</summary>
    <p class="small muted mt-2 mb-1">Mot de passe : <code>password</code></p>
    <ul class="small mb-0 ps-3"><li>Étudiante : <code>EVC-26-0142</code> (Aya Kouassi)</li><li>Formateur : <code>marc.yao@evc.test</code></li><li>Administration : <code>admin@evc.test</code></li><li>Finances : <code>finance@evc.test</code></li></ul>
  </details>
@endif
@endsection
