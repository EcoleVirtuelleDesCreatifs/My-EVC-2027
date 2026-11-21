@php($E = \App\Services\AvailabilityService::ETATS)
<div>
  <div class="dispo-tiles">
    @foreach ([['libre', $n['libre'], 'Libres', 'Ont terminé leur projet', 'ok', 'fa-circle-check'], ['recu', $n['recu'], 'Projet reçu', 'Pas encore démarré', 'warn', 'fa-envelope-open-text'], ['encours', $n['encours'], 'En cours', 'Travaillent sur un projet', 'info', 'fa-pen-nib'], ['retard', $n['retard'], 'En retard', $n['retard'] ? 'Échéance dépassée' : 'Aucun retard', 'bad', 'fa-clock']] as [$k, $v, $l, $sub, $tone, $ic])
      <button type="button" class="dispo-tile dt-{{ $tone }} {{ $etat === $k ? 'on' : '' }}" wire:click="$set('etat', '{{ $etat === $k ? 'tous' : $k }}')" aria-pressed="{{ $etat === $k ? 'true' : 'false' }}">
        <span class="dt-i"><i class="fa-solid {{ $ic }}"></i></span><span class="dt-v">{{ $v }}</span><span class="dt-l">{{ $l }}</span><span class="dt-s">{{ $sub }}</span></button>
    @endforeach
  </div>

  <div class="dispo-legend panel card-pad mt-4">
    <div class="d-flex align-items-center flex-wrap gap-3 mb-3"><h2 class="h6 mb-0">Répartition par formation</h2>
      <span class="legend ms-auto"><span><i class="ds-libre"></i>Libre</span><span><i class="ds-recu"></i>Projet reçu</span><span><i class="ds-encours"></i>En cours</span></span></div>
    <div class="dispo-frows">
      @foreach ($parFormation as $pf)
        @php($e = ['libre' => $pf['rows']->where('etat', 'libre')->count(), 'recu' => $pf['rows']->where('etat', 'recu')->count(), 'encours' => $pf['rows']->where('etat', 'encours')->count()])
        <div class="dispo-frow"><div class="d-flex align-items-center gap-2 mb-2"><x-fchip :code="$pf['f']->code" /><span class="small fw-sb">{{ $pf['f']->short_name }}</span><span class="small muted ms-auto">{{ $e['libre'] }} libre{{ $e['libre'] > 1 ? 's' : '' }} sur {{ $pf['rows']->count() }}</span></div>
          @include('livewire.admin._dispo-stack', ['e' => $e])</div>
      @endforeach
    </div>
  </div>

  <div class="filters-row mt-4 mb-3">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Nom ou matricule…" wire:model.live.debounce.300ms="q"></label>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
    @if ($selection)<button class="btn btn-primary" wire:click="ouvrirAttribution(@js($selection))"><i class="fa-solid fa-paper-plane"></i>Attribuer à {{ count($selection) }}</button>@endif
  </div>

  <div class="panel tbl" wire:loading.class="wire-loading">
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th style="width:36px"></th><th>Étudiant</th><th>État</th><th>Projet actuel</th><th>Parcours</th><th></th></tr></thead><tbody>
      @forelse ($rows as $r)
        @php($s = $r['s'])
        <tr>
          <td><input type="checkbox" class="form-check-input" value="{{ $s->id }}" wire:model.live="selection" aria-label="Sélectionner {{ $s->user->name }}"></td>
          <td><a class="link-plain" href="{{ route('admin.etudiant', $s) }}" wire:navigate>{{ $s->user->name }}</a><div class="small muted">@foreach ($s->formations as $f)<x-fchip :code="$f->code" /> @endforeach</div></td>
          <td><x-pill :tone="$E[$r['etat']][1]">{{ $E[$r['etat']][0] }}</x-pill>@if ($r['late'])<x-pill tone="bad" class="ms-1">{{ $r['late'] }} en retard</x-pill>@endif
            @if ($r['libreDepuis'] !== null)<div class="small muted">depuis {{ $r['libreDepuis'] }} j</div>@endif</td>
          <td class="small">@if ($r['current'])<span class="fw-sb">{{ $r['current']->project->title }}</span><div class="muted">à rendre le {{ $r['current']->due_on->translatedFormat('j M') }}</div>@else<span class="muted">—</span>@endif</td>
          <td class="small">{{ $r['valides'] }} / {{ $r['total'] }} validés @if ($r['aValider'])<div class="muted">{{ $r['aValider'] }} à valider</div>@endif</td>
          <td class="table-actions">@if ($r['etat'] === 'libre')<button class="btn btn-primary btn-sm" wire:click="ouvrirAttribution([{{ $s->id }}])">Attribuer</button>
            @else<a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', (string) $s->user->phone) }}?text={{ rawurlencode('Bonjour '.$s->firstName().', où en est votre projet « '.($r['current']?->project->title ?? '').' » ?') }}"><i class="fa-brands fa-whatsapp"></i>Relancer</a>@endif</td>
        </tr>
      @empty
        <tr><td colspan="6"><x-empty title="Aucun étudiant pour ce filtre" /></td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  @include('livewire.partials.attribution')
</div>
