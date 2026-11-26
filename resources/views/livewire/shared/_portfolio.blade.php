@php
  $admin = $admin ?? false;
  $ro = ! $admin && \App\Support\LectureSeule::applies(auth()->user());
  $icons = ['DG' => 'fa-pen-ruler', 'CM' => 'fa-hashtag', 'GIA' => 'fa-table-cells'];
  $mosaic = $items->flatMap(fn ($a) => $a->visuals->take(2)->map(fn ($v) => $v + ['code' => $a->project->formation->code]))->take(4)->values();
  $size = fn (int $b) => $b >= 1048576 ? number_format($b / 1048576, 1, ',', ' ').' Mo' : max(1, round($b / 1024)).' Ko';
  $open = fn (array $d) => match ($d['kind']) {
      'link' => $d['url'],
      'image' => route('fichier.depot.apercu', [$d['file'], 'w' => 1600]),
      default => ! empty($d['inline']) ? route('fichier.depot.apercu', $d['file']) : route('fichier.depot', $d['file']),
  };
@endphp
  <section class="pf-cover" aria-labelledby="pfTitle">
    <div class="pf-glow" aria-hidden="true"></div>
    <div class="pf-cover-text">
      <x-logo variant="logo-horizontal-blanc" class="pf-logo" />
      <p class="pf-eyebrow">Portfolio de projets validés</p>
      <h1 id="pfTitle">{{ $s->user->name }}</h1>
      <p class="pf-sub">{{ $s->formations->pluck('name')->implode(' · ') }}@if ($s->promotion) <span>· {{ $s->promotion->name }}</span>@endif @if ($admin)<span>· {{ $s->matricule }}</span>@endif</p>
      <div class="pf-stats">
        <div><b>{{ $stats['total'] }}</b><span>projet{{ $stats['total'] > 1 ? 's' : '' }} validé{{ $stats['total'] > 1 ? 's' : '' }}</span></div>
        <div><b>{{ $stats['deliverables'] }}</b><span>livrable{{ $stats['deliverables'] > 1 ? 's' : '' }}</span></div>
        <div><b>{{ $stats['distinctions'] }}</b><span>Excellent ou Très bien</span></div>
      </div>
      <div class="d-flex flex-wrap gap-2 mt-4">
        @if ($stats['total'])
          <a class="btn btn-primary" href="{{ route('pdf.portfolio', $s) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>Télécharger le catalogue PDF</a>
        @endif
        @if ($admin)
          <a class="btn pf-btn-light" href="{{ route('admin.etudiant', $s) }}" wire:navigate><i class="fa-solid fa-user-graduate"></i>Fiche étudiant</a>
          <a class="btn pf-btn-light" href="{{ route('admin.portfolios') }}" wire:navigate><i class="fa-solid fa-arrow-left"></i>Tous les portfolios</a>
        @else
          @unless ($ro)<a class="btn pf-btn-light" href="{{ route('etudiant.projets') }}" wire:navigate><i class="fa-solid fa-pen-nib"></i>Mes projets en cours</a>@endunless
        @endif
      </div>
    </div>
    <div class="pf-mosaic" aria-hidden="true">
      @forelse ($mosaic as $i => $v)
        <figure class="c-{{ $v['code'] }}" style="aspect-ratio:{{ $v['width'] }} / {{ $v['height'] }}"><img src="{{ route('fichier.depot.apercu', [$v['file'], 'w' => 480]) }}" width="{{ $v['width'] }}" height="{{ $v['height'] }}" alt="" loading="{{ $i < 3 ? 'eager' : 'lazy' }}" decoding="async"></figure>
      @empty
        @foreach ($s->formations->take(3) as $fo)
          <figure class="pf-ph c-{{ $fo->code }}" style="aspect-ratio:4 / 3"><i class="fa-solid {{ $icons[$fo->code] ?? 'fa-star' }}"></i></figure>
        @endforeach
      @endforelse
    </div>
  </section>

  @if (! $admin)
    @include('livewire.etudiant._fsel')
  @endif

  @if ($items->isEmpty())
    <x-empty :title="$admin ? 'Aucun projet validé pour le moment' : 'Votre portfolio se construit projet après projet'" :text="$admin ? 'Les projets validés de cet étudiant apparaîtront ici avec tous leurs livrables.' : 'Chaque projet validé par vos formateurs rejoint automatiquement ce catalogue, avec tous ses livrables et l’avis reçu.'" />
  @else
    <div class="pf-grid">
      @foreach ($items as $a)
        @php($code = $a->project->formation->code)
        @php($main = $a->visuals->first() ?? $a->deliverables->firstWhere('kind', 'video'))
        <article class="pf-card c-{{ $code }}">
          <div class="pf-media {{ $main ? 'pf-'.$main['kind'] : '' }}">
            @if ($main && $main['kind'] === 'image')
              <a href="{{ $open($main) }}" target="_blank" rel="noopener" aria-label="Agrandir {{ $main['name'] }}">
                <img src="{{ route('fichier.depot.apercu', [$main['file'], 'w' => 960]) }}" width="{{ $main['width'] }}" height="{{ $main['height'] }}" alt="Visuel du projet {{ $a->project->title }}" loading="lazy" decoding="async">
              </a>
            @elseif ($main)
              <video controls preload="metadata" src="{{ route('fichier.depot.apercu', $main['file']) }}" aria-label="Vidéo du projet {{ $a->project->title }}"></video>
            @else
              <a class="pf-ph" href="{{ $admin ? route('admin.etudiant', $s) : ($ro ? '#' : route('etudiant.projet', $a)) }}" wire:navigate aria-label="Voir {{ $a->project->title }}"><i class="fa-solid {{ $a->deliverables->first()['icon'] ?? ($icons[$code] ?? 'fa-star') }}"></i></a>
            @endif
            @if ($a->appreciation)<span class="pf-badge {{ in_array($a->appreciation, ['Excellent', 'Très bien']) ? 'top' : '' }}"><i class="fa-solid fa-award"></i>{{ $a->appreciation }}</span>@endif
          </div>
          <div class="pf-body">
            <div class="d-flex align-items-center gap-2 flex-wrap"><x-fchip :code="$code" /><span class="small muted">{{ $a->project->type }}</span></div>
            <h2>{{ $a->project->title }}</h2>
            @if ($a->review)<blockquote>« {{ \Illuminate\Support\Str::limit($a->review, 180) }} »@if ($a->reviewer)<cite>{{ $a->reviewer }}</cite>@endif</blockquote>@endif
            @if ($a->deliverables->isNotEmpty())
              <div class="pf-dl">
                <p class="pf-dl-t">{{ $a->deliverables->count() }} livrable{{ $a->deliverables->count() > 1 ? 's' : '' }}</p>
                <ul>
                  @foreach ($a->deliverables as $d)
                    <li>
                      <a href="{{ $open($d) }}" target="_blank" rel="noopener" @if ($d['kind'] !== 'link' && empty($d['inline']) && $d['kind'] !== 'image') download @endif title="{{ $d['name'] }}">
                        @if ($d['kind'] === 'image')
                          <span class="pf-dl-thumb" style="aspect-ratio:{{ $d['width'] }} / {{ $d['height'] }}"><img src="{{ route('fichier.depot.apercu', [$d['file'], 'w' => 480]) }}" width="{{ $d['width'] }}" height="{{ $d['height'] }}" alt="" loading="lazy" decoding="async"></span>
                        @else
                          <span class="pf-dl-ico"><i class="fa-solid {{ $d['icon'] }}"></i></span>
                        @endif
                        <span class="pf-dl-name"><b>{{ $d['name'] }}</b><small>{{ collect([$d['label'], $d['kind'] === 'image' ? $d['width'].' × '.$d['height'].' px · '.$d['orientation'] : null, $d['kind'] !== 'link' && $d['size'] ? $size($d['size']) : null])->filter()->implode(' · ') }}</small></span>
                        <i class="fa-solid {{ $d['kind'] === 'link' ? 'fa-arrow-up-right-from-square' : (in_array($d['kind'], ['image', 'video']) || ! empty($d['inline']) ? 'fa-eye' : 'fa-download') }} muted"></i>
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div>
            @endif
            <div class="pf-foot">
              <span class="small muted"><i class="fa-regular fa-calendar-check me-1"></i>Validé le {{ $a->validated_at?->translatedFormat('j F Y') }}</span>
              @if ($admin)
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.etudiant', $s) }}" wire:navigate>Suivi du projet</a>
              @else
                @unless ($ro)<a class="btn btn-ghost btn-sm" href="{{ route('etudiant.projet', $a) }}" wire:navigate>Voir le projet</a>@endunless
              @endif
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif
