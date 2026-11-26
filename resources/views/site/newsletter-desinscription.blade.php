@extends('layouts.site')
@section('title', 'Désinscription de la newsletter · EVC — École Virtuelle des Créatifs')
@section('description', "Confirmation de désinscription de la newsletter de l'École Virtuelle des Créatifs.")
@section('page', 'contact')

@section('content')
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/classe-large.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Newsletter</span></nav>
    <h1 class="mb-3">Newsletter <span>EVC</span></h1>
  </div>
</header>
<section class="section">
  <div class="container" style="max-width:640px">
    @if ($ok)
      <div class="card p-4 text-center">
        <p class="fs-2 mb-2">✓</p>
        <h2 class="h4 mb-2">Vous êtes désabonné</h2>
        <p class="text-muted mb-0">L'adresse <strong>{{ $email }}</strong> ne recevra plus la newsletter de l'EVC. Vous pouvez vous réabonner à tout moment depuis le pied de page du site.</p>
        <a href="/" class="btn btn-evc mt-4">Retour à l'accueil</a>
      </div>
    @else
      <div class="card p-4 text-center">
        <h2 class="h4 mb-2">Lien invalide</h2>
        <p class="text-muted mb-0">Ce lien de désinscription est invalide ou a déjà été utilisé. Si vous recevez encore nos e-mails, répondez-y simplement ou écrivez-nous via la page contact.</p>
        <a href="/contact" class="btn btn-evc mt-4">Nous contacter</a>
      </div>
    @endif
  </div>
</section>
@endsection
