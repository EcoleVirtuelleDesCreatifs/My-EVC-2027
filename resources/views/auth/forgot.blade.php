@extends('layouts.auth')
@section('title', 'Mot de passe oublié')
@section('content')
<h2 class="l2-t">Mot de passe oublié</h2>
<p class="muted mb-4">Indiquez l'e-mail de votre compte : nous vous envoyons un lien pour choisir un nouveau mot de passe.</p>
@if (session('status'))<div class="alert alert-success small">{{ session('status') }}</div>@endif
<form method="POST" action="{{ route('password.email') }}" class="d-flex flex-column gap-3">
  @csrf
  <div><label class="form-label" for="email">Adresse e-mail</label>
    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <button class="btn btn-primary btn-lg w-100" type="submit">Recevoir le lien</button>
  <a class="btn btn-ghost w-100" href="{{ route('login') }}">Retour à la connexion</a>
</form>
@endsection
