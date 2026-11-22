<div class="stack">
  <div class="u-grid" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
    @foreach ($formations as $x)
      <x-panel :title="$x->name" class="c-{{ $x->code }}">
        <x-slot:tools><x-fchip :code="$x->code" /></x-slot:tools>
        <p class="small muted">{{ $x->pitch }}</p>
        <div class="lib-dl mb-3">
          <div class="lib-dl-row"><span>Prix</span><b>{{ fcfa($x->price) }}</b></div>
          <div class="lib-dl-row"><span>Acompte</span><b>{{ fcfa($x->deposit) }}</b></div>
          <div class="lib-dl-row"><span>Durée</span><b>{{ $x->months }} mois</b></div>
          <div class="lib-dl-row"><span>Solde dû</span><b>{{ $x->balance_after_days }} j après le début</b></div>
          <div class="lib-dl-row"><span>Inscrits en cours</span><b>{{ $x->enrollments_count }}</b></div>
          <div class="lib-dl-row"><span>Modules</span><b>{{ count($x->modules ?? []) }}</b></div>
        </div>
        <div class="d-flex align-items-center gap-2"><x-pill :tone="$x->active ? 'ok' : 'mute'">{{ $x->active ? 'Ouverte aux inscriptions' : 'Fermée' }}</x-pill><button class="btn btn-ghost btn-sm ms-auto" wire:click="modifierFormation({{ $x->id }})"><i class="fa-solid fa-pen"></i>Modifier</button></div>
      </x-panel>
    @endforeach
  </div>

  <x-panel title="Sessions (promotions)" :p0="true">
    <x-slot:tools><button class="btn btn-primary btn-sm" wire:click="nouvelleSession"><i class="fa-solid fa-plus"></i>Nouvelle session</button></x-slot:tools>
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Session</th><th>Début</th><th>Fin</th><th class="text-end">Étudiants</th><th>Statut</th><th></th></tr></thead><tbody>
      @foreach ($promotions as $p)
        <tr><td class="fw-sb">{{ $p->name }}</td><td>{{ $p->starts_on->translatedFormat('j M Y') }}</td><td>{{ $p->ends_on->translatedFormat('j M Y') }}</td><td class="text-end">{{ $p->students_count }}</td>
          <td><x-pill :tone="['ouverte' => 'info', 'en_cours' => 'ok', 'terminee' => 'mute'][$p->status]">{{ \App\Models\Promotion::STATUSES[$p->status] }}</x-pill></td>
          <td class="table-actions"><button class="btn btn-quiet btn-sm btn-icon" wire:click="modifierSession({{ $p->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button><button class="btn btn-quiet btn-sm btn-icon text-danger" wire:click="supprimerSession({{ $p->id }})" wire:confirm="Supprimer définitivement la session « {{ $p->name }} » ? Les sessions utilisées ne peuvent pas être supprimées." wire:loading.attr="disabled" aria-label="Supprimer la session" title="Supprimer la session"><i class="fa-solid fa-trash-can"></i></button></td></tr>
      @endforeach
    </tbody></table></div>
  </x-panel>

  @if ($editF)
    <x-modal title="Modifier la formation" icon="fa-book-open" size="modal-lg">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label">Nom</label><input class="form-control" wire:model="f.name">@error('f.name')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label class="form-label">Nom court</label><input class="form-control" wire:model="f.short_name"></div>
        <div class="col-12"><label class="form-label">Présentation</label><textarea class="form-control" rows="2" wire:model="f.pitch"></textarea></div>
        <div class="col-sm-4"><label class="form-label">Prix (FCFA)</label><input class="form-control" type="number" wire:model="f.price">@error('f.price')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-sm-4"><label class="form-label">Acompte (FCFA)</label><input class="form-control" type="number" wire:model="f.deposit">@error('f.deposit')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-sm-4"><label class="form-label">Durée (mois)</label><input class="form-control" type="number" wire:model="f.months"></div>
        <div class="col-sm-4"><label class="form-label">Solde dû après (jours)</label><input class="form-control" type="number" wire:model="f.balance_after_days"></div>
        <div class="col-sm-8"><label class="form-label">Conditions de paiement</label><input class="form-control" wire:model="f.pay_rule"></div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="act" wire:model="f.active"><label class="form-check-label" for="act">Ouverte aux inscriptions</label></div></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerFormation">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
  @if ($newP)
    <x-modal :title="$editP ? 'Modifier la session' : 'Nouvelle session'" icon="fa-calendar">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Nom</label><input class="form-control" wire:model="p.name">@error('p.name')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label">Début</label><input class="form-control" type="date" wire:model="p.starts_on"></div>
        <div class="col-sm-6"><label class="form-label">Fin</label><input class="form-control" type="date" wire:model="p.ends_on">@error('p.ends_on')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Statut</label><select class="form-select" wire:model="p.status">@foreach (\App\Models\Promotion::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerSession">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
