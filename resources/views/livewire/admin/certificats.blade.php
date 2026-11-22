<div>
  @error('delivrer')<div class="alert-strip as-warn mb-3" role="alert">{{ $message }}</div>@enderror
  @error('registre')<div class="alert-strip as-warn mb-3" role="alert">{{ $message }}</div>@enderror
  <div class="alert-strip as-info mb-3">Les nouveaux certificats sont délivrés et publiés par le registre officiel. MY EVC transmet les dossiers et récupère les PDF publiés sans les recréer.</div>
  <div class="filters-row mb-3">
    <button type="button" class="btn btn-ghost btn-sm" wire:click="synchroniser" wire:loading.attr="disabled" wire:target="synchroniser">Actualiser le registre</button>
    <ul class="nav nav-tabs border-0 flex-grow-1">
      <li class="nav-item"><button class="nav-link {{ $onglet === 'eligibles' ? 'active' : '' }}" wire:click="$set('onglet','eligibles')">Suivi de l'éligibilité</button></li>
      <li class="nav-item"><button class="nav-link {{ $onglet === 'delivres' ? 'active' : '' }}" wire:click="$set('onglet','delivres')">Certificats délivrés <span class="badge rounded-pill text-bg-secondary">{{ $issued->count() }}</span></button></li>
    </ul>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
  </div>
  @if ($onglet === 'eligibles')
    <div class="panel tbl" wire:loading.class="wire-loading">
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Étudiant</th><th>Formation</th><th>Conditions</th><th></th></tr></thead><tbody>
        @forelse ($eligibles as $row)
          <tr wire:key="certificat-{{ $row['s']->id }}-{{ $row['f']->id }}"><td><a class="link-plain" href="{{ route('admin.etudiant', $row['s']) }}" wire:navigate>{{ $row['s']->user->name }}</a><div class="small muted mono">{{ $row['s']->matricule }}</div></td><td><x-fchip :code="$row['f']->code" /></td>
            <td><div class="d-flex flex-wrap gap-1">@foreach ($row['checks'] as $label => $ok)<span class="pill {{ $ok ? 'p-ok' : 'p-mute text-white' }} nodot" title="{{ $label }}"><i class="fa-solid {{ $ok ? 'fa-check' : 'fa-xmark' }} me-1" aria-hidden="true"></i>{{ $label }}</span>@endforeach</div></td>
            <td class="table-actions">@if ($row['request']?->registry_document_id)
              <span class="pill p-info text-white">{{ $row['request']->statusLabel() }}</span><div class="small muted mt-1">{{ $row['request']->reference ?? 'Référence attribuée à la délivrance' }}</div>
              @elseif ($row['ok'] === count($row['checks']))<button class="btn btn-primary btn-sm" type="button" wire:click="ouvrir({{ $row['s']->id }}, {{ $row['f']->id }})" wire:loading.attr="disabled" wire:target="ouvrir,confirmer"><i class="fa-solid fa-award"></i>Transmettre au registre</button>
              @else<button class="btn btn-ghost btn-sm" type="button" wire:click="ouvrir({{ $row['s']->id }}, {{ $row['f']->id }})" wire:loading.attr="disabled" wire:target="ouvrir,confirmer" title="Transmettre le dossier et ses conditions manquantes au contrôle du registre">Décision du jury</button>@endif</td></tr>
        @empty
          <tr><td colspan="4"><x-empty title="Aucun étudiant en attente" /></td></tr>
        @endforelse
      </tbody></table></div>
    </div>
  @else
    <x-panel :p0="true">
      @forelse ($issued as $c)
        <div class="row-item"><span class="icon-sq"><i class="fa-solid fa-award"></i></span><div class="ri-main"><div class="ri-t">{{ $c->student->user->name }} · {{ $c->formation->short_name }}</div><div class="ri-s mono">{{ $c->code }} · {{ $c->issued_on->translatedFormat('j M Y') }} · {{ $c->appreciation }}</div></div>
          @if (! $c->registryRequest || $c->registryRequest->status === 'publie')
            <a class="btn btn-ghost btn-sm" href="{{ route('pdf.certificat', $c) }}" target="_blank" rel="noopener"><i class="fa-solid fa-file-pdf"></i>PDF</a>
          @endif
          <span class="small muted">{{ $c->registryRequest?->statusLabel() ?? 'Document local antérieur au raccordement' }}</span>
          <a class="btn btn-quiet btn-sm btn-icon" href="{{ route('certificat.verifier', ['reference' => $c->code]) }}" target="_blank" rel="noopener" aria-label="Vérifier sur le site"><i class="fa-solid fa-shield-halved"></i></a></div>
      @empty
        <div class="p-4"><x-empty title="Aucun certificat délivré" /></div>
      @endforelse
    </x-panel>
  @endif
  @if ($delivrer)
    <div wire:key="jury-{{ $delivrer }}">
    <x-modal title="Transmettre au registre officiel" icon="fa-award">
      <p class="small">Le dossier et les conditions remplies ou manquantes seront transmis au registre. Le contrôle pédagogique, l’approbation et la publication y restent obligatoires. L’étudiant recevra son e-mail uniquement après publication du PDF officiel.</p>
      <label class="form-label">Appréciation du jury</label>
      <select class="form-select" wire:model="appreciation">@foreach (['Excellent', 'Très bien', 'Bien', 'Acquis'] as $a)<option>{{ $a }}</option>@endforeach</select>
      @error('appreciation')<div class="small text-danger" role="alert">{{ $message }}</div>@enderror
      <x-slot:footer><button type="button" class="btn btn-ghost" wire:click="closeModal" wire:loading.attr="disabled" wire:target="confirmer">Annuler</button><button type="button" class="btn btn-primary" wire:click="confirmer" wire:loading.attr="disabled" wire:target="confirmer"><i class="fa-solid fa-award"></i><span wire:loading.remove wire:target="confirmer">Transmettre le dossier</span><span wire:loading wire:target="confirmer">Transmission en cours…</span></button></x-slot:footer>
    </x-modal>
    </div>
  @endif
</div>
