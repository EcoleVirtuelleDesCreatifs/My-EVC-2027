<div class="u-grid u-1-1 align-items-start">
  <x-panel title="Mes informations">
    <form wire:submit="enregistrer" class="d-flex flex-column gap-3">
      <div class="d-flex align-items-center gap-3">
        <span class="ini" style="width:64px;height:64px;font-size:22px">@if ($photo)<img src="{{ $photo->temporaryUrl() }}" alt="">@elseif ($u->avatarUrl())<img src="{{ $u->avatarUrl() }}" alt="">@else{{ $u->initials() }}@endif</span>
        <div><div class="fw-sb">{{ $u->name }}</div><div class="small muted">{{ $u->email }}</div>
          <label class="btn btn-ghost btn-sm mt-2 mb-0"><i class="fa-solid fa-camera"></i>Changer la photo<input type="file" class="d-none" wire:model="photo" accept="image/*"></label>
          @error('photo')<div class="small text-danger">{{ $message }}</div>@enderror</div>
      </div>
      @if ($u->student)
        <div class="lib-dl">
          <div class="lib-dl-row"><span>Matricule</span><b class="mono">{{ $u->student->matricule }}</b></div>
          <div class="lib-dl-row"><span>Formation(s)</span><b>{{ $u->student->formations->pluck('short_name')->implode(', ') }}</b></div>
          <div class="lib-dl-row"><span>Session</span><b>{{ $u->student->promotion?->name ?? '—' }}</b></div>
        </div>
      @endif
      <div><label class="form-label" for="phone">Téléphone / WhatsApp</label><input class="form-control" id="phone" wire:model="phone">@error('phone')<div class="small text-danger">{{ $message }}</div>@enderror</div>
      <div><button class="btn btn-primary" type="submit">Enregistrer</button></div>
    </form>
  </x-panel>
  <x-panel title="Mot de passe">
    <form wire:submit="changerMotDePasse" class="d-flex flex-column gap-3">
      <div><label class="form-label" for="actuel">Mot de passe actuel</label><input class="form-control @error('actuel') is-invalid @enderror" type="password" id="actuel" wire:model="actuel" autocomplete="current-password">@error('actuel')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div><label class="form-label" for="nouveau">Nouveau mot de passe</label><input class="form-control @error('nouveau') is-invalid @enderror" type="password" id="nouveau" wire:model="nouveau" autocomplete="new-password">@error('nouveau')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      <div><label class="form-label" for="conf">Confirmer</label><input class="form-control" type="password" id="conf" wire:model="nouveau_confirmation" autocomplete="new-password"></div>
      <div><button class="btn btn-primary" type="submit">Modifier le mot de passe</button></div>
    </form>
  </x-panel>
</div>
