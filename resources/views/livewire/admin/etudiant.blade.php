@php($E = \App\Services\AvailabilityService::ETATS[$etat['etat']])
<div>
  <section class="panel mb-4"><div class="panel-b d-flex flex-wrap align-items-center gap-3">
    <span class="ini" style="width:56px;height:56px;font-size:20px">@if ($s->user->avatarUrl())<img src="{{ $s->user->avatarUrl() }}" alt="">@else{{ $s->user->initials() }}@endif</span>
    <div class="flex-grow-1">
      <div class="d-flex align-items-center gap-2 flex-wrap"><b style="font-size:18px">{{ $s->user->name }}</b><span class="mono small muted">{{ $s->matricule }}</span>
        <x-pill :tone="['actif' => 'ok', 'suspendu' => 'bad', 'certifie' => 'info', 'abandon' => 'mute'][$s->status]">{{ \App\Models\Student::STATUSES[$s->status] }}</x-pill></div>
      <div class="small muted">{{ $s->user->email }} · {{ $s->user->phone }} · {{ $s->city }} · {{ $s->promotion?->name }}</div>
      <div class="mt-1">@foreach ($s->formations as $f)<x-fchip :code="$f->code" /> @endforeach</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @if ($s->user->phone)<a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $s->user->phone) }}"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>@endif
      <a class="btn btn-ghost btn-sm" href="{{ route('pdf.attestation', $s) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>Attestation</a>
    </div>
  </div></section>

  <div class="stat-grid mb-4">
    <x-kpi label="Disponibilité projets" :value="$E[0]" :icon="$E[2]" :tone="$E[1]" :sub="$etat['late'] ? $etat['late'].' projet(s) en retard' : $E[3]" :sub-tone="$etat['late'] ? 'bad' : null" />
    <x-kpi label="Projets validés" :value="$etat['valides'].' / '.$etat['total']" icon="fa-circle-check" tone="ok" :sub="$etat['aValider'] ? $etat['aValider'].' à valider' : ''" />
    <x-kpi label="Reste à payer" :value="fcfa($s->balance())" icon="fa-wallet" :tone="$s->balance() ? 'cor' : 'ok'" :sub="'Payé : '.fcfa($s->totalPaid())" />
    <x-kpi label="Assiduité" :value="$s->attendanceRate().' %'" icon="fa-user-check" :tone="$s->attendanceRate() >= 80 ? 'ok' : 'warn'" />
  </div>

  <ul class="nav nav-tabs mb-3">
    @foreach (['projets' => 'Projets', 'paiements' => 'Paiements', 'presences' => 'Présences', 'compte' => 'Compte'] as $k => $l)
      <li class="nav-item"><button class="nav-link {{ $onglet === $k ? 'active' : '' }}" wire:click="$set('onglet','{{ $k }}')">{{ $l }}</button></li>
    @endforeach
  </ul>

  @if ($onglet === 'projets')
    <x-panel title="Projets" :p0="true">
      <x-slot:tools>@can('admin.projets')<button class="btn btn-primary btn-sm" wire:click="ouvrirAttribution"><i class="fa-solid fa-plus"></i>Attribuer un projet</button>@endcan</x-slot:tools>
      @forelse ($s->assignments->sortBy('due_on') as $a)
        <div class="row-item"><x-fchip :code="$a->project->formation->code" />
          <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div><div class="ri-s">À rendre le {{ $a->due_on->translatedFormat('j M Y') }}{{ $a->appreciation ? ' · '.$a->appreciation : '' }}</div></div>
          <x-pill :tone="$a->tone()">{{ $a->isLate() ? 'En retard' : $a->label(true) }}</x-pill>
          @if ($a->status === 'soumis')@can('admin.projets')<button class="btn btn-ghost btn-sm" wire:click="ouvrirCorrection({{ $a->id }})">Corriger</button>@endcan @endif
        </div>
      @empty
        <div class="p-4"><x-empty title="Aucun projet attribué" /></div>
      @endforelse
    </x-panel>
  @elseif ($onglet === 'paiements')
    <div class="u-grid u-1-1 align-items-start">
      <x-panel title="Échéancier" :p0="true">
        <x-slot:tools>@can('admin.finances')<button class="btn btn-primary btn-sm" wire:click="ouvrirVersement"><i class="fa-solid fa-plus"></i>Enregistrer un versement</button>@endcan</x-slot:tools>
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Échéance</th><th>Date</th><th class="text-end">Montant</th><th>État</th></tr></thead><tbody>
          @foreach ($schedules as $x)@php([$l, $t] = $x->state())<tr><td><x-fchip :code="$x->formation->code" /> {{ $x->label }}</td><td>{{ $x->due_on->translatedFormat('j M Y') }}</td><td class="text-end">{{ fcfa($x->amount) }}<div class="small muted">réglé {{ fcfa($x->paid()) }}</div></td><td><x-pill :tone="$t">{{ $l }}</x-pill></td></tr>@endforeach
        </tbody></table></div>
      </x-panel>
      <x-panel title="Versements" :p0="true">
        @forelse ($s->payments as $p)
          <div class="row-item"><span class="icon-sq"><i class="fa-solid fa-receipt"></i></span><div class="ri-main"><div class="ri-t">{{ fcfa($p->amount) }} · {{ $p->method }}</div><div class="ri-s">{{ $p->paid_on->translatedFormat('j M Y') }} · {{ $p->receipt_number }} · {{ $p->recorder?->name }}</div></div>
            <a class="btn btn-ghost btn-sm" href="{{ route('pdf.recu', $p) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i></a></div>
        @empty
          <div class="p-4"><x-empty title="Aucun versement" /></div>
        @endforelse
      </x-panel>
    </div>
  @elseif ($onglet === 'presences')
    <x-panel title="Dernières présences" :p0="true">
      @forelse ($presences as $p)
        <div class="row-item"><x-fchip :code="$p->seance->formation->code" /><div class="ri-main"><div class="ri-t">{{ $p->seance->title }}</div><div class="ri-s">{{ $p->seance->starts_at->translatedFormat('D j M · H\hi') }}</div></div><x-pill :tone="\App\Models\Presence::STATUSES[$p->status][1]">{{ \App\Models\Presence::STATUSES[$p->status][0] }}</x-pill></div>
      @empty
        <div class="p-4"><x-empty title="Aucune présence enregistrée" /></div>
      @endforelse
    </x-panel>
  @else
    <x-panel title="Compte et statut">
      <div class="lib-dl mb-3">
        <div class="lib-dl-row"><span>Dernière connexion</span><b>{{ $s->user->last_login_at?->diffForHumans() ?? 'Jamais' }}</b></div>
        <div class="lib-dl-row"><span>Compte créé le</span><b>{{ $s->created_at->translatedFormat('j F Y') }}</b></div>
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-ghost btn-sm" wire:click="renvoyerActivation"><i class="fa-solid fa-paper-plane"></i>Envoyer un lien de connexion</button>
        @if ($s->status === 'suspendu')<button class="btn btn-primary btn-sm" wire:click="statut('actif')">Réactiver le compte</button>
        @else<button class="btn btn-outline-danger btn-sm" wire:click="statut('suspendu')" wire:confirm="Suspendre ce compte ? L'étudiant ne pourra plus se connecter.">Suspendre le compte</button>@endif
        @if ($s->status !== 'abandon')<button class="btn btn-ghost btn-sm" wire:click="statut('abandon')" wire:confirm="Marquer cet étudiant en abandon ?">Abandon</button>@endif
      </div>
    </x-panel>
  @endif

  @if ($versement)
    <x-modal title="Enregistrer un versement" icon="fa-money-bill-wave">
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label" for="m">Montant (FCFA)</label><input class="form-control @error('montant') is-invalid @enderror" type="number" id="m" wire:model="montant">@error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label" for="d">Date</label><input class="form-control @error('date') is-invalid @enderror" type="date" id="d" wire:model="date">@error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label" for="me">Mode</label><select class="form-select" id="me" wire:model="methode">@foreach (\App\Models\Payment::METHODS as $me)<option>{{ $me }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label" for="r">Référence de transaction</label><input class="form-control" id="r" wire:model="reference" placeholder="Facultatif"></div>
      </div>
      <p class="small muted mt-3 mb-0">Reste à payer : {{ fcfa($s->balance()) }}. Un reçu PDF est généré et l'étudiant est prévenu.</p>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerVersement" wire:loading.attr="disabled"><i class="fa-solid fa-check"></i>Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
  @if ($attribution)
    <x-modal title="Attribuer un projet" icon="fa-paper-plane">
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label" for="pj">Projet</label><select class="form-select @error('projet') is-invalid @enderror" id="pj" wire:model="projet"><option value="">Choisir…</option>@foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->formation->code }} — {{ $p->title }}{{ $s->assignments->contains('project_id', $p->id) ? ' (déjà reçu)' : '' }}</option>@endforeach</select>@error('projet')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><label class="form-label" for="ech">À rendre le</label><input class="form-control @error('echeance') is-invalid @enderror" type="date" id="ech" wire:model="echeance">@error('echeance')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="attribuer"><i class="fa-solid fa-paper-plane"></i>Attribuer</button></x-slot:footer>
    </x-modal>
  @endif
  @include('livewire.partials.correction')
</div>
