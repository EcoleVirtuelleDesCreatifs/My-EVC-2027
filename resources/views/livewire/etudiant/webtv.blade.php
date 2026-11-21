<div>
  @include('livewire.etudiant._fsel')
  <div class="stat-grid mb-4">
    <x-kpi label="Vidéos disponibles" :value="$all->count()" icon="fa-tv" tone="lav" />
    <x-kpi label="Vidéos vues" :value="$vues" icon="fa-circle-check" tone="ok" :sub="$all->count() ? round($vues / $all->count() * 100).' % du catalogue' : ''" />
    <x-kpi label="Replays de séances" :value="$all->where('module', 'Séances')->count()" icon="fa-clapperboard" tone="info" />
    <x-kpi label="Tutoriels" :value="$all->where('module', '!=', 'Séances')->count()" icon="fa-graduation-cap" tone="cor" />
  </div>
  @if ($resume)
    <a class="panel d-flex align-items-center gap-3 p-3 mb-4 text-decoration-none" href="{{ route('etudiant.video', $resume) }}" wire:navigate>
      <span class="icon-sq" style="background:var(--accent);color:#fff"><i class="fa-solid fa-play"></i></span>
      <div class="flex-grow-1"><div class="small-caps">Reprendre</div><b style="color:var(--fg)">{{ $resume->title }}</b><div class="mt-1"><x-bar :pct="$prog[$resume->id]->percent" tone="cor" /></div></div>
      <span class="btn btn-primary btn-sm">Continuer</span>
    </a>
  @endif
  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Rechercher une vidéo…" wire:model.live.debounce.300ms="q"></label>
    <div class="seg seg-soft"><button type="button" class="{{ $module === '' ? 'on' : '' }}" wire:click="$set('module','')">Toutes</button>@foreach ($modules as $m)<button type="button" class="{{ $module === $m ? 'on' : '' }}" wire:click="$set('module', @js($m))">{{ $m }}</button>@endforeach</div>
  </div>
  @if ($videos->isNotEmpty())
    <div class="tv-grid" wire:loading.class="wire-loading">@foreach ($videos as $v)@include('livewire.etudiant._tv-card')@endforeach</div>
  @else
    <x-empty title="Aucune vidéo" text="Les replays sont publiés après chaque séance par vos formateurs." />
  @endif
</div>
