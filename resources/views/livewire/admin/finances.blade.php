<div>
  @php($periodLabel = ucfirst($from->translatedFormat('F Y')))
  <div class="stat-grid fin-kpis mb-4">
    <x-kpi label="Solde de caisse" :value="fcfa($kpi['balance'])" icon="fa-cash-register" :tone="$kpi['balance'] >= 0 ? 'ok' : 'bad'" :sub="'Encaissé '.fcfa($kpi['in']).' − dépenses '.fcfa($kpi['out'])" :sub-tone="$kpi['balance'] < 0 ? 'bad' : null" />
    <x-kpi label="Encaissé · {{ $periodLabel }}" :value="fcfa($kpi['periodIn'])" icon="fa-arrow-down" tone="lav" sub="versements des étudiants" />
    <x-kpi label="Dépenses · {{ $periodLabel }}" :value="fcfa($kpi['periodOut'])" icon="fa-arrow-up" tone="cor" :sub="'Résultat du mois : '.($kpi['periodResult'] >= 0 ? '+' : '−').fcfa(abs($kpi['periodResult']))" :sub-tone="$kpi['periodResult'] < 0 ? 'bad' : 'ok'" />
    <x-kpi label="Reste à encaisser" :value="fcfa($kpi['due'])" icon="fa-hourglass-half" tone="warn" :sub="$retards->count().' échéance(s) en retard · '.fcfa($retards->sum(fn ($x) => $x->remaining()))" :sub-tone="$retards->count() ? 'bad' : null" />
  </div>
  <div class="filters-row mb-3">
    <ul class="nav nav-tabs border-0 flex-grow-1">
      <li class="nav-item"><button class="nav-link {{ $vue === 'versements' ? 'active' : '' }}" wire:click="$set('vue','versements')">Versements</button></li>
      <li class="nav-item"><button class="nav-link {{ $vue === 'depenses' ? 'active' : '' }}" wire:click="$set('vue','depenses')">Dépenses</button></li>
      <li class="nav-item"><button class="nav-link {{ $vue === 'caisse' ? 'active' : '' }}" wire:click="$set('vue','caisse')">Caisse</button></li>
      <li class="nav-item"><button class="nav-link {{ $vue === 'retards' ? 'active' : '' }}" wire:click="$set('vue','retards')">Retards et relances <span class="badge rounded-pill text-bg-warning">{{ $retards->count() }}</span></button></li>
    </ul>
    @if ($vue === 'versements')<label class="search-field mb-0" style="min-width:260px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Reçu, étudiant, matricule…" wire:model.live.debounce.300ms="q"></label>@endif
    @if (in_array($vue, ['depenses', 'caisse']))
      <select class="form-select" wire:model.live="periode" aria-label="Période" style="max-width:200px">@foreach ($months as $k => $l)<option value="{{ $k === now()->format('Y-m') ? '' : $k }}">{{ $l }}</option>@endforeach</select>
    @endif
    @if ($vue === 'depenses')
      <select class="form-select" wire:model.live="categorie" aria-label="Catégorie" style="max-width:240px"><option value="">Toutes les catégories</option>@foreach (\App\Models\Expense::CATEGORIES as $c)<option>{{ $c }}</option>@endforeach</select>
      <label class="search-field mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Libellé, bénéficiaire…" wire:model.live.debounce.300ms="q" aria-label="Rechercher une dépense"></label>
      <button class="btn btn-primary" wire:click="nouvelleDepense"><i class="fa-solid fa-plus"></i>Ajouter une dépense</button>
    @else
      <button class="btn btn-primary d-sm-none" wire:click="nouveau"><i class="fa-solid fa-plus"></i>Versement</button>
    @endif
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
  @elseif ($vue === 'depenses')
    <div class="panel tbl" wire:loading.class="wire-loading">
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Date</th><th>Dépense</th><th>Catégorie</th><th>Caisse</th><th class="text-end">Montant</th><th></th></tr></thead><tbody>
        @forelse ($expenses as $e)
          <tr><td class="text-nowrap">{{ $e->spent_on->translatedFormat('j M Y') }}</td>
            <td><b>{{ $e->label }}</b><div class="small muted">{{ collect([$e->beneficiary, $e->reference, 'saisie par '.($e->recorder?->name ?? '—')])->filter()->implode(' · ') }}</div></td>
            <td><span class="pill p-lav nodot">{{ $e->category }}</span></td><td class="small">{{ $e->method }}</td>
            <td class="text-end fw-sb text-danger text-nowrap">− {{ fcfa($e->amount) }}</td>
            <td class="table-actions text-nowrap">
              @if ($e->receipt_path)<a class="btn btn-quiet btn-sm btn-icon" href="{{ route('fichier.depense', $e) }}" aria-label="Justificatif" title="{{ $e->receipt_name }}"><i class="fa-solid fa-paperclip"></i></a>@endif
              <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifierDepense({{ $e->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
              <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimerDepense({{ $e->id }})" wire:confirm="Supprimer cette dépense ? Le solde de caisse sera recalculé." aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button></td></tr>
        @empty
          <tr><td colspan="6"><x-empty title="Aucune dépense sur cette période" text="Enregistrez les sorties d’argent (loyer, salaires, matériel…) pour suivre le solde réel de la caisse." /></td></tr>
        @endforelse
      </tbody>
      @if ($expenses->total())<tfoot><tr><th colspan="4">Total · {{ $periodLabel }}{{ $categorie ? ' · '.$categorie : '' }}</th><th class="text-end text-danger text-nowrap">− {{ fcfa($expensesTotal) }}</th><th></th></tr></tfoot>@endif
      </table></div>
    </div>
    <div class="mt-3">{{ $expenses->links() }}</div>
  @elseif ($vue === 'caisse')
    <div class="d-flex flex-wrap gap-2 mb-3"><button class="btn btn-primary btn-sm" wire:click="nouvelleDepense"><i class="fa-solid fa-plus"></i>Ajouter une dépense</button><button class="btn btn-ghost btn-sm" wire:click="nouveau"><i class="fa-solid fa-plus"></i>Enregistrer un versement</button></div>
    <div class="u-grid u-2-1 align-items-start">
      <x-panel title="Soldes par caisse" sub="Encaissements − dépenses, depuis le début · mouvements de {{ $periodLabel }}" :p0="true">
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Caisse</th><th class="text-end">Entrées · mois</th><th class="text-end">Sorties · mois</th><th class="text-end">Solde</th></tr></thead><tbody>
          @foreach ($caisse['methods'] as $m)
            <tr><td><b>{{ $m['method'] }}</b><div class="small muted">{{ fcfa($m['in']) }} encaissés · {{ fcfa($m['out']) }} dépensés</div></td>
              <td class="text-end text-success text-nowrap">{{ $m['periodIn'] ? '+ '.fcfa($m['periodIn']) : '—' }}</td>
              <td class="text-end text-danger text-nowrap">{{ $m['periodOut'] ? '− '.fcfa($m['periodOut']) : '—' }}</td>
              <td class="text-end fw-sb text-nowrap {{ $m['balance'] < 0 ? 'text-danger' : '' }}">{{ fcfa($m['balance']) }}</td></tr>
          @endforeach
        </tbody><tfoot><tr><th>Total</th><th class="text-end text-success text-nowrap">+ {{ fcfa($kpi['periodIn']) }}</th><th class="text-end text-danger text-nowrap">− {{ fcfa($kpi['periodOut']) }}</th><th class="text-end text-nowrap">{{ fcfa($kpi['balance']) }}</th></tr></tfoot></table></div>
      </x-panel>
      <x-panel title="Dépenses par catégorie · {{ $periodLabel }}">
        @forelse ($caisse['categories'] as $c)
          <div class="fin-cat"><div class="d-flex justify-content-between small"><span>{{ $c['category'] }} <span class="muted">({{ $c['n'] }})</span></span><b>{{ fcfa($c['total']) }}</b></div>
            <div class="bar mt-1"><span style="width:{{ $kpi['periodOut'] ? round($c['total'] / $kpi['periodOut'] * 100) : 0 }}%;background:var(--corail)"></span></div></div>
        @empty
          <p class="small muted mb-0">Aucune dépense sur cette période.</p>
        @endforelse
      </x-panel>
    </div>
    <x-panel title="Évolution sur 6 mois" class="mt-3" :p0="true">
      @php($peak = max(1, collect($caisse['months'])->max(fn ($m) => max($m['in'], $m['out']))))
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Mois</th><th style="width:40%"></th><th class="text-end">Encaissé</th><th class="text-end">Dépensé</th><th class="text-end">Résultat</th></tr></thead><tbody>
        @foreach ($caisse['months'] as $m)
          <tr><td class="text-nowrap">{{ $m['label'] }}</td>
            <td><div class="fin-bars"><span class="in" style="width:{{ round($m['in'] / $peak * 100) }}%"></span><span class="out" style="width:{{ round($m['out'] / $peak * 100) }}%"></span></div></td>
            <td class="text-end text-success text-nowrap">{{ fcfa($m['in']) }}</td><td class="text-end text-danger text-nowrap">{{ fcfa($m['out']) }}</td>
            <td class="text-end fw-sb text-nowrap {{ $m['result'] < 0 ? 'text-danger' : 'text-success' }}">{{ ($m['result'] >= 0 ? '+ ' : '− ').fcfa(abs($m['result'])) }}</td></tr>
        @endforeach
      </tbody></table></div>
    </x-panel>
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

  @if ($depenseForm)
    <x-modal :title="$depenseId ? 'Modifier la dépense' : 'Enregistrer une dépense'" icon="fa-receipt">
      <div class="row g-3">
        <div class="col-12"><label class="form-label" for="dl">Libellé</label><input class="form-control @error('d.label') is-invalid @enderror" id="dl" wire:model="d.label" placeholder="Ex. Loyer de novembre, achat de câbles HDMI…">@error('d.label')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label" for="da">Montant (FCFA)</label><input class="form-control @error('d.amount') is-invalid @enderror" type="number" min="1" id="da" wire:model="d.amount">@error('d.amount')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label" for="dd">Date</label><input class="form-control @error('d.spent_on') is-invalid @enderror" type="date" id="dd" wire:model="d.spent_on">@error('d.spent_on')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label" for="dc">Catégorie</label><select class="form-select" id="dc" wire:model="d.category">@foreach (\App\Models\Expense::CATEGORIES as $c)<option>{{ $c }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label" for="dm">Payé depuis la caisse</label><select class="form-select" id="dm" wire:model="d.method">@foreach (\App\Models\Payment::METHODS as $m)<option>{{ $m }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label" for="db">Bénéficiaire</label><input class="form-control" id="db" wire:model="d.beneficiary" placeholder="Facultatif"></div>
        <div class="col-sm-6"><label class="form-label" for="dr">Référence</label><input class="form-control" id="dr" wire:model="d.reference" placeholder="N° de facture, de transaction…"></div>
        <div class="col-12"><label class="form-label" for="dj">Justificatif <span class="small muted">(PDF ou image, 10 Mo max.)</span></label><input class="form-control @error('justificatif') is-invalid @enderror" type="file" id="dj" wire:model="justificatif" accept=".pdf,.jpg,.jpeg,.png,.webp">@error('justificatif')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label" for="dn">Note</label><textarea class="form-control" id="dn" rows="2" wire:model="d.note"></textarea></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerDepense" wire:loading.attr="disabled" wire:target="enregistrerDepense,justificatif"><i class="fa-solid fa-check"></i>Enregistrer</button></x-slot:footer>
    </x-modal>
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
