<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Projets en cours" :value="$all->where('status', 'en_cours')->count()" icon="fa-hourglass-half" tone="info" :sub="$inactifs ? $inactifs.' sans activité depuis 7 jours' : 'Tous actifs cette semaine'" :sub-tone="$inactifs ? 'bad' : null" />
    <x-kpi label="Rapports à évaluer" :value="$all->where('status', 'rendu')->count()" icon="fa-file-circle-check" :tone="$all->where('status', 'rendu')->count() ? 'cor' : 'ok'" />
    <x-kpi label="Évalués" :value="$all->where('status', 'evalue')->count()" icon="fa-award" tone="ok" />
    <x-kpi label="Sans projet" :value="$sansProjet->count()" icon="fa-user-clock" :tone="$sansProjet->count() ? 'warn' : 'ok'" sub="Étudiants n'ayant pas lancé leur RECO" />
  </div>
  <div class="filters-row mb-3">
    <div class="seg seg-soft"><button type="button" class="{{ $statut === '' ? 'on' : '' }}" wire:click="$set('statut','')">Tous</button>@foreach (\App\Models\RecoProject::STATUSES as $k => [$l])<button type="button" class="{{ $statut === $k ? 'on' : '' }}" wire:click="$set('statut','{{ $k }}')">{{ $l }}</button>@endforeach</div>
    @if ($formations->count() > 1)<select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>@endif
  </div>
  <div class="panel tbl"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Étudiant</th><th>Projet</th><th>Avancement</th><th class="text-end">Activités</th><th>Dernière activité</th><th>Statut</th><th></th></tr></thead><tbody>
    @forelse ($items as $p)
      <tr><td><b>{{ $p->student->user->name }}</b><div class="small muted mono">{{ $p->student->matricule }}</div></td>
        <td><x-fchip :code="$p->formation->code" /> {{ $p->name }}</td>
        <td style="min-width:150px"><div class="small muted">Jour {{ $p->day() }} / 60 · {{ $p->stepsDone() }}/{{ count($p->steps) }} étapes</div><x-bar :pct="$p->day() / 60 * 100" tone="cor" /></td>
        <td class="text-end">{{ $p->entries_count }}</td>
        <td class="small">{{ $p->entries_max_entry_date ? \Illuminate\Support\Carbon::parse($p->entries_max_entry_date)->diffForHumans() : 'Aucune' }}</td>
        <td><x-pill :tone="$p->statusTone()">{{ $p->statusLabel() }}</x-pill></td>
        <td class="table-actions"><a class="btn btn-sm {{ $p->status === 'rendu' ? 'btn-primary' : 'btn-ghost' }}" href="{{ gestion_route('reco.fiche', $p) }}" wire:navigate>{{ $p->status === 'rendu' ? 'Évaluer' : 'Ouvrir' }}</a></td></tr>
    @empty
      <tr><td colspan="7"><x-empty title="Aucun RECO 60" text="Les projets apparaissent ici dès qu'un étudiant lance son RECO 60." /></td></tr>
    @endforelse
  </tbody></table></div></div>
  @if ($sansProjet->isNotEmpty())
    <x-panel title="Étudiants sans RECO 60" :p0="true" class="mt-4">
      @foreach ($sansProjet as $s)
        <div class="row-item"><span class="ini">{{ $s->user->initials() }}</span><div class="ri-main"><div class="ri-t">{{ $s->user->name }}</div><div class="ri-s">{{ $s->formations->where('has_reco', true)->pluck('code')->implode(', ') }}</div></div>
          <a class="btn btn-ghost btn-sm" href="{{ route('myevc.messagerie') }}" wire:navigate><i class="fa-solid fa-message"></i>Relancer</a></div>
      @endforeach
    </x-panel>
  @endif
</div>
