<div class="u-grid u-2-1 align-items-start">
  <div class="stack">
    <div class="tv-player">
      @if ($v->embedUrl())
        <iframe src="{{ $v->embedUrl() }}" title="{{ $v->title }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
      @elseif ($v->path)
        <video controls playsinline preload="metadata" src="{{ route('fichier.video', $v) }}"
          x-data="{ last: 0 }"
          x-init="$el.addEventListener('loadedmetadata', () => { const s = {{ (int) ($p?->seconds ?? 0) }}; if (s > 5 && s < $el.duration - 5) $el.currentTime = s })"
          x-on:timeupdate="if (Math.abs($el.currentTime - last) >= 10) { last = $el.currentTime; $wire.progression(Math.floor($el.currentTime), Math.floor($el.duration)) }"
          x-on:ended="$wire.progression(Math.floor($el.duration), Math.floor($el.duration))"></video>
      @else
        <div class="d-grid h-100 text-white" style="place-items:center;position:absolute;inset:0">Vidéo bientôt disponible</div>
      @endif
    </div>
    @if ($v->externalUrl())
      <div class="alert-strip as-info d-flex flex-wrap align-items-center gap-3">
        <div class="flex-grow-1"><strong>Un problème de lecture ?</strong><p class="small mb-0">Si la plateforme affiche « Vidéo non disponible », ouvrez la vidéo directement. Certaines vidéos sont soumises à des restrictions de lecture intégrée, d’âge ou de région.</p></div>
        <a class="btn btn-ghost btn-sm" href="{{ $v->externalUrl() }}" target="_blank" rel="noopener noreferrer">Regarder sur {{ $v->source === 'youtube' ? 'YouTube' : 'Vimeo' }}<i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
      </div>
    @endif
    <x-panel :title="$v->title">
      <x-slot:tools>@if ($p?->completed_at)<x-pill tone="ok">Vue</x-pill>@else<button class="btn btn-ghost btn-sm" wire:click="marquerVue"><i class="fa-solid fa-check"></i>Marquer comme vue</button>@endif</x-slot:tools>
      <div class="d-flex flex-wrap gap-3 small muted mb-2"><span><x-fchip :code="$v->formation->code" /> {{ $v->module }}</span>@if ($v->duration_min)<span><i class="fa-regular fa-clock me-1"></i>{{ $v->durationLabel() }}</span>@endif
        @if ($v->seance)<span><i class="fa-regular fa-calendar me-1"></i>Séance du {{ $v->seance->starts_at->translatedFormat('j F Y') }}</span>@endif<span><i class="fa-regular fa-eye me-1"></i>{{ $v->views_count }} vues</span></div>
      @if ($v->description)<p class="mb-0" style="white-space:pre-line">{{ $v->description }}</p>@endif
      @if ($p && $p->percent && ! $p->completed_at)<div class="mt-3"><div class="small muted mb-1">Progression : {{ $p->percent }} %</div><x-bar :pct="$p->percent" tone="cor" /></div>@endif
    </x-panel>
  </div>
  <x-panel title="Dans le même module" :p0="true">
    @forelse ($suite as $o)
      <a class="row-item text-decoration-none" href="{{ route('etudiant.video', $o) }}" wire:navigate><span class="icon-sq"><i class="fa-solid {{ $prog->get($o->id)?->completed_at ? 'fa-check' : 'fa-play' }}"></i></span><div class="ri-main"><div class="ri-t">{{ $o->title }}</div><div class="ri-s">{{ $o->durationLabel() }}</div></div></a>
    @empty
      <div class="p-4 small muted">Pas d'autre vidéo dans ce module.</div>
    @endforelse
  </x-panel>
</div>
