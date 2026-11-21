<article class="nw-card" data-cat="{{ $n->category }}">
  <a href="{{ route('site.actualite', $n) }}" class="nw-media"><img decoding="async" src="{{ $n->cover }}" alt="{{ $n->title }}" loading="lazy"></a>
  <div class="nw-body">
    <div class="nw-meta"><span class="nw-cat">{{ $n->category }}</span><span>{{ $n->label ?: $n->published_at->translatedFormat('F Y') }}</span></div>
    <h3><a href="{{ route('site.actualite', $n) }}">{{ $n->title }}</a></h3>
    <p>{{ $n->excerpt }}</p>
    <a href="{{ route('site.actualite', $n) }}" class="link-arrow">Lire l'article <svg class="ico"><use href="#i-arrow"/></svg></a>
  </div>
</article>
