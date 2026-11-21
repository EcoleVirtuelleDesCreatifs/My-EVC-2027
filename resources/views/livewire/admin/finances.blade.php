<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Encaissé au total" :value="fcfa($kpi['total'])" icon="fa-wallet" tone="ok" />
    <x-kpi label="Encaissé ce mois" :value="fcfa($kpi['mois'])" icon="fa-calendar-check" tone="lav" :sub="ucfirst(now()->translatedFormat('F Y'))" />
    <x-kpi label="Reste à encaisser" :value="fcfa(max(0, $kpi['du'] - $kpi['total']))" icon="fa-hourglass-half" tone="cor" />
    <x-kpi label="Échéances en retard" :value="$retards->count()" icon="fa-triangle-exclamation" :tone="$retards->count() ? 'warn' : 'ok'" :sub="fcfa($retards->sum(fn ($x) => $x->remaining()))" />
  </div>
  <div class="filters-row mb-3">
    <ul class="nav nav-tabs border-0 flex-grow-1">
      <li class="nav-item"><button class="nav-link {{ $vue === 'versements' ? 'active' : '' }}" wire:click="$set('vue','versements')">Versements</button></li>
      <li class="nav-item"><button class="nav-link {{ $vue === 'retards' ? 'active' : '' }}" wire:click="$set('vue','retards')">Retards et relances <span class="badge rounded-pill text-bg-warning">{{ $retards->count() }}</span></button></li>
    </ul>
    @if ($vue === 'versements')<label class="search-field mb-0" style="min-width:260px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Reçu, étudiant, matricule…" wire:model.live.debounce.300ms="q"></label>@endif
    <button class="btn btn-primary d-sm-none" wire:click="nouveau"><i class="fa-solid fa-plus"></i>Versement</button>
  </div>

  @if ($vue === 'versements')
    <div class="panel tbl" wire:loading.class="wire-loading">
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Date</th><th>Étudiant</th><th>Reçu</th><th>Mode</th><th class="text-end">Montant</th><th></th></tr></thead><tbody>
        @forelse ($payments as $p)
          <tr><td>{{ $p->paid_on->translatedFormat('j M Y') }}</td><td><a class="link-plain" href="{{ route('admin.etudiant', $p->student) }}" wire:navigate>{{ $p->student->user->name }}</a><div class="small muted mono">{{ $p->student->matricule }}</div></td>
            <td class="mono small">{{ $p->receipt_number }}</td><td class="small">{{ $p->method }}@if ($p->transaction_ref)<div class="muted">{{ $p->transaction_ref }}</div>@endif</td>
            <td class="text-end fw-sb">{{ fcfa($p->amount) }}</td><td class="table-actions"><a class="btn btn-quiet btn-sm btn-icon" href="{{ route('pdf.recu', $p) }}" target="_blank" aria-label="Reçu PDF"><i class="fa-solid fa-file-pdf"></i></a></td></tr>
        @empty
          <tr><td colspan="6"><x-empty title="Aucun versement" /></td></tr>
        @endforelse
      </tbody></table></div>
    </div>
    <div class="mt-3">{{ $payments->links() }}</div>
  @else
    <x-panel :p0="true">
      @forelse ($retards as $x)
        <div class="row-item"><span class="ini">{{ $x->student->user->initials() }}</span>
          <div class="ri-main"><div class="ri-t">{{ $x->student->user->name }} · {{ $x->label }}</div><div class="ri-s">Échéance du {{ $x->due_on->translatedFormat('j F') }} ({{ $x->due_on->diffForHumans() }})@if ($x->reminded_at) · relancé {{ $x->reminded_at->diffForHumans() }}@endif</div></div>
          <b class="text-danger small me-2">{{ fcfa($x->remaining()) }}</b>
          <button class="btn btn-ghost btn-sm" wire:click="relancer({{ $x->id }})"><i class="fa-solid fa-bell"></i>Relancer</button>
          <button class="btn btn-primary btn-sm" wire:click="nouveau({{ $x->student_id }})">Encaisser</button></div>
      @empty
        <div class="p-4"><x-empty title="Aucun retard de paiement" text="Toutes les échéances passées sont réglées." /></div>
      @endforelse
    </x-panel>
  @endif

  @if ($form)
    <x-modal title="Enregistrer un versement" icon="fa-money-bill-wave">
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label">Étudiant</label>
          @if ($chosen)
            <div class="d-flex align-items-center gap-2 p-2 border rounded-3"><span class="ini">{{ $chosen->user->initials() }}</span><div class="flex-grow-1"><b>{{ $chosen->user->name }}</b><div class="small muted">{{ $chosen->matricule }} · reste {{ fcfa($chosen->balance()) }}</div></div><button class="btn btn-quiet btn-sm" wire:click="$set('etudiant', null)">Changer</button></div>
          @else
            <input class="form-control @error('etudiant') is-invalid @enderror" placeholder="Nom ou matricule…" wire:model.live.debounce.250ms="recherche" autofocus>
            @error('etudiant')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @foreach ($found as $f)<button type="button" class="d-block w-100 text-start btn btn-quiet btn-sm mt-1" wire:click="choisir({{ $f->id }})">{{ $f->user->name }} <span class="muted mono">{{ $f->matricule }}</span></button>@endforeach
          @endif
        </div>
        <div class="row g-3">
          <div class="col-sm-6"><label class="form-label">Montant (FCFA)</label><input class="form-control @error('montant') is-invalid @enderror" type="number" wire:model="montant">@error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <div class="col-sm-6"><label class="form-label">Date</label><input class="form-control" type="date" wire:model="date"></div>
          <div class="col-sm-6"><label class="form-label">Mode</label><select class="form-select" wire:model="methode">@foreach (\App\Models\Payment::METHODS as $m)<option>{{ $m }}</option>@endforeach</select></div>
          <div class="col-sm-6"><label class="form-label">Référence</label><input class="form-control" wire:model="reference" placeholder="Facultatif"></div>
        </div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer" wire:loading.attr="disabled"><i class="fa-solid fa-check"></i>Enregistrer et générer le reçu</button></x-slot:footer>
    </x-modal>
  @endif
</div>
