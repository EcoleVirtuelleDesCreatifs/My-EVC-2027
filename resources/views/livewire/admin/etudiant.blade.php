@php
  $E = \App\Services\AvailabilityService::ETATS[$etat['etat']];
  $reportsOk = $s->finalReports->where('status', 'valide')->count();
  $reportsTodo = $s->finalReports->where('status', 'depose')->count();
  $sessionEnded = $s->promotion?->ends_on?->copy()->endOfDay()->isPast();
  $tabs = [
      'projets' => ['Projets', $s->assignments->count(), $etat['aValider'] ? 'warn' : null],
      'paiements' => ['Paiements', $s->payments->count(), $s->balance() ? null : 'ok'],
      'presences' => ['Présences', null, null],
      'rapport' => ['Rapport de fin', $reportsOk.'/'.$s->formations->count(), $reportsTodo ? 'warn' : ($reportsOk === $s->formations->count() ? 'ok' : ($sessionEnded ? 'bad' : null))],
      'compte' => ['Compte', null, null],
  ];
@endphp
<div @if (! $versement && ! $attribution && ! $editionProfil && ! $corrige && ! $inscription) wire:poll.15s.visible @endif>
  <section class="panel mb-4"><div class="panel-b d-flex flex-wrap align-items-center gap-3">
    <span class="ini ini-0 fiche-ini">@if ($s->user->avatarUrl())<img src="{{ $s->user->avatarUrl() }}" alt="Photo de {{ $s->user->name }}">@else{{ $s->user->initials() }}@endif</span>
    <div class="flex-grow-1">
      <div class="d-flex align-items-center gap-2 flex-wrap"><b style="font-size:18px">{{ $s->user->name }}</b><span class="mono small muted">{{ $s->matricule }}</span>
        <x-pill :tone="['actif' => 'ok', 'suspendu' => 'bad', 'certifie' => 'info', 'abandon' => 'mute'][$s->status]">{{ \App\Models\Student::STATUSES[$s->status] }}</x-pill></div>
      <div class="small muted fiche-meta">@foreach (array_filter([['fa-envelope', $s->user->email], ['fa-phone', $s->user->phone], ['fa-location-dot', $s->city ? $s->city.($s->country ? ', '.$s->country : '') : null], ['fa-calendar', $s->promotion?->name]], fn ($m) => $m[1]) as [$ic, $v])<span><i class="fa-solid {{ $ic }}"></i>{{ $v }}</span>@endforeach</div>
      <div class="mt-1 d-flex flex-wrap align-items-center gap-1">@foreach ($s->formations as $f)<x-fchip :code="$f->code" /> @endforeach
        @can('admin.etudiants')<button type="button" class="btn btn-quiet btn-sm py-0 px-2 small" wire:click="gererFormations('ajouter')"><i class="fa-solid fa-plus me-1"></i>Formation</button>@endcan</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      @can('admin.etudiants')<button type="button" class="btn btn-primary btn-sm" wire:click="modifierProfil"><i class="fa-solid fa-user-pen"></i>Modifier le profil</button>@endcan
      @if ($s->user->phone)<a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $s->user->phone) }}"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>@endif
      <a class="btn btn-ghost btn-sm" href="{{ route('pdf.attestation', $s) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>Attestation</a>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.portfolio', $s) }}" wire:navigate><i class="fa-solid fa-swatchbook"></i>Portfolio</a>
    </div>
  </div></section>

  <div class="stat-grid mb-4">
    <x-kpi label="Disponibilité projets" :value="$E[0]" :icon="$E[2]" :tone="$E[1]" :sub="$etat['late'] ? $etat['late'].' projet(s) en retard' : $E[3]" :sub-tone="$etat['late'] ? 'bad' : null" />
    <x-kpi label="Projets validés" :value="$etat['valides'].' / '.$etat['total']" icon="fa-circle-check" tone="ok" :sub="$etat['aValider'] ? $etat['aValider'].' à valider' : ''" />
    <x-kpi label="Reste à payer" :value="fcfa($s->balance())" icon="fa-wallet" :tone="$s->balance() ? 'cor' : 'ok'" :sub="'Payé : '.fcfa($s->totalPaid())" />
    <x-kpi label="Assiduité" :value="$s->attendanceRate().' %'" icon="fa-user-check" :tone="$s->attendanceRate() >= 80 ? 'ok' : 'warn'" />
  </div>

  <ul class="nav nav-tabs mb-3">
    @foreach ($tabs as $k => [$l, $n, $tone])
      <li class="nav-item"><button class="nav-link {{ $onglet === $k ? 'active' : '' }}" wire:click="$set('onglet','{{ $k }}')">{{ $l }}@if ($n !== null) <span class="tab-n {{ $tone ? 'tn-'.$tone : '' }}">{{ $n }}</span>@endif</button></li>
    @endforeach
  </ul>

  @if ($onglet === 'projets')
    <x-panel title="Projets" :p0="true">
      <x-slot:tools>@can('admin.projets')<button class="btn btn-primary btn-sm" wire:click="ouvrirAttribution"><i class="fa-solid fa-plus"></i>Attribuer un projet</button>@endcan</x-slot:tools>
      @forelse ($s->assignments->sortBy('due_on') as $a)
        <div class="row-item"><x-fchip :code="$a->project->formation->code" />
          <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div>
            <div class="ri-s">{{ match ($a->status) {
                'valide' => 'Validé le '.$a->validated_at?->translatedFormat('j M Y').($a->appreciation ? ' · '.$a->appreciation : ''),
                'soumis' => 'Rendu le '.$a->submitted_at?->translatedFormat('j M Y à H\hi').' · à corriger',
                default => 'À rendre le '.$a->due_on->translatedFormat('j M Y').' · '.mb_strtolower($a->dueText()),
            } }}{{ $a->submissions_count > 1 ? ' · '.$a->submissions_count.' dépôts' : '' }}</div></div>
          <x-pill :tone="$a->tone()">{{ $a->isLate() ? 'En retard' : $a->label(true) }}</x-pill>
          @can('admin.projets')<a class="btn btn-ghost btn-sm" href="{{ route('projet.echanges', $a) }}" wire:navigate><i class="fa-solid fa-comments"></i>Échanges</a>@endcan
          @if ($a->status === 'soumis')@can('admin.projets')<button class="btn btn-ghost btn-sm" wire:click="ouvrirCorrection({{ $a->id }})">Corriger</button>@endcan @endif
        </div>
      @empty
        <div class="p-4"><x-empty title="Aucun projet attribué" /></div>
      @endforelse
    </x-panel>
  @elseif ($onglet === 'paiements')
    <x-panel title="Formations et montants" class="mb-3" :p0="true">
      <x-slot:tools>@can('admin.etudiants')<button class="btn btn-ghost btn-sm" wire:click="gererFormations('ajouter')"><i class="fa-solid fa-plus"></i>Ajouter une formation</button>@endcan</x-slot:tools>
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Formation</th><th>Session · modalité</th><th class="text-end">Prix</th><th class="text-end">Réglé</th><th class="text-end">Reste</th><th></th></tr></thead><tbody>
        @foreach ($parFormation as $row)
          <tr><td><x-fchip :code="$row['f']->code" /> <b>{{ $row['f']->name }}</b></td>
            <td class="small">{{ \App\Models\Promotion::find($row['f']->pivot->promotion_id)?->name ?? '—' }} · {{ $row['f']->pivot->mode }}</td>
            <td class="text-end text-nowrap">{{ fcfa($row['due']) }}</td><td class="text-end text-nowrap text-success">{{ fcfa($row['paid']) }}</td>
            <td class="text-end text-nowrap fw-sb {{ $row['due'] - $row['paid'] > 0 ? 'text-danger' : '' }}">{{ fcfa(max(0, $row['due'] - $row['paid'])) }}</td>
            <td class="table-actions text-nowrap">@can('admin.etudiants')
              <button class="btn btn-quiet btn-sm" wire:click="gererFormations('remplacer', {{ $row['f']->id }})"><i class="fa-solid fa-right-left me-1"></i>Remplacer</button>
              @if ($s->formations->count() > 1)<button class="btn btn-quiet btn-sm" wire:click="gererFormations('retirer', {{ $row['f']->id }})"><i class="fa-solid fa-xmark me-1"></i>Retirer</button>@endif
            @endcan</td></tr>
        @endforeach
      </tbody><tfoot><tr><th colspan="2">Total global</th><th class="text-end text-nowrap">{{ fcfa($s->totalDue()) }}</th><th class="text-end text-nowrap text-success">{{ fcfa($s->totalPaid()) }}</th><th class="text-end text-nowrap {{ $s->balance() ? 'text-danger' : '' }}">{{ fcfa($s->balance()) }}</th><th>@if ($s->totalPaid() > $s->totalDue())<span class="small text-success">Avoir {{ fcfa($s->totalPaid() - $s->totalDue()) }}</span>@endif</th></tr></tfoot></table></div>
    </x-panel>
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
  @elseif ($onglet === 'rapport')
    <div class="stack">
      <div class="alert-strip {{ $reportsOk === $s->formations->count() ? 'as-ok' : ($sessionEnded ? 'as-bad' : 'as-info') }}"><i class="fa-solid fa-file-signature"></i>
        <span>{{ $reportsOk }} rapport{{ $reportsOk > 1 ? 's' : '' }} validé{{ $reportsOk > 1 ? 's' : '' }} sur {{ $s->formations->count() }}@if ($reportsTodo) · <b>{{ $reportsTodo }} à relire</b>@endif
          @if ($s->promotion?->ends_on) · fin de session {{ $sessionEnded ? 'le' : 'prévue le' }} {{ $s->promotion->ends_on->translatedFormat('j F Y') }}@endif</span></div>
      @foreach ($s->formations as $f)
        @php($r = $s->finalReports->firstWhere('formation_id', $f->id))
        @php([$rl, $rt] = \App\Models\FinalReport::label($r))
        <x-panel :title="$f->name">
          <x-slot:tools><x-pill :tone="! $r && $sessionEnded ? 'bad' : $rt">{{ ! $r && $sessionEnded ? 'En retard' : $rl }}</x-pill></x-slot:tools>
          @if ($r)
            <a class="rp-file" href="{{ route('fichier.rapport', $r) }}">
              <span class="pf-dl-ico c-{{ $f->code }}"><i class="fa-solid {{ \App\Support\Portfolio::FORMATS[strtolower(pathinfo($r->original_name, PATHINFO_EXTENSION))][1] ?? 'fa-file' }}"></i></span>
              <span class="pf-dl-name"><b>{{ $r->original_name }}</b><small>Version {{ $r->version }} · déposé le {{ $r->submitted_at->translatedFormat('j F Y à H\hi') }}</small></span>
              <i class="fa-solid fa-download muted"></i>
            </a>
            @if ($r->message)<div class="rp-feedback"><b><i class="fa-solid fa-user-graduate me-1"></i>Message de l’étudiant</b><p class="mb-0">{{ $r->message }}</p></div>@endif
            @if ($r->feedback)<div class="rp-feedback {{ $r->status === 'a_reprendre' ? 'bad' : 'ok' }}"><b><i class="fa-solid fa-comment-dots me-1"></i>Retour envoyé</b><p class="mb-0">{{ $r->feedback }}</p>@if ($r->reviewer)<small>{{ $r->reviewer->name }} · {{ $r->reviewed_at?->translatedFormat('j F Y') }}</small>@endif</div>@endif
            @if ($r->status === 'depose')
              @can('admin.etudiants')
                <div class="mt-3">
                  <label class="form-label" for="rt{{ $r->id }}">Retour à l’étudiant <span class="small muted">(obligatoire pour demander des compléments)</span></label>
                  <textarea class="form-control" id="rt{{ $r->id }}" rows="3" wire:model="retour.{{ $r->id }}" placeholder="Points forts, éléments à compléter…"></textarea>
                  @error('retour.'.$r->id)<div class="small text-danger">{{ $message }}</div>@enderror
                  <div class="d-flex flex-wrap gap-2 mt-2">
                    <button class="btn btn-primary btn-sm" wire:click="relireRapport({{ $r->id }}, true)" wire:confirm="Valider ce rapport ? L’étudiant sera prévenu par e-mail."><i class="fa-solid fa-circle-check"></i>Valider le rapport</button>
                    <button class="btn btn-ghost btn-sm" wire:click="relireRapport({{ $r->id }}, false)"><i class="fa-solid fa-rotate"></i>Demander des compléments</button>
                  </div>
                </div>
              @endcan
            @endif
          @else
            <x-empty title="Rapport non déposé" text="L’étudiant le dépose depuis la rubrique « Rapport de fin » de son espace. Vous serez prévenu par e-mail." />
          @endif
        </x-panel>
      @endforeach
    </div>
  @else
    <x-panel title="Compte et statut">
      <div class="lib-dl mb-3">
        <div class="lib-dl-row"><span>Dernière connexion</span><b>{{ $s->user->last_login_at?->diffForHumans() ?? 'Jamais' }}</b></div>
        <div class="lib-dl-row"><span>Compte créé le</span><b>{{ $s->created_at->translatedFormat('j F Y') }}</b></div>
        <div class="lib-dl-row"><span>Accès à l’espace</span><b>@if ($s->isReadOnly())<x-pill tone="warn">Lecture seule depuis le {{ $s->accessEndsOn()->translatedFormat('j F Y') }}</x-pill>@elseif ($s->accessEndsOn())<x-pill tone="ok">Complet jusqu’au {{ $s->accessEndsOn()->translatedFormat('j F Y') }}</x-pill>@else<x-pill tone="ok">Complet</x-pill>@endif</b></div>
      </div>
      @can('admin.etudiants')
        <form wire:submit="enregistrerAcces" class="d-flex flex-wrap align-items-end gap-2 mb-3">
          <div><label class="form-label" for="acc">Prolonger l’accès complet jusqu’au</label><input class="form-control @error('accesJusquau') is-invalid @enderror" type="date" id="acc" wire:model="accesJusquau">@error('accesJusquau')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <button class="btn btn-ghost btn-sm" type="submit">Enregistrer</button>
          <span class="small muted w-100">Vide : l’accès complet s’arrête à la fin de la dernière session suivie. Ensuite, le compte passe en lecture seule (messagerie seule).</span>
        </form>
      @endcan
      <div class="d-flex flex-wrap gap-2">
        <button class="btn btn-ghost btn-sm" wire:click="renvoyerActivation"><i class="fa-solid fa-paper-plane"></i>Envoyer un lien de connexion</button>
        @if ($s->status === 'suspendu')<button class="btn btn-primary btn-sm" wire:click="statut('actif')">Réactiver le compte</button>
        @else<button class="btn btn-outline-danger btn-sm" wire:click="statut('suspendu')" wire:confirm="Suspendre ce compte ? L'étudiant ne pourra plus se connecter.">Suspendre le compte</button>@endif
        @if ($s->status !== 'abandon')<button class="btn btn-ghost btn-sm" wire:click="statut('abandon')" wire:confirm="Marquer cet étudiant en abandon ?">Abandon</button>@endif
      </div>
    </x-panel>
  @endif

  @if ($inscription && $simulation)
    <x-modal title="Formations de l’étudiant" icon="fa-graduation-cap">
      <div class="seg seg-soft d-flex mb-3" role="group" aria-label="Action">
        @foreach (['ajouter' => 'Ajouter une formation', 'remplacer' => 'Remplacer', 'retirer' => 'Retirer'] as $k => $l)
          @continue($k === 'retirer' && $s->formations->count() < 2)
          <button type="button" class="{{ $action === $k ? 'on' : '' }}" wire:click="$set('action','{{ $k }}')">{{ $l }}</button>
        @endforeach
      </div>
      <div class="row g-3">
        @if ($action !== 'ajouter')
          <div class="col-12"><label class="form-label" for="fa">{{ $action === 'retirer' ? 'Formation à retirer' : 'Formation actuelle à remplacer' }}</label>
            <select class="form-select @error('ancienne') is-invalid @enderror" id="fa" wire:model.live="ancienne">@foreach ($s->formations as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select>@error('ancienne')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @endif
        @if ($action !== 'retirer')
          <div class="col-12"><label class="form-label" for="fn">{{ $action === 'remplacer' ? 'Nouvelle formation' : 'Formation à ajouter' }}</label>
            <select class="form-select @error('nouvelle') is-invalid @enderror" id="fn" wire:model.live="nouvelle"><option value="">Choisir…</option>
              @foreach ($catalogue->reject(fn ($f) => $s->formations->contains('id', $f->id)) as $f)<option value="{{ $f->id }}">{{ $f->name }} — {{ fcfa($f->price) }} ({{ $f->months }} mois)</option>@endforeach</select>
            @error('nouvelle')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <div class="col-sm-6"><label class="form-label" for="fs">Session</label><select class="form-select" id="fs" wire:model="sessionId">@foreach ($sessions as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
          <div class="col-sm-6"><label class="form-label" for="fm">Modalité</label><select class="form-select" id="fm" wire:model="modeInscription"><option>En ligne</option><option>Présentiel</option></select></div>
        @endif
      </div>
      <div class="fin-sim mt-3">
        <div><span>Total actuel des formations</span><b>{{ fcfa($simulation['total']) }}</b></div>
        @if ($simulation['remove'])<div><span>− {{ $simulation['remove']->name }}</span><b class="text-danger">− {{ fcfa($simulation['removed']) }}</b></div>@endif
        @if ($simulation['add'])<div><span>+ {{ $simulation['add']->name }} <small class="muted">(acompte {{ fcfa($simulation['add']->deposit) }} · solde {{ fcfa($simulation['add']->balance()) }})</small></span><b class="text-success">+ {{ fcfa($simulation['added']) }}</b></div>@endif
        <div class="sep"><span>Nouveau total global</span><b>{{ fcfa($simulation['newTotal']) }}</b></div>
        <div><span>Déjà payé <small class="muted">(reste acquis)</small></span><b>{{ fcfa($simulation['paid']) }}</b></div>
        <div class="tot"><span>Reste à solder</span><b>{{ fcfa($simulation['newBalance']) }}</b></div>
        @if ($simulation['credit'])<div><span>Avoir en faveur de l’étudiant</span><b class="text-success">{{ fcfa($simulation['credit']) }}</b></div>@endif
        <p class="small muted mb-0 mt-2">Les versements déjà reçus sont réimputés automatiquement sur l’échéancier, de la plus ancienne échéance à la plus récente. @if ($simulation['remove'])L’historique des projets de la formation retirée est conservé.@endif L’étudiant est prévenu par e-mail.</p>
      </div>
      @error('inscription')<div class="alert alert-danger small mt-2 mb-0">{{ $message }}</div>@enderror
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="confirmerInscription" wire:loading.attr="disabled" @disabled($action !== 'retirer' && ! $nouvelle)><i class="fa-solid fa-check"></i>Confirmer et recalculer</button></x-slot:footer>
    </x-modal>
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
        <div><label class="form-label" for="pj">Projet</label><select class="form-select @error('projet') is-invalid @enderror" id="pj" wire:model="projet" @disabled($projects->isEmpty())><option value="">Choisir…</option>@foreach ($projects as $p)<option value="{{ $p->id }}">{{ $p->formation->code }} — {{ $p->title }}</option>@endforeach</select>@error('projet')<div class="invalid-feedback">{{ $message }}</div>@enderror
          <p class="small muted mt-2 mb-0">{{ $projects->isEmpty() ? 'Aucun nouveau projet disponible pour cet étudiant.' : 'Seuls les projets publiés de ses formations qu’il n’a jamais reçus sont proposés.' }} Les projets déjà attribués restent dans son historique.</p>
        </div>
        <div><label class="form-label" for="ech">À rendre le</label><input class="form-control @error('echeance') is-invalid @enderror" type="date" id="ech" wire:model="echeance">@error('echeance')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="attribuer" @disabled($projects->isEmpty()) wire:loading.attr="disabled" wire:target="attribuer"><i class="fa-solid fa-paper-plane"></i>Attribuer</button></x-slot:footer>
    </x-modal>
  @endif
  @if ($editionProfil)
    <x-modal title="Modifier le profil étudiant" icon="fa-user-pen" size="modal-lg">
      <form id="student-profile-form" wire:submit="enregistrerProfil" class="row g-3">
        @foreach (['name' => ['Nom complet', 'text'], 'email' => ['Adresse e-mail', 'email'], 'phone' => ['Téléphone / WhatsApp', 'tel'], 'birth_date' => ['Date de naissance', 'date'], 'gender' => ['Genre', 'text'], 'country' => ['Pays', 'text'], 'city' => ['Ville', 'text']] as $field => [$label, $type])
          <div class="col-md-6"><label class="form-label" for="student-profile-{{ $field }}">{{ $label }}</label><input id="student-profile-{{ $field }}" type="{{ $type }}" class="form-control @error('profil.'.$field) is-invalid @enderror" wire:model="profil.{{ $field }}" @required(in_array($field, ['name', 'email']))>@error('profil.'.$field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @endforeach
        <div class="col-md-6"><label class="form-label" for="student-profile-level">Niveau</label><select id="student-profile-level" class="form-select" wire:model="profil.level">@foreach (['Débutant', 'Intermédiaire', 'Avancé'] as $level)<option>{{ $level }}</option>@endforeach</select>@error('profil.level')<div class="text-danger small">{{ $message }}</div>@enderror</div>
        <p class="small muted mb-0">Le matricule, les formations, la session, les paiements, les rôles et le mot de passe ne sont pas modifiés. L’adresse e-mail sert aussi à la connexion.</p>
      </form>
      <x-slot:footer><button type="button" class="btn btn-ghost" wire:click="closeModal">Annuler</button><button type="submit" form="student-profile-form" class="btn btn-primary" wire:loading.attr="disabled" wire:target="enregistrerProfil">Enregistrer le profil</button></x-slot:footer>
    </x-modal>
  @endif
  @include('livewire.partials.correction')
</div>
