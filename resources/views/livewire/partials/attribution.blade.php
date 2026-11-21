@if ($attribution)
  @php($projects = \App\Models\Project::with('formation')->where('published', true)->orderBy('formation_id')->get())
  @php($cands = $this->candidatsAttribution())
  <x-modal title="Attribuer un projet" icon="fa-paper-plane" size="modal-lg" close="$set('attribution', false)">
    <div class="row g-3">
      <div class="col-md-8"><label class="form-label">Projet</label>
        <select class="form-select @error('aProjet') is-invalid @enderror" wire:model.live="aProjet"><option value="">Choisir un projet…</option>@foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->formation->code }} — {{ $p->title }}</option>@endforeach</select>
        @error('aProjet')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="col-md-4"><label class="form-label">À rendre le</label><input class="form-control @error('aEcheance') is-invalid @enderror" type="date" wire:model="aEcheance">@error('aEcheance')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div class="col-12">
        <div class="d-flex align-items-center mb-2"><label class="form-label mb-0">Étudiants ({{ count($aEtudiants) }} sélectionné{{ count($aEtudiants) > 1 ? 's' : '' }})</label>
          <button type="button" class="btn btn-quiet btn-sm ms-auto" wire:click="$set('aEtudiants', {{ json_encode($cands->pluck('id')->values()) }})">Tout sélectionner</button>
          <button type="button" class="btn btn-quiet btn-sm" wire:click="$set('aEtudiants', [])">Aucun</button></div>
        <div class="border rounded-3 p-2" style="max-height:260px;overflow:auto">
          @forelse ($cands as $c)
            <label class="d-flex align-items-center gap-2 p-1 small"><input type="checkbox" class="form-check-input mt-0" value="{{ $c->id }}" wire:model.live="aEtudiants">{{ $c->user->name }} <span class="muted mono">{{ $c->matricule }}</span></label>
          @empty
            <p class="small muted mb-0 p-2">Choisissez d'abord un projet.</p>
          @endforelse
        </div>
        @error('aEtudiants')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="notif" wire:model="aNotifier"><label class="form-check-label" for="notif">Prévenir les étudiants (notification et e-mail)</label></div></div>
    </div>
    <x-slot:footer><button class="btn btn-ghost" wire:click="$set('attribution', false)">Annuler</button><button class="btn btn-primary" wire:click="confirmerAttribution" wire:loading.attr="disabled"><i class="fa-solid fa-paper-plane"></i>Attribuer</button></x-slot:footer>
  </x-modal>
@endif
