@if ($s->formations->count() > 1)
  <div class="fsel-row"><span class="d-none d-sm-inline">Afficher</span>
    <div class="fsel seg seg-soft d-flex" role="group" aria-label="Formation">
      <button type="button" class="{{ $f === '' ? 'on' : '' }}" wire:click="choisirFormation('')">Toutes</button>
      @foreach ($s->formations as $fo)
        <button type="button" class="{{ $f === $fo->code ? 'on' : '' }}" wire:click="choisirFormation('{{ $fo->code }}')"><span class="sw" style="background:var(--{{ strtolower($fo->code) }})"></span>{{ $fo->code }}</button>
      @endforeach
    </div>
  </div>
@endif
