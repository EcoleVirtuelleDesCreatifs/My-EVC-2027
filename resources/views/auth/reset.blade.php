@extends('layouts.auth')
@section('title', $activation ? 'Activer mon compte' : 'Nouveau mot de passe')
@section('content')
<h2 class="l2-t">{{ $activation ? 'Activez votre compte' : 'Choisissez un nouveau mot de passe' }}</h2>
<p class="muted mb-4">{{ $activation ? 'Bienvenue à l\'EVC ! Choisissez le mot de passe de votre espace MY EVC.' : 'Au moins 8 caractères, avec des lettres et des chiffres.' }}</p>
<form method="POST" action="{{ route('password.update') }}" class="d-flex flex-column gap-3">
  @csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <input type="hidden" name="activation" value="{{ $activation ? 1 : 0 }}">
  <div><label class="form-label" for="email">Adresse e-mail</label>
    <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div><div class="d-flex justify-content-between align-items-baseline gap-2"><label class="form-label" for="password">Mot de passe</label><button type="button" class="btn btn-link link-u small p-0" data-password-toggle="password" aria-controls="password" aria-pressed="false" aria-label="Afficher le mot de passe" hidden>Afficher</button></div>
    <input class="form-control @error('password') is-invalid @enderror" type="password" id="password" name="password" required autocomplete="new-password">
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
  <div><div class="d-flex justify-content-between align-items-baseline gap-2"><label class="form-label" for="password_confirmation">Confirmer le mot de passe</label><button type="button" class="btn btn-link link-u small p-0" data-password-toggle="password_confirmation" aria-controls="password_confirmation" aria-pressed="false" aria-label="Afficher la confirmation du mot de passe" hidden>Afficher</button></div>
    <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"></div>
  <button class="btn btn-primary btn-lg w-100" type="submit">{{ $activation ? 'Activer mon compte' : 'Enregistrer' }}</button>
</form>
<script>
  document.querySelectorAll('[data-password-toggle]').forEach(button => {
    const input = document.getElementById(button.dataset.passwordToggle);
    const label = input.id === 'password_confirmation' ? 'la confirmation du mot de passe' : 'le mot de passe';
    button.hidden = false;
    button.addEventListener('click', () => {
      const visible = input.type === 'password';
      input.type = visible ? 'text' : 'password';
      button.textContent = visible ? 'Masquer' : 'Afficher';
      button.setAttribute('aria-pressed', String(visible));
      button.setAttribute('aria-label', `${visible ? 'Masquer' : 'Afficher'} ${label}`);
    });
  });
</script>
@endsection
