<div>
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <x-fchip :code="$x->formation->code" /><span class="small muted">{{ ucfirst($x->starts_at->translatedFormat('l j F')) }} · {{ $x->hours() }} · {{ $x->modeLabel() }}</span>
    <span class="ms-auto d-flex gap-2">
      <x-pill tone="ok">{{ $counts['present'] ?? 0 }} présents</x-pill><x-pill tone="warn">{{ $counts['retard'] ?? 0 }} retards</x-pill><x-pill tone="bad">{{ $counts['absent'] ?? 0 }} absents</x-pill>
    </span>
  </div>
  <form wire:submit="enregistrer">
    <x-panel :p0="true">
      <x-slot:tools><button type="button" class="btn btn-ghost btn-sm" wire:click="tousPresents"><i class="fa-solid fa-check-double"></i>Tous présents</button></x-slot:tools>
      @forelse ($students as $s)
        <div class="row-item">
          <span class="ini">{{ $s->user->initials() }}</span>
          <div class="ri-main"><div class="ri-t">{{ $s->user->name }}</div><div class="ri-s mono">{{ $s->matricule }}</div></div>
          <div class="seg seg-soft" role="radiogroup" aria-label="Présence de {{ $s->user->name }}">
            @foreach (\App\Models\Presence::STATUSES as $k => [$l])
              <button type="button" class="{{ ($statuts[$s->id] ?? '') === $k ? 'on' : '' }}" wire:click="$set('statuts.{{ $s->id }}', '{{ $k }}')">{{ $l }}</button>
            @endforeach
          </div>
        </div>
      @empty
        <div class="p-4"><x-empty title="Aucun étudiant attendu" /></div>
      @endforelse
    </x-panel>
    <div class="mt-3 d-flex gap-2"><button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-floppy-disk"></i>Enregistrer l'appel</button>
      @if ($x->meet_url)<a class="btn btn-ghost btn-lg" href="{{ $x->meet_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i>Ouvrir Google Meet</a>@endif</div>
  </form>
</div>
