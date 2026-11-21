<div>
  <a class="dispo-link mb-3" href="{{ route('admin.disponibilite') }}" wire:navigate><span class="dl-i"><i class="fa-solid fa-user-clock"></i></span><span class="lr-m"><b>Disponibilité des étudiants</b><span class="small muted d-block">Qui est libre, qui a reçu un projet, qui travaille dessus</span></span><i class="fa-solid fa-chevron-right muted ms-auto"></i></a>
  <div class="filters-row mb-3">
    <ul class="nav nav-tabs flex-grow-1 border-0">
      <li class="nav-item"><button class="nav-link {{ $onglet === 'valider' ? 'active' : '' }}" wire:click="$set('onglet','valider')">À valider <span class="badge rounded-pill text-bg-warning">{{ $queue->count() }}</span></button></li>
      <li class="nav-item"><button class="nav-link {{ $onglet === 'catalogue' ? 'active' : '' }}" wire:click="$set('onglet','catalogue')">Catalogue</button></li>
    </ul>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
  </div>

  @if ($onglet === 'valider')
    <x-panel :p0="true">
      @forelse ($queue as $a)
        <div class="row-item"><x-fchip :code="$a->project->formation->code" />
          <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div><div class="ri-s">{{ $a->student->user->name }} · rendu {{ $a->submitted_at?->diffForHumans() }}</div></div>
          <button class="btn btn-primary btn-sm" wire:click="ouvrirCorrection({{ $a->id }})"><i class="fa-solid fa-pen-to-square"></i>Corriger</button></div>
      @empty
        <div class="p-4"><x-empty title="Rien à valider" text="Les projets rendus par les étudiants apparaissent ici." /></div>
      @endforelse
    </x-panel>
  @else
    <x-panel :p0="true">
      <x-slot:tools><button class="btn btn-ghost btn-sm" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouveau projet</button></x-slot:tools>
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Projet</th><th>Type</th><th class="text-end">Attribués</th><th class="text-end">Validés</th><th>Statut</th><th></th></tr></thead><tbody>
        @foreach ($catalogue as $p)
          <tr><td><x-fchip :code="$p->formation->code" /> <span class="fw-sb">{{ $p->title }}</span><div class="small muted text-truncate" style="max-width:420px">{{ $p->brief }}</div></td>
            <td class="small">{{ $p->type }}</td><td class="text-end">{{ $p->assignments_count }}</td><td class="text-end">{{ $p->valides_count }}</td>
            <td><x-pill :tone="$p->published ? 'ok' : 'mute'">{{ $p->published ? 'Publié' : 'Brouillon' }}</x-pill></td>
            <td class="table-actions"><button class="btn btn-ghost btn-sm" wire:click="ouvrirAttribution([], {{ $p->id }})">Attribuer</button><button class="btn btn-quiet btn-sm btn-icon" wire:click="modifier({{ $p->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button></td></tr>
        @endforeach
      </tbody></table></div>
    </x-panel>
  @endif

  @if ($form)
    <x-modal :title="$editId ? 'Modifier le projet' : 'Nouveau projet'" icon="fa-palette" size="modal-lg">
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label">Formation</label><select class="form-select" wire:model="p.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label">Type</label><input class="form-control" wire:model="p.type" placeholder="Identité visuelle, Excel…"></div>
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('p.title') is-invalid @enderror" wire:model="p.title">@error('p.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Consignes</label><textarea class="form-control @error('p.brief') is-invalid @enderror" rows="5" wire:model="p.brief"></textarea>@error('p.brief')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="pub" wire:model="p.published"><label class="form-check-label" for="pub">Publié (peut être attribué)</label></div></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
  @include('livewire.partials.attribution')
  @include('livewire.partials.correction')
</div>
