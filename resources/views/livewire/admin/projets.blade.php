<div>
  <a class="dispo-link mb-3" href="{{ route('admin.disponibilite') }}" wire:navigate><span class="dl-i"><i class="fa-solid fa-user-clock"></i></span><span class="lr-m"><b>Disponibilité des étudiants</b><span class="small muted d-block">Qui est libre, qui a reçu un projet, qui travaille dessus</span></span><i class="fa-solid fa-chevron-right muted ms-auto"></i></a>
  <div class="filters-row mb-3">
    <ul class="nav nav-tabs flex-grow-1 border-0">
      <li class="nav-item"><button class="nav-link {{ $onglet === 'valider' ? 'active' : '' }}" wire:click="$set('onglet','valider')">À valider <span class="badge rounded-pill text-bg-warning">{{ $queue->count() }}</span></button></li>
      <li class="nav-item"><button class="nav-link {{ $onglet === 'catalogue' ? 'active' : '' }}" wire:click="$set('onglet','catalogue')">Catalogue</button></li>
      <li class="nav-item"><button class="nav-link {{ $onglet === 'auto' ? 'active' : '' }}" wire:click="$set('onglet','auto')"><i class="fa-solid fa-robot me-1"></i>Attribution automatique <span class="badge rounded-pill {{ $autoConfig['enabled'] ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $autoConfig['enabled'] ? 'Activée' : 'Désactivée' }}</span></button></li>
    </ul>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
  </div>

  @if ($onglet === 'valider')
    <x-panel :p0="true">
      @forelse ($queue as $a)
        <div class="row-item"><x-fchip :code="$a->project->formation->code" />
          <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div><div class="ri-s">{{ $a->student->user->name }} · rendu {{ $a->submitted_at?->diffForHumans() }}</div></div>
          <button class="btn btn-primary btn-sm" wire:click="ouvrirCorrection({{ $a->id }})"><i class="fa-solid fa-pen-to-square"></i>Corriger</button></div>
      @empty
        <div class="p-4"><x-empty title="Rien à valider" text="Les projets rendus par les étudiants apparaissent ici." /></div>
      @endforelse
    </x-panel>
  @elseif ($onglet === 'auto')
    <div class="u-grid u-1-1 align-items-start">
      <x-panel title="Réglages de l’attribution automatique">
        <form wire:submit="enregistrerAuto" class="stack">
          <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="autoOn" wire:model.live="auto.enabled"><label class="form-check-label fw-sb" for="autoOn">Attribuer automatiquement les premiers projets du catalogue</label></div>
          <p class="small muted mb-0">Le premier projet est envoyé à l’activation du compte. Ensuite, après chaque rendu, le projet suivant est envoyé une fois l’attente écoulée, avec un e-mail à l’étudiant. Un seul projet du parcours est ouvert à la fois.</p>
          <div class="u-grid u-1-1">
            <div><label class="form-label" for="autoCount">Nombre de projets du parcours</label><input class="form-control" type="number" min="1" max="20" id="autoCount" wire:model="auto.count" @disabled(! $auto['enabled'])>@error('auto.count')<div class="small text-danger">{{ $message }}</div>@enderror<div class="small muted">Les premiers projets publiés de chaque formation, dans l’ordre du catalogue.</div></div>
            <div><label class="form-label" for="autoDays">Délai de remise (jours)</label><input class="form-control" type="number" min="1" max="60" id="autoDays" wire:model="auto.days" @disabled(! $auto['enabled'])>@error('auto.days')<div class="small text-danger">{{ $message }}</div>@enderror</div>
          </div>
          <div><label class="form-label" for="autoDelay">Envoi du projet suivant après un rendu</label>
            <select class="form-select" id="autoDelay" wire:model="auto.delay" @disabled(! $auto['enabled'])>
              @foreach ([0, 15, 30, 60, 120, 180, 360, 720, 1440, 2880] as $m)<option value="{{ $m }}">{{ $m ? 'Après '.\App\Services\ProjectService::delayLabel($m) : 'Immédiatement' }}</option>@endforeach
              @if (! in_array((int) $auto['delay'], [0, 15, 30, 60, 120, 180, 360, 720, 1440, 2880], true))<option value="{{ $auto['delay'] }}">Après {{ \App\Services\ProjectService::delayLabel((int) $auto['delay']) }}</option>@endif
            </select>@error('auto.delay')<div class="small text-danger">{{ $message }}</div>@enderror</div>
          <div><button class="btn btn-primary" type="submit" wire:loading.attr="disabled"><i class="fa-solid fa-floppy-disk"></i>Enregistrer</button></div>
        </form>
      </x-panel>
      <x-panel title="Prochains envois" :p0="true">
        @forelse ($plan as $row)
          <div class="row-item"><x-fchip :code="$row['formation']->code" />
            <div class="ri-main"><div class="ri-t">{{ $row['student']->user->name }} <span class="small muted mono">{{ $row['student']->matricule }}</span></div>
              <div class="ri-s">{{ $row['project']->title }} · {{ $row['at']->isFuture() ? 'envoi prévu le '.$row['at']->translatedFormat('j F à H\hi') : 'envoi en cours (prochaine minute)' }}</div></div>
            <button class="btn btn-ghost btn-sm" wire:click="envoyerMaintenant({{ $row['student']->id }}, {{ $row['formation']->id }})" wire:confirm="Envoyer ce projet maintenant ? L’étudiant recevra un e-mail.">Envoyer maintenant</button>
          </div>
        @empty
          <div class="p-4"><x-empty title="Aucun envoi en attente" text="{{ $autoConfig['enabled'] ? 'Les projets suivants apparaîtront ici après les rendus des étudiants.' : 'L’attribution automatique est désactivée.' }}" /></div>
        @endforelse
      </x-panel>
    </div>
  @else
    <x-panel :p0="true">
      <x-slot:tools><a class="btn btn-ghost btn-sm" href="{{ route('admin.projet.nouveau', ['formation' => $formation]) }}" wire:navigate><i class="fa-solid fa-plus"></i>Nouveau projet</a></x-slot:tools>
      <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Projet</th><th>Type</th><th class="text-end">Attribués</th><th class="text-end">Validés</th><th>Statut</th><th></th></tr></thead><tbody>
        @foreach ($catalogue as $p)
          <tr><td><x-fchip :code="$p->formation->code" /> <span class="fw-sb">{{ $p->title }}</span>@if ($autoConfig['enabled'] && $autoRank->has($p->id)) <span class="pill p-info" title="Attribué automatiquement, un projet à la fois : à l’activation du compte puis {{ \App\Services\ProjectService::delayLabel($autoConfig['delay']) }} après chaque rendu. Délai : {{ $autoConfig['days'] }} jours."><i class="fa-solid fa-robot me-1"></i>Auto {{ $autoRank[$p->id] }}/{{ $autoConfig['count'] }}</span>@endif<div class="small muted text-truncate" style="max-width:420px">{{ $p->briefExcerpt() }}</div></td>
            <td class="small">{{ $p->type }}</td><td class="text-end"><button type="button" class="btn btn-ghost btn-sm" wire:click="voirDestinataires({{ $p->id }})" aria-label="Voir les destinataires de {{ $p->title }}">{{ $p->assignments_count }} étudiant(s)<i class="fa-solid fa-users ms-1"></i></button></td><td class="text-end">{{ $p->valides_count }}</td>
            <td><x-pill :tone="$p->published ? 'ok' : 'mute'">{{ $p->published ? 'Publié' : 'Brouillon' }}</x-pill></td>
            <td class="table-actions"><button class="btn btn-ghost btn-sm" wire:click="ouvrirAttribution([], {{ $p->id }})">Attribuer</button><a class="btn btn-quiet btn-sm btn-icon" href="{{ route('admin.projet.modifier', $p) }}" wire:navigate aria-label="Modifier le projet"><i class="fa-solid fa-pen"></i></a></td></tr>
        @endforeach
      </tbody></table></div>
    </x-panel>
  @endif

  @if ($destProject)
    <x-modal title="Étudiants destinataires" icon="fa-users" size="modal-lg">
      <p class="fw-sb">{{ $destProject->title }}</p>
      <input class="form-control mb-3" type="search" wire:model.live.debounce.300ms="rechercheDestinataire" placeholder="Rechercher par nom ou matricule" aria-label="Rechercher un destinataire">
      <p class="small muted">{{ $destinations->total() }} destinataire(s)</p>
      <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Étudiant</th><th>État du projet</th><th>Échéance</th></tr></thead><tbody>
        @forelse ($destinations as $a)
          <tr wire:key="destinataire-{{ $a->id }}"><td>@can('admin.etudiants')<a href="{{ route('admin.etudiant', $a->student) }}" wire:navigate>{{ $a->student->user->name }}</a>@else{{ $a->student->user->name }}@endcan<div class="small muted mono">{{ $a->student->matricule }}</div></td><td><x-pill :tone="$a->tone()">{{ $a->isLate() ? 'En retard' : $a->label(true) }}</x-pill></td><td class="small">{{ $a->due_on->translatedFormat('j M Y') }}</td></tr>
        @empty<tr><td colspan="3" class="muted">Aucun étudiant destinataire{{ $rechercheDestinataire ? ' correspondant à la recherche' : '' }}.</td></tr>@endforelse
      </tbody></table></div>
      {{ $destinations->links() }}
      <x-slot:footer><button type="button" class="btn btn-ghost" wire:click="closeModal">Fermer</button></x-slot:footer>
    </x-modal>
  @endif
  @include('livewire.partials.attribution')
  @include('livewire.partials.correction')
</div>
