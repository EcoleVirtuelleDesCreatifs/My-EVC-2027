@extends('layouts.site')
@section('title', 'Actualités · EVC — École Virtuelle des Créatifs')
@section('description', "Rentrées, cérémonies, partenariats, événements et projets : suivez la vie de l'École Virtuelle des Créatifs et de ses apprenants.")
@section('page', 'actualites')

@section('content')
<header class="page-head">
  <div class="bg"><img decoding="async" src="/assets/site/img/caravane.jpg" alt=""></div>
  <div class="container">
    <nav class="crumb" aria-label="Fil d'Ariane"><a href="/">Accueil</a><span>/</span><span>Actualités</span></nav>
    <h1 class="mb-3 reveal">Les actualités <span>de l'EVC</span></h1>
    <p class="mb-0 reveal d1">Rentrées, cérémonies, partenariats, événements et projets : suivez la vie de l'école et de ses apprenants.</p>
  </div>
</header>
<section class="section">
  <div class="container">
    @if ($featured)
      <article class="nw-feat reveal">
        <a href="{{ route('site.actualite', $featured) }}" class="nw-feat-media"><img loading="lazy" decoding="async" src="{{ $featured->cover }}" alt="{{ $featured->title }}"></a>
        <div class="nw-feat-body">
          <div class="nw-meta"><span class="nw-cat">{{ $featured->category }}</span><span>{{ $featured->label }}</span></div>
          <h2><a href="{{ route('site.actualite', $featured) }}">{{ $featured->title }}</a></h2>
          <p>{{ $featured->excerpt }}</p>
          <a href="{{ route('site.actualite', $featured) }}" class="btn btn-navy">Lire l'article</a>
        </div>
      </article>
    @endif
    @if ($categories->count() > 1)
      <div class="filter-bar nw-filter mt-5 reveal">
        <button class="active" data-nf="all">Toutes</button>
        @foreach ($categories as $cat)<button data-nf="{{ $cat }}">{{ $cat }}</button>@endforeach
      </div>
    @endif
    <div class="row g-4 {{ $categories->count() > 1 ? '' : 'mt-4' }}" id="nwGrid">
      @forelse ($news as $n)
        <div class="col-md-6 col-lg-4 nw-col reveal" data-cat="{{ $n->category }}">@include('site.news._card')</div>
      @empty
        @unless ($featured)<p class="text-muted-evc">Les premières actualités arrivent bientôt.</p>@endunless
      @endforelse
    </div>
  </div>
</section>
@include('site.news._cta')
@endsection
