<div class="profile-layout">
  <x-panel title="Mes informations">
    <form wire:submit="enregistrer" class="d-flex flex-column gap-4">
      <div class="profile-identity">
        <span class="avatar lg profile-avatar">
          @if ($photo)<img src="{{ $photo->temporaryUrl() }}" alt="Aperçu de votre nouvelle photo" width="96" height="96">
          @elseif ($u->avatarUrl())<img src="{{ $u->avatarUrl() }}" alt="Photo de {{ $u->name }}" width="96" height="96">
          @else{{ $u->initials() }}@endif
        </span>
        <div class="profile-details">
          <h2 class="profile-name">{{ $u->name }}</h2>
          <p class="small muted mb-2 profile-email">{{ $u->email }}</p>
          <span class="pill p-info">{{ $u->roleLabel() }}</span>
          <div class="mt-3">
            <input type="file" id="profile-photo" class="visually-hidden" wire:model="photo" accept="image/*" aria-describedby="profile-photo-help">
            <label for="profile-photo" class="btn btn-ghost btn-sm mb-0"><i class="fa-solid fa-camera" aria-hidden="true"></i>Changer la photo</label>
            <p id="profile-photo-help" class="small muted mt-2 mb-0">Image · 4 Mo maximum · cadrage automatique</p>
            <span class="small muted" wire:loading wire:target="photo" role="status">Chargement de la photo…</span>
            @error('photo')<div class="small text-danger" role="alert">{{ $message }}</div>@enderror
          </div>
        </div>
      </div>
      @if ($u->student)
        <div class="profile-section">
          <h3 class="profile-section-title">Mon parcours à l’EVC</h3>
          <dl class="profile-facts">
            <div><dt>Matricule</dt><dd class="mono">{{ $u->student->matricule }}</dd></div>
            <div><dt>Formation(s)</dt><dd>{{ $u->student->formations->pluck('short_name')->implode(', ') }}</dd></div>
            <div><dt>Session</dt><dd>{{ $u->student->promotion?->name ?? '—' }}</dd></div>
          </dl>
          <p class="small muted mt-2 mb-0">Pour modifier vos informations d’inscription, contactez l’administration.</p>
        </div>
      @endif
      <div class="profile-section">
        <h3 class="profile-section-title">Mes coordonnées</h3>
        <label class="form-label" for="phone">Téléphone / WhatsApp</label>
        <input class="form-control @error('phone') is-invalid @enderror" type="tel" id="phone" wire:model="phone" autocomplete="tel" placeholder="Ex. : +225 07 00 00 00 00">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div class="profile-actions"><button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="enregistrer,photo"><i class="fa-solid fa-check" aria-hidden="true"></i>Enregistrer les modifications</button><span class="small muted" wire:loading wire:target="enregistrer" role="status">Enregistrement…</span></div>
    </form>
  </x-panel>
  <x-panel title="Sécurité du compte">
    <p class="small muted mb-4">Choisissez un mot de passe unique d’au moins 8 caractères, contenant des lettres et des chiffres.</p>
    <form wire:submit="changerMotDePasse" class="d-flex flex-column gap-3">
      <div x-data="{ visible: false }">
        <div class="d-flex justify-content-between align-items-baseline gap-2"><label class="form-label" for="actuel">Mot de passe actuel</label><button class="btn btn-link link-u small p-0" type="button" x-on:click="visible = !visible" x-bind:aria-pressed="visible" aria-controls="actuel" x-text="visible ? 'Masquer' : 'Afficher'" aria-label="Afficher ou masquer le mot de passe actuel">Afficher</button></div>
        <input class="form-control @error('actuel') is-invalid @enderror" type="password" x-bind:type="visible ? 'text' : 'password'" id="actuel" wire:model="actuel" autocomplete="current-password">
        @error('actuel')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div x-data="{ visible: false }">
        <div class="d-flex justify-content-between align-items-baseline gap-2"><label class="form-label" for="nouveau">Nouveau mot de passe</label><button class="btn btn-link link-u small p-0" type="button" x-on:click="visible = !visible" x-bind:aria-pressed="visible" aria-controls="nouveau" x-text="visible ? 'Masquer' : 'Afficher'" aria-label="Afficher ou masquer le nouveau mot de passe">Afficher</button></div>
        <input class="form-control @error('nouveau') is-invalid @enderror" type="password" x-bind:type="visible ? 'text' : 'password'" id="nouveau" wire:model="nouveau" autocomplete="new-password">
        @error('nouveau')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>
      <div x-data="{ visible: false }">
        <div class="d-flex justify-content-between align-items-baseline gap-2"><label class="form-label" for="conf">Confirmer le mot de passe</label><button class="btn btn-link link-u small p-0" type="button" x-on:click="visible = !visible" x-bind:aria-pressed="visible" aria-controls="conf" x-text="visible ? 'Masquer' : 'Afficher'" aria-label="Afficher ou masquer la confirmation du mot de passe">Afficher</button></div>
        <input class="form-control" type="password" x-bind:type="visible ? 'text' : 'password'" id="conf" wire:model="nouveau_confirmation" autocomplete="new-password">
      </div>
      <div class="profile-actions mt-2"><button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="changerMotDePasse"><i class="fa-solid fa-lock" aria-hidden="true"></i>Modifier le mot de passe</button></div>
    </form>
  </x-panel>
</div>
