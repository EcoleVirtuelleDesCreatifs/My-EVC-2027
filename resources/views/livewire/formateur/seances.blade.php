<div>
  <div class="seg seg-soft mb-3"><button type="button" class="{{ $vue === 'avenir' ? 'on' : '' }}" wire:click="$set('vue','avenir')">À venir</button><button type="button" class="{{ $vue === 'passees' ? 'on' : '' }}" wire:click="$set('vue','passees')">Passées</button></div>
  <x-panel :p0="true">
    @forelse ($seances as $x)
      <div class="row-item c-{{ $x->formation->code }}">
        <div class="dbox"><span>{{ $x->starts_at->translatedFormat('M') }}</span><b>{{ $x->starts_at->day }}</b></div>
        <div class="ri-main"><div class="ri-t">{{ $x->title }} @if ($x->cancelled)<x-pill tone="bad">Annulée</x-pill>@endif</div><div class="ri-s"><x-fchip :code="$x->formation->code" /> {{ $x->module }} · {{ jour($x->starts_at) }} · {{ $x->hours() }} · {{ $x->modeLabel() }}</div></div>
        @if ($x->starts_at->subMinutes(30)->isPast() && ! $x->cancelled)
          <a class="btn btn-sm {{ $x->presences_count ? 'btn-ghost' : 'btn-primary' }}" href="{{ route('formateur.appel', $x) }}" wire:navigate><i class="fa-solid fa-list-check"></i>{{ $x->presences_count ? 'Appel fait ('.$x->presences_count.')' : 'Faire l\'appel' }}</a>
        @elseif ($x->meet_url)
          <a class="btn btn-ghost btn-sm" href="{{ $x->meet_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i>Lien Meet</a>
        @endif
      </div>
    @empty
      <div class="p-4"><x-empty title="Aucune séance" /></div>
    @endforelse
  </x-panel>
</div>
