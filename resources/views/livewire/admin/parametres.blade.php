<div style="max-width:760px">
  <x-panel title="Coordonnées de l'école" sub="Utilisées sur le site, dans les e-mails et sur les documents PDF">
    <form wire:submit="enregistrer" class="d-flex flex-column gap-3">
      @foreach ($labels as $k => [$label])
        <div><label class="form-label" for="s-{{ $k }}">{{ $label }}</label><input class="form-control @error('values.'.$k) is-invalid @enderror" id="s-{{ $k }}" wire:model="values.{{ $k }}">@error('values.'.$k)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      @endforeach
      <div><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk"></i>Enregistrer</button></div>
    </form>
  </x-panel>
</div>
