@extends('layouts.site')
@section('title', $news->title.' · EVC — École Virtuelle des Créatifs')
@section('description', $news->excerpt)
@section('og_image', $news->cover)
@section('page', 'actualites')

@section('content')
<header class="art-head">
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><a href="{{ route('site.actualites') }}">Actualités</a><span>/</span><span>{{ $news->category }}</span></nav>
    <div class="nw-meta mb-3"><span class="nw-cat">{{ $news->category }}</span><span>{{ $news->label ?: $news->published_at->translatedFormat('j F Y') }}</span><span>· {{ $news->readingTime() }} min de lecture</span></div>
    <h1>{{ $news->title }}</h1>
    <p class="art-lead">{{ $news->excerpt }}</p>
    <div class="art-by"><img loading="lazy" decoding="async" src="/assets/site/img/logo.png" alt=""><div><b>Rédaction EVC</b><small>Publié le {{ $news->published_at->translatedFormat('j F Y') }}</small></div></div>
  </div>
</header>
<div class="container"><figure class="art-cover"><img decoding="async" src="{{ $news->cover }}" alt="{{ $news->title }}"></figure></div>
<section class="pb-5">
  <div class="container"><div class="row g-5">
    <div class="col-lg-1 d-none d-lg-block"><div class="share-sticky">
      <div class="share vertical" data-share data-title="{{ $news->title }}" data-url="{{ route('site.actualite', $news) }}">
  <span class="share-l">Partager</span>
  <a data-net="whatsapp" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp" style="--c:#25D366"><svg class="ico"><use href="#i-wa"/></svg></a>
  <a data-net="facebook" target="_blank" rel="noopener" aria-label="Partager sur Facebook" style="--c:#1877F2"><svg class="ico" viewBox="0 0 24 24"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v2H8v4h2v8h4v-8h3l1-4h-4V8z" fill="currentColor" stroke="none"/></svg></a>
  <a data-net="linkedin" target="_blank" rel="noopener" aria-label="Partager sur LinkedIn" style="--c:#0A66C2"><svg class="ico" viewBox="0 0 24 24"><path d="M4 9h4v12H4zM6 3a2 2 0 1 1 0 4 2 2 0 0 1 0-4zM10 9h4v2c.6-1.1 2-2.2 4-2.2 4 0 4.5 2.6 4.5 6V21h-4v-5.4c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21h-4z" fill="currentColor" stroke="none"/></svg></a>
  <a data-net="x" target="_blank" rel="noopener" aria-label="Partager sur X" style="--c:#111"><svg class="ico" viewBox="0 0 24 24"><path d="M4 4l7.2 9.6L4.4 20H6l6-5.4 4.2 5.4H20l-7.5-10L19 4h-1.6l-5.6 5.1L8 4z" fill="currentColor" stroke="none"/></svg></a>
  <a data-net="email" aria-label="Partager par e-mail" style="--c:#3563AD"><svg class="ico"><use href="#i-mail"/></svg></a>
  <button type="button" data-copy aria-label="Copier le lien" style="--c:#F39822"><svg class="ico" viewBox="0 0 24 24"><path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg><span class="copied">Lien copié !</span></button>
</div>
    </div></div>
    <article class="col-lg-7 art-body">{!! $news->bodyHtml() !!}</article>
    <aside class="col-lg-4">
      @if ($others->isNotEmpty())
        <div class="nw-side"><h6>À lire aussi</h6>
          @foreach ($others as $o)
            <a class="nw-side-item" href="{{ route('site.actualite', $o) }}"><img loading="lazy" decoding="async" src="{{ $o->cover }}" alt=""><span><small>{{ $o->category }}</small>{{ $o->title }}</span></a>
          @endforeach
        </div>
      @endif
      <div class="nw-side-cta"><h5 class="text-white">Rentrée {{ now()->year }}–{{ now()->year + 1 }}</h5><p>Inscriptions ouvertes. Un conseiller vous oriente sur WhatsApp.</p><a href="/preinscription" class="btn btn-evc w-100">Préinscription</a></div>
    </aside>
  </div></div>
</section>
@if ($others->count() >= 3)
<section class="section bg-cream"><div class="container">
  <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4"><h2 class="title mb-0">Autres <span class="hl">actualités</span></h2><a href="{{ route('site.actualites') }}" class="link-arrow">Toutes les actualités <svg class="ico"><use href="#i-arrow"/></svg></a></div>
  <div class="row g-4">@foreach ($others->take(3) as $n)<div class="col-md-4">@include('site.news._card')</div>@endforeach</div>
</div></section>
@endif
@include('site.news._cta')
@endsection
