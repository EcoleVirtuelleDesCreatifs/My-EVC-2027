<div>
  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Nom, référence, e-mail, téléphone…" wire:model.live.debounce.300ms="q"></label>
  </div>
  <div class="seg seg-soft mb-3 flex-wrap">
    <button type="button" class="{{ $etape === 'actives' ? 'on' : '' }}" wire:click="$set('etape','actives')">À traiter <span class="cnt">{{ $counts->except(['activee', 'refusee'])->sum() }}</span></button>
    @foreach (\App\Models\Candidature::STAGES as $k => [$l])
      <button type="button" class="{{ $etape === $k ? 'on' : '' }}" wire:click="$set('etape','{{ $k }}')">{{ $l }} <span class="cnt">{{ $counts[$k] ?? 0 }}</span></button>
    @endforeach
    <button type="button" class="{{ $etape === 'toutes' ? 'on' : '' }}" wire:click="$set('etape','toutes')">Toutes</button>
  </div>
  <div class="panel tbl" wire:loading.class="wire-loading">
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Candidat</th><th>Référence</th><th>Formations</th><th>Modalité</th><th>Provenance</th><th>Reçue</th><th>Étape</th><th></th></tr></thead><tbody>
      @forelse ($items as $c)
        <tr>
          <td><div class="fw-sb">{{ $c->full_name }}</div><div class="small muted">{{ $c->email }} · {{ $c->phone }}</div></td>
          <td class="mono small">{{ $c->reference }}</td>
          <td>@foreach ($c->formations as $code)<x-fchip :code="$code" /> @endforeach</td>
          <td class="small">{{ $c->mode ?? '—' }}</td>
          <td class="small">{{ $c->attributionLabel() }}</td>
          <td class="small">{{ $c->created_at->diffForHumans() }}</td>
          <td><x-pill :tone="$c->stageTone()">{{ $c->stageLabel() }}</x-pill></td>
          <td class="table-actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.candidature', $c) }}" wire:navigate>Ouvrir</a></td>
        </tr>
      @empty
        <tr><td colspan="8"><x-empty title="Aucune candidature" /></td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
