<div>
  <div class="seg seg-soft mb-3">
    @foreach (['soumis' => 'À corriger', 'reprendre' => 'Renvoyés', 'valide' => 'Validés'] as $k => $l)
      <button type="button" class="{{ $etat === $k ? 'on' : '' }}" wire:click="$set('etat','{{ $k }}')">{{ $l }} <span class="cnt">{{ $counts[$k] }}</span></button>
    @endforeach
  </div>
  <x-panel :p0="true">
    @forelse ($items as $a)
      <div class="row-item">
        <x-fchip :code="$a->project->formation->code" />
        <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div><div class="ri-s">{{ $a->student->user->name }} · {{ $a->status === 'soumis' ? 'rendu '.$a->submitted_at?->diffForHumans() : ($a->status === 'valide' ? $a->appreciation.' · '.$a->validated_at?->translatedFormat('j M') : 'à rendre le '.$a->due_on->translatedFormat('j M')) }}</div></div>
        @if ($a->status === 'soumis')<button class="btn btn-primary btn-sm" wire:click="ouvrirCorrection({{ $a->id }})"><i class="fa-solid fa-pen-to-square"></i>Corriger</button>
        @else<x-pill :tone="$a->tone()">{{ $a->label(true) }}</x-pill>@endif
      </div>
    @empty
      <div class="p-4"><x-empty title="Rien ici" text="{{ $etat === 'soumis' ? 'Aucun projet en attente de correction.' : 'Aucun projet pour ce filtre.' }}" /></div>
    @endforelse
  </x-panel>
  @include('livewire.partials.correction')
</div>
