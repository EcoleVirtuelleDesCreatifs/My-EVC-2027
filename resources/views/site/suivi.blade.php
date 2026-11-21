@extends('layouts.site')
@section('title', 'Suivre ma candidature · EVC — École Virtuelle des Créatifs')
@section('description', "Suivez l'avancement de votre préinscription à l'École Virtuelle des Créatifs avec votre référence.")
@section('page', 'admissions')

@section('content')
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/classe-large.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><a href="/admissions">Admissions</a><span>/</span><span>Suivi de candidature</span></nav>
    <h1 class="mb-3">Suivre ma <span>candidature</span></h1>
    <p class="mb-0">Saisissez la référence reçue par e-mail après votre préinscription, et l'adresse e-mail utilisée.</p>
  </div>
</header>
<section class="section">
  <div class="container" style="max-width:920px">
    @if ($candidature)
      @php($steps = array_slice(\App\Models\Candidature::STAGES, 0, 5, true))
      <div class="form-panel">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
          <div><span class="kicker">Référence {{ $candidature->reference }}</span>
            <h2 class="title mb-1" style="font-size:1.6rem">Bonjour {{ $candidature->first_name }}</h2>
            <p class="text-muted-evc mb-0">Préinscription du {{ $candidature->created_at->translatedFormat('j F Y') }} · {{ implode(' + ', $candidature->formations) }}</p></div>
        </div>
        @if ($candidature->stage === 'refusee')
          <div class="alert alert-light border">Votre candidature n'a pas été retenue pour cette session. Un conseiller peut vous orienter vers une autre formation ou la prochaine rentrée : écrivez-nous sur WhatsApp.</div>
        @else
          <ol class="list-unstyled d-flex flex-column gap-3 mb-0">
            @foreach ($steps as $key => [$label, $tone, $hint])
              @php($i = $loop->index)
              @php($done = $candidature->stageIndex() >= $i)
              <li class="d-flex gap-3 align-items-start">
                <span class="d-inline-grid flex-none rounded-circle text-white fw-bold" style="width:36px;height:36px;place-items:center;background:{{ $done ? 'var(--ok)' : '#C9CFE0' }}">@if ($done)<svg class="ico"><use href="#i-check"/></svg>@else{{ $i + 1 }}@endif</span>
                <div><b class="{{ $candidature->stage === $key ? 'text-dark' : '' }}">{{ $label }}</b>@if ($candidature->stage === $key)<span class="badge rounded-pill ms-2" style="background:var(--orange)">Étape actuelle</span>@endif
                  <div class="small text-muted-evc">{{ $hint }}</div></div>
              </li>
            @endforeach
          </ol>
          @if ($candidature->stage === 'activee')
            <a href="{{ route('login') }}" class="btn btn-evc btn-lg mt-4">Accéder à MY EVC</a>
          @endif
        @endif
        <hr class="my-4">
        <p class="small text-muted-evc mb-2">Une question sur votre dossier ?</p>
        <a href="https://wa.me/{{ \App\Models\Setting::get('whatsapp_ci') }}?text={{ rawurlencode('Candidature '.$candidature->reference) }}" target="_blank" rel="noopener" class="btn btn-wa d-inline-flex align-items-center gap-2"><svg class="ico"><use href="#i-wa"/></svg> Écrire à un conseiller</a>
      </div>
    @else
      <form method="get" action="{{ route('candidature.suivi') }}" class="form-panel form-evc">
        @if ($error)<div class="alert alert-danger">{{ $error }}</div>@endif
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label" for="ref">Référence</label><input class="form-control" id="ref" name="reference" value="{{ $reference }}" placeholder="PRE-{{ now()->year }}-0001" required></div>
          <div class="col-md-6"><label class="form-label" for="mail">E-mail utilisé</label><input class="form-control" id="mail" type="email" name="email" value="{{ $email }}" required></div>
        </div>
        <button class="btn btn-navy btn-lg mt-4" type="submit">Voir ma candidature</button>
        <p class="small text-muted-evc mt-3 mb-0">Pas encore inscrit ? <a href="/preinscription">Faire ma préinscription</a></p>
      </form>
    @endif
  </div>
</section>
@endsection
