<div class="stack">
  <a href="{{ $back }}" wire:navigate class="btn btn-quiet align-self-start"><i class="fa-solid fa-arrow-left"></i>Retour à la bibliothèque</a>
  <form wire:submit="enregistrer" class="stack">
    <x-panel title="Présentation de la ressource" sub="Préparez les informations qui seront visibles dans la bibliothèque des étudiants.">
      <div class="row g-3">
        <div class="col-12"><label for="resource-title" class="form-label">Titre *</label><input id="resource-title" class="form-control" wire:model="r.title" maxlength="150" required>@error('r.title')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="resource-category" class="form-label">Rayon *</label><select id="resource-category" class="form-select" wire:model="r.category">@foreach (\App\Models\Resource::CATEGORIES as $key => $category)<option value="{{ $key }}">{{ $category[0] }}</option>@endforeach</select>@error('r.category')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label for="resource-formation" class="form-label">Formation *</label><select id="resource-formation" class="form-select" wire:model="r.formation_id">@unless ($trainer)<option value="">Toutes les formations</option>@endunless @foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->code }} — {{ $f->name }}</option>@endforeach</select>@error('r.formation_id')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label for="resource-description" class="form-label">Description *</label><textarea id="resource-description" class="form-control" wire:model="r.description" rows="4" maxlength="300" required placeholder="Présentez l’utilité de cette ressource et les conseils d’utilisation."></textarea><small class="muted">300 caractères maximum.</small>@error('r.description')<div class="small text-danger">{{ $message }}</div>@enderror</div>
      </div>
    </x-panel>
    <x-panel title="Fichier ou lien" sub="Les fichiers hébergés restent privés et sont téléchargés depuis un compte habilité.">
      <div class="row g-3">
        <div class="col-12 d-flex flex-wrap gap-3">
          <label class="form-check"><input class="form-check-input" type="radio" wire:model.live="r.source" value="fichier"> Fichier hébergé par l’EVC</label>
          <label class="form-check"><input class="form-check-input" type="radio" wire:model.live="r.source" value="lien"> Lien vers le site officiel</label>
        </div>
        @if (($r['source'] ?? 'fichier') === 'fichier')
          <div class="col-12" x-data="{ uploading: false, progress: 0, error: false }" x-on:livewire-upload-start="uploading = true; progress = 0; error = false" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false; error = true" x-on:livewire-upload-progress="progress = $event.detail.progress">
            <label for="resource-file" class="form-label">{{ $existing?->path ? 'Remplacer le fichier (facultatif)' : 'Fichier à télécharger *' }}</label>
            <input id="resource-file" type="file" wire:model="fichier" class="form-control @error('fichier') is-invalid @enderror">
            @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <p class="small muted mt-2 mb-0">Limite de la bibliothèque : 50 Mo. Limite actuelle du serveur : {{ number_format($uploadLimit / 1048576, 0, ',', ' ') }} Mo.</p>
            @if ($existing?->path)<p class="small muted mt-2">Un fichier est déjà associé à cette ressource. Laissez ce champ vide pour le conserver.</p>@endif
            <div x-show="uploading" x-cloak class="mt-2"><progress max="100" :value="progress" class="w-100"></progress><span class="small" x-text="'Envoi du fichier : ' + progress + ' %'"></span></div>
            <p x-show="error" x-cloak role="alert" class="small text-danger mt-2">L’envoi a échoué. Vérifiez la taille du fichier et votre connexion, puis réessayez.</p>
            @if ($fichier)<p class="small mt-2 mb-0"><i class="fa-solid fa-paperclip me-1"></i>{{ $fichier->getClientOriginalName() }} — prêt à enregistrer.</p>@endif
          </div>
        @else
          <div class="col-12"><label for="resource-url" class="form-label">Adresse du site officiel *</label><input id="resource-url" type="url" class="form-control" wire:model="r.url" placeholder="https://" required>@error('r.url')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        @endif
        <div class="col-md-4"><label for="resource-format" class="form-label">Format</label><input id="resource-format" class="form-control" wire:model="r.format" maxlength="60" placeholder="PDF, ZIP, PSD, vidéo…">@error('r.format')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label for="resource-size" class="form-label">Taille</label><input id="resource-size" class="form-control" wire:model="r.size" maxlength="30" placeholder="Renseignée lors de l’envoi">@error('r.size')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label for="resource-licence" class="form-label">Licence *</label><select id="resource-licence" class="form-select" wire:model="r.licence">@foreach (\App\Models\Resource::LICENCES as $licence)<option>{{ $licence }}</option>@endforeach</select>@error('r.licence')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label for="resource-compatible" class="form-label">Compatibilité et outils nécessaires</label><input id="resource-compatible" class="form-control" wire:model="r.compatible" maxlength="120" placeholder="Windows, macOS, mobile, Photoshop, Illustrator…">@error('r.compatible')<div class="small text-danger">{{ $message }}</div>@enderror</div>
      </div>
    </x-panel>
    <x-panel title="Publication et visibilité">
      <div class="stack gap-3">
        <label class="form-check"><input class="form-check-input" type="checkbox" wire:model="r.essential"> Indispensable : mettre en avant dans « Pour bien démarrer »</label>
        <label class="form-check"><input class="form-check-input" type="checkbox" wire:model="r.published"> Publier maintenant — sinon enregistrer en brouillon</label>
        @unless ($editId)<label class="form-check"><input class="form-check-input" type="checkbox" wire:model="notifier"> Prévenir les étudiants concernés par une notification</label>@endunless
      </div>
    </x-panel>
    <div class="d-flex gap-2 justify-content-end mb-4"><a href="{{ $back }}" wire:navigate class="btn btn-ghost">Annuler</a><button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="enregistrer,fichier"><i class="fa-solid fa-floppy-disk"></i><span wire:loading.remove wire:target="enregistrer">Enregistrer la ressource</span><span wire:loading wire:target="enregistrer">Enregistrement…</span></button></div>
  </form>
</div>
