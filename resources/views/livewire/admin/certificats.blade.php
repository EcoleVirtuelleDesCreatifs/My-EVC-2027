<div>
  <div class="filters-row mb-3">
    <ul class="nav nav-tabs border-0 flex-grow-1">
      <li class="nav-item"><button class="nav-link {{ $onglet === 'eligibles' ? 'active' : '' }}" wire:click="$set('onglet','eligibles')">Suivi de l'éligibilité</button></li>
      <li class="nav-item"><button class="nav-link {{ $onglet === 'delivres' ? 'active' : '' }}" wire:click="$set('onglet','delivres')">Certificats délivrés <span class="badge rounded-pill text-bg-secondary">{{ $issued->count() }}</span></button></li>
    </ul>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
  </div>
  @if ($onglet === 'eligibles')
    <div class="panel tbl" wire:loading.class="wire-loading">
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Étudiant</th><th>Formation</th>@foreach (['Soldé', 'Projets', 'Assiduité', 'Séances'] as $h)<th class="text-center">{{ $h }}</th>@endforeach<th></th></tr></thead><tbody>
        @forelse ($eligibles as $row)
          <tr><td><a class="link-plain" href="{{ route('admin.etudiant', $row['s']) }}" wire:navigate>{{ $row['s']->user->name }}</a><div class="small muted mono">{{ $row['s']->matricule }}</div></td><td><x-fchip :code="$row['f']->code" /></td>
            @foreach ($row['checks'] as $label => $ok)<td class="text-center" title="{{ $label }}"><i class="fa-solid {{ $ok ? 'fa-circle-check text-success' : 'fa-circle-xmark muted' }}"></i></td>@endforeach
            <td class="table-actions">@if ($row['ok'] === count($row['checks']))<button class="btn btn-primary btn-sm" wire:click="ouvrir({{ $row['s']->id }}, {{ $row['f']->id }})"><i class="fa-solid fa-award"></i>Délivrer</button>
              @else<button class="btn btn-ghost btn-sm" wire:click="ouvrir({{ $row['s']->id }}, {{ $row['f']->id }})" title="Délivrer malgré les conditions manquantes (décision du jury)">Décision du jury</button>@endif</td></tr>
        @empty
          <tr><td colspan="7"><x-empty title="Aucun étudiant en attente" /></td></tr>
        @endforelse
      </tbody></table></div>
    </div>
  @else
    <x-panel :p0="true">
      @forelse ($issued as $c)
        <div class="row-item"><span class="icon-sq"><i class="fa-solid fa-award"></i></span><div class="ri-main"><div class="ri-t">{{ $c->student->user->name }} · {{ $c->formation->short_name }}</div><div class="ri-s mono">{{ $c->code }} · {{ $c->issued_on->translatedFormat('j M Y') }} · {{ $c->appreciation }}</div></div>
          <a class="btn btn-ghost btn-sm" href="{{ route('pdf.certificat', $c) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>PDF</a>
          <a class="btn btn-quiet btn-sm btn-icon" href="{{ route('certificat.verifier', ['reference' => $c->code]) }}" target="_blank" aria-label="Vérifier sur le site"><i class="fa-solid fa-shield-halved"></i></a></div>
      @empty
        <div class="p-4"><x-empty title="Aucun certificat délivré" /></div>
      @endforelse
    </x-panel>
  @endif
  @if ($delivrer)
    <x-modal title="Délivrer le certificat" icon="fa-award">
      <p class="small">Le certificat PDF est généré avec une référence vérifiable sur le site, et l'étudiant est prévenu par e-mail.</p>
      <label class="form-label">Appréciation du jury</label>
      <select class="form-select" wire:model="appreciation">@foreach (['Excellent', 'Très bien', 'Bien', 'Acquis'] as $a)<option>{{ $a }}</option>@endforeach</select>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="confirmer"><i class="fa-solid fa-award"></i>Délivrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
