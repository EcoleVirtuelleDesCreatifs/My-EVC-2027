<div>
  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Nom, matricule, e-mail, téléphone…" wire:model.live.debounce.300ms="q"></label>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
    <select class="form-select" wire:model.live="promotion" aria-label="Session"><option value="">Toutes les sessions</option>@foreach ($promotions as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
    <select class="form-select" wire:model.live="statut" aria-label="Statut"><option value="">Tous les statuts</option>@foreach (\App\Models\Student::STATUSES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select>
  </div>
  <div class="panel tbl" wire:loading.class="wire-loading">
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Étudiant</th><th>Formations</th><th>Session</th><th class="text-end">Reste à payer</th><th>Statut</th><th></th></tr></thead><tbody>
      @forelse ($items as $s)
        <tr>
          <td><div class="d-flex align-items-center gap-2"><span class="ini">{{ $s->user->initials() }}</span><div><a class="link-plain" href="{{ route('admin.etudiant', $s) }}" wire:navigate>{{ $s->user->name }}</a><div class="small muted mono">{{ $s->matricule }}</div></div></div></td>
          <td>@foreach ($s->formations as $f)<x-fchip :code="$f->code" /> @endforeach</td>
          <td class="small">{{ $s->promotion?->name ?? '—' }}</td>
          <td class="text-end {{ $s->balance() ? 'fw-sb' : 'muted' }}">{{ $s->balance() ? fcfa($s->balance()) : 'Soldé' }}</td>
          <td><x-pill :tone="['actif' => 'ok', 'suspendu' => 'bad', 'certifie' => 'info', 'abandon' => 'mute'][$s->status]">{{ \App\Models\Student::STATUSES[$s->status] }}</x-pill></td>
          <td class="table-actions"><a class="btn btn-ghost btn-sm" href="{{ route('admin.etudiant', $s) }}" wire:navigate>Fiche</a></td>
        </tr>
      @empty
        <tr><td colspan="6"><x-empty title="Aucun étudiant" text="Les étudiants apparaissent ici après l'activation de leur candidature." /></td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
