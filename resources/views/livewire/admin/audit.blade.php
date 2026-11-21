<div>
  <label class="search-field mb-3"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Action, objet, utilisateur…" wire:model.live.debounce.300ms="q"></label>
  <div class="panel tbl" wire:loading.class="wire-loading"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Objet</th><th>IP</th></tr></thead><tbody>
    @forelse ($items as $l)
      <tr><td class="small text-nowrap">{{ $l->created_at->translatedFormat('j M Y H\hi') }}</td><td class="small">{{ $l->user?->name ?? 'Système' }}</td><td class="fw-sb small">{{ $l->action }}</td><td class="small">{{ $l->subject }}</td><td class="small mono muted">{{ $l->ip }}</td></tr>
    @empty
      <tr><td colspan="5"><x-empty title="Aucune entrée" /></td></tr>
    @endforelse
  </tbody></table></div></div>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
