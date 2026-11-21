@php($n = fn ($c) => $c.' ouvrage'.($c > 1 ? 's' : ''))
<div>
  @include('livewire.etudiant._fsel')
  <section class="lib-hall">
    <div class="lh-txt">
      <span class="lh-k"><i class="fa-solid fa-building-columns"></i>Bibliothèque numérique EVC</span>
      <h2>Tous vos ouvrages, rangés par rayon</h2>
      <p>{{ $n($all->count()) }} · {{ $counts->count() }} rayons · {{ $s->formations->pluck('code')->implode(', ') }}@if ($lastUpdate) · mis à jour {{ $lastUpdate->diffForHumans() }}@endif</p>
      <div class="lh-search">
        <label class="search-field mb-0 flex-grow-1"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" placeholder="Rechercher un livre, un logiciel, une police…" wire:model.live.debounce.300ms="q" aria-label="Rechercher dans la bibliothèque"></label>
        <select class="form-select" wire:model.live="tri" aria-label="Trier les ouvrages"><option value="recent">Plus récents</option><option value="populaire">Plus téléchargés</option><option value="az">De A à Z</option></select>
      </div>
    </div>
    <div class="lh-stack" aria-hidden="true">@foreach ($vitrine as $r)<x-book :r="$r" />@endforeach</div>
  </section>

  <div class="lib-layout">
    <nav class="lib-rayons" aria-label="Rayons de la bibliothèque">
      <button type="button" class="ray {{ $rayon === '' && $q === '' ? 'on' : '' }}" wire:click="rayon('')"><i class="fa-solid fa-building-columns"></i><span>Tous les rayons</span><b>{{ $all->count() }}</b></button>
      @foreach (\App\Models\Resource::CATEGORIES as $k => $c)
        <button type="button" class="ray {{ $rayon === $k && $q === '' ? 'on' : '' }}" wire:click="rayon('{{ $k }}')"><i class="fa-solid {{ $c[2] }}"></i><span>{{ $c[0] }}</span><b>{{ $counts[$k] ?? 0 }}</b></button>
      @endforeach
      <button type="button" class="ray {{ $rayon === 'mes' && $q === '' ? 'on' : '' }}" wire:click="rayon('mes')"><i class="fa-solid fa-bookmark"></i><span>Mon étagère</span><b>{{ count(array_intersect($got, $all->pluck('id')->all())) }}</b></button>
    </nav>

    <div class="bookcase" wire:loading.class="wire-loading" wire:target="q,tri,rayon,choisirFormation">
      @if ($view['mode'] === 'shelves')
        @foreach ($shelves as [$plate, $title, $items, $key])
          @continue($items->isEmpty())
          <section class="shelf">
            <header class="shelf-h"><span class="shelf-plate">{!! $plate !!}</span><h2>{{ $title }}</h2><span class="shelf-n">{{ $n($items->count()) }}</span>
              @if ($key)<button type="button" class="shelf-all" wire:click="rayon('{{ $key }}')">Tout le rayon<i class="fa-solid fa-chevron-right"></i></button>@endif</header>
            <div class="shelf-books">@foreach ($items as $r)@include('livewire.etudiant._book-slot')@endforeach</div>
            <div class="shelf-plank" aria-hidden="true"></div>
          </section>
        @endforeach
      @else
        <div class="case-h">@isset($view['plate'])<span class="shelf-plate">{!! $view['plate'] !!}</span>@endisset<h2>{{ $view['title'] }}</h2><span class="shelf-n">{{ $n($view['items']->count()) }}</span></div>
        @if ($view['items']->isNotEmpty())
          <div class="shelf-grid">@foreach ($view['items'] as $r)@include('livewire.etudiant._book-slot')@endforeach</div>
        @else
          <div class="case-empty"><i class="fa-solid fa-book-open"></i>
            <h3>{{ $rayon === 'mes' ? 'Votre étagère est vide' : ($q !== '' ? 'Aucun ouvrage trouvé' : 'Rayon vide pour le moment') }}</h3>
            <p>{{ $rayon === 'mes' ? 'Les ouvrages que vous téléchargez se rangent ici.' : ($q !== '' ? 'Essayez un autre mot : « Excel », « polices », « mockup »…' : 'Vos formateurs le complètent au fil des séances.') }}</p>
            @if ($rayon === 'mes')<button class="btn btn-ghost btn-sm" wire:click="rayon('')">Parcourir les rayons</button>@endif
          </div>
        @endif
      @endif
    </div>
  </div>

  @if ($fiche)
    <x-modal :title="$fiche->categoryLabel()" :icon="$fiche->icon()" size="modal-lg">
      <div class="bk-fiche">
        <div class="bf-cover"><x-book :r="$fiche" size="lg" :got="in_array($fiche->id, $got)" /></div>
        <div class="bf-info">
          <h3>{{ $fiche->title }}</h3>
          <p class="bf-desc">{{ $fiche->description }}</p>
          <div class="lib-dl">
            <div class="lib-dl-row"><span>Formation</span><b>@if ($fiche->formation)<x-fchip :code="$fiche->formation->code" /> {{ $fiche->formation->short_name }}@else Toutes les formations @endif</b></div>
            <div class="lib-dl-row"><span>Format</span><b>{{ $fiche->format }}</b></div>
            @if ($fiche->size)<div class="lib-dl-row"><span>Taille</span><b>{{ $fiche->size }}</b></div>@endif
            <div class="lib-dl-row"><span>Licence</span><b>{{ $fiche->licence }}</b></div>
            @if ($fiche->compatible)<div class="lib-dl-row"><span>Compatible</span><b>{{ $fiche->compatible }}</b></div>@endif
            <div class="lib-dl-row"><span>En rayon depuis</span><b>{{ $fiche->created_at->translatedFormat('j F Y') }}</b></div>
            <div class="lib-dl-row"><span>Téléchargements</span><b>{{ number_format($fiche->downloads_count, 0, ',', ' ') }}</b></div>
          </div>
          @if ($fiche->isExternal())<div class="alert-strip as-info mt-3"><i class="fa-solid fa-shield-halved"></i><span>Téléchargez toujours depuis le site officiel de l'éditeur.</span></div>@endif
          @if ($fiche->licence === 'Réservé EVC')<p class="small muted mt-3 mb-0"><i class="fa-solid fa-lock me-1"></i>Ouvrage réservé aux étudiants de l'EVC : merci de ne pas le partager en dehors de l'école.</p>@endif
          <div class="bf-act">@include('livewire.etudiant._dl-button', ['r' => $fiche, 'cls' => 'btn-lg'])@if (in_array($fiche->id, $got))<span class="small muted"><i class="fa-solid fa-bookmark me-1"></i>Dans mon étagère</span>@endif</div>
        </div>
      </div>
    </x-modal>
  @endif
</div>
