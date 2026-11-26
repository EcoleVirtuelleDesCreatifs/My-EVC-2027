<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Portfolios" :value="$totals['portfolios']" icon="fa-swatchbook" tone="cor" sub="étudiants avec au moins un projet validé" />
    <x-kpi label="Projets validés" :value="$totals['projets']" icon="fa-circle-check" tone="ok" sub="dans ces portfolios" />
  </div>

  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Nom ou matricule…" wire:model.live.debounce.300ms="q" aria-label="Rechercher un étudiant"></label>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
    <select class="form-select" wire:model.live="tri" aria-label="Trier"><option value="recents">Validations récentes</option><option value="projets">Plus de projets validés</option><option value="nom">Nom (A → Z)</option></select>
    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" id="pfAvec" wire:model.live="avecProjets"><label class="form-check-label small" for="pfAvec">Avec projets validés</label></div>
  </div>

  @if ($items->isEmpty())
    <x-empty title="Aucun portfolio à afficher" text="Les portfolios se remplissent automatiquement à chaque projet validé par l’équipe pédagogique." />
  @else
    <div class="pfl-grid">
      @foreach ($items as $s)
        @php($code = $s->formations->first()?->code)
        <article class="pfl-card c-{{ $code }}" wire:key="pf-{{ $s->id }}">
          <a class="pfl-cover" href="{{ route('admin.portfolio', $s) }}" wire:navigate aria-label="Ouvrir le portfolio de {{ $s->user->name }}">
            @forelse ($s->pfVisuals as $v)
              <figure style="aspect-ratio:{{ $v['width'] }} / {{ $v['height'] }}"><img src="{{ route('fichier.depot.apercu', [$v['file'], 'w' => 480]) }}" width="{{ $v['width'] }}" height="{{ $v['height'] }}" alt="" loading="lazy" decoding="async"></figure>
            @empty
              <span class="pfl-empty"><i class="fa-solid {{ $s->pf['total'] ? 'fa-file-lines' : 'fa-hourglass-half' }}"></i>{{ $s->pf['total'] ? 'Livrables sans visuel' : 'Aucun projet validé' }}</span>
            @endforelse
          </a>
          <div class="pfl-body">
            <div class="pfl-who">
              <span class="ini pfl-ini">@if ($s->user->avatarUrl())<img src="{{ $s->user->avatarUrl() }}" alt="">@else{{ $s->user->initials() }}@endif</span>
              <div class="min-w-0">
                <h2>{{ $s->user->name }}</h2>
                <div class="small muted"><span class="mono">{{ $s->matricule }}</span>@if ($s->promotion) · {{ $s->promotion->name }}@endif</div>
              </div>
            </div>
            <div class="d-flex gap-1 flex-wrap">@foreach ($s->formations as $f)<x-fchip :code="$f->code" />@endforeach</div>
            <div class="pfl-stats">
              <div><b>{{ $s->pf['total'] }}</b><span>validé{{ $s->pf['total'] > 1 ? 's' : '' }}</span></div>
              <div><b>{{ $s->pf['deliverables'] }}</b><span>livrable{{ $s->pf['deliverables'] > 1 ? 's' : '' }}</span></div>
              <div><b>{{ $s->pf['distinctions'] }}</b><span>distinction{{ $s->pf['distinctions'] > 1 ? 's' : '' }}</span></div>
            </div>
            @if ($s->pfLast)
              <p class="pfl-last"><i class="fa-regular fa-calendar-check"></i><span>Dernier projet validé : <b>{{ $s->pfLast->project->title }}</b> · {{ $s->pfLast->validated_at?->translatedFormat('j M Y') }}</span></p>
            @endif
            <div class="pfl-actions">
              <a class="btn btn-primary btn-sm" href="{{ route('admin.portfolio', $s) }}" wire:navigate><i class="fa-solid fa-swatchbook"></i>Voir le portfolio</a>
              @if ($s->pf['total'])<a class="btn btn-ghost btn-sm" href="{{ route('pdf.portfolio', $s) }}" target="_blank" title="Catalogue PDF" aria-label="Catalogue PDF de {{ $s->user->name }}"><i class="fa-solid fa-file-pdf"></i></a>@endif
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.etudiant', $s) }}" wire:navigate title="Fiche étudiant" aria-label="Fiche étudiant de {{ $s->user->name }}"><i class="fa-solid fa-user-graduate"></i></a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
  @endif
</div>
