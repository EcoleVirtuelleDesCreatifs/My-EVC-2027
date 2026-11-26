<div>
  @include('livewire.etudiant._classes')
  @include('livewire.etudiant._fsel')
  <div class="seg seg-soft mb-3" role="tablist">
    @foreach (['afaire' => 'À faire', 'rendus' => 'Rendus', 'valides' => 'Validés', 'tous' => 'Tous'] as $k => $l)
      <button type="button" class="{{ $etat === $k ? 'on' : '' }}" wire:click="$set('etat','{{ $k }}')">{{ $l }} <span class="cnt">{{ $counts[$k] }}</span></button>
    @endforeach
  </div>
  @forelse ($items as $a)
    <a href="{{ route('etudiant.projet', $a) }}" wire:navigate class="panel d-block text-decoration-none mb-3 c-{{ $a->project->formation->code }}">
      <div class="panel-b d-flex align-items-center gap-3 flex-wrap">
        <span class="icon-sq"><i class="fa-solid fa-pen-nib"></i></span>
        <div class="flex-grow-1" style="min-width:200px">
          <div class="d-flex align-items-center gap-2 mb-1"><x-fchip :code="$a->project->formation->code" /><span class="small muted">{{ $a->project->type }}</span></div>
          <div class="fw-sb" style="color:var(--fg)">{{ $a->project->title }}</div>
          <div class="small muted">{{ $a->status === 'valide' ? 'Validé le '.$a->validated_at?->translatedFormat('j F').($a->appreciation ? ' · '.$a->appreciation : '') : 'À rendre le '.$a->due_on->translatedFormat('j F').' · '.mb_strtolower($a->dueText()) }}</div>
        </div>
        <x-pill :tone="$a->tone()">{{ $a->isLate() ? 'En retard' : $a->label() }}</x-pill>
        <i class="fa-solid fa-chevron-right muted"></i>
      </div>
    </a>
  @empty
    <x-empty title="Aucun projet ici" text="{{ $etat === 'afaire' ? 'Vous êtes à jour : vos formateurs vous attribueront le prochain projet.' : 'Rien à afficher pour ce filtre.' }}" />
  @endforelse
</div>
