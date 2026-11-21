<div class="u-grid u-2-1 align-items-start">
  <div class="stack">
    <section class="panel"><div class="panel-b d-flex flex-wrap align-items-center gap-4">
      <div class="reco-day"><b>Jour {{ $p->day() }}</b><span>/ 60</span></div>
      <div class="flex-grow-1" style="min-width:200px"><div class="small muted mb-1">{{ $p->student->user->name }} · <x-fchip :code="$p->formation->code" /> · début le {{ $p->started_on->translatedFormat('j F') }}</div><x-bar :pct="$p->day() / 60 * 100" tone="cor" /></div>
      <x-pill :tone="$p->statusTone()">{{ $p->statusLabel() }}</x-pill>
    </div></section>
    @if ($p->kpis)<x-panel title="Indicateurs"><div class="kpi-mini">@foreach ($p->kpis as $k)<div><b>{{ $k['v'] }}</b><span>{{ $k['l'] }}</span></div>@endforeach<div><b>{{ rtrim(rtrim(number_format($p->entries->sum('hours'), 1, ',', ''), '0'), ',') }} h</b><span>Heures déclarées</span></div></div></x-panel>@endif
    <x-panel title="Journal d'activités" :sub="$p->entries->count().' activité(s)'">
      @forelse ($p->entries as $en)
        <div class="entry">
          <div class="small muted">{{ ucfirst($en->entry_date->translatedFormat('l j F')) }} · {{ rtrim(rtrim(number_format($en->hours, 1, ',', ''), '0'), ',') }} h</div>
          <b>{{ $en->action }}</b>
          @if ($en->result)<div class="small" style="white-space:pre-line">{{ $en->result }}</div>@endif
          <div class="d-flex flex-wrap gap-2 mt-1">
            @if ($en->file_path)<a class="file-chip" href="{{ route('fichier.reco', $en) }}"><i class="fa-solid fa-paperclip"></i>{{ $en->file_name }}</a>@endif
            @if ($en->link)<a class="file-chip" href="{{ $en->link }}" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i>{{ \Illuminate\Support\Str::limit($en->link, 40) }}</a>@endif
          </div>
          @if ($en->feedback)<div class="fb"><b>{{ $en->feedbackAuthor?->name }}</b> : {{ $en->feedback }}</div>
          @else<div class="d-flex gap-2 mt-2"><input class="form-control form-control-sm" placeholder="Votre retour sur cette activité…" wire:model="retours.{{ $en->id }}" wire:keydown.enter="commenter({{ $en->id }})"><button class="btn btn-ghost btn-sm" wire:click="commenter({{ $en->id }})">Envoyer</button></div>@endif
        </div>
      @empty
        <p class="small muted mb-0">Aucune activité déclarée pour le moment.</p>
      @endforelse
    </x-panel>
  </div>
  <div class="stack">
    <x-panel title="Étapes" :sub="$p->stepsDone().' / '.count($p->steps)">
      <ol class="reco-steps">@foreach ($p->steps as $i => $st)<li class="{{ ! empty($st['done']) ? 'done' : '' }}"><span class="rs-n">@if (! empty($st['done']))<i class="fa-solid fa-check"></i>@else{{ $i + 1 }}@endif</span><div><b class="small">{{ $st['t'] }}</b><div class="small muted">{{ $st['d'] }}</div></div></li>@endforeach</ol>
    </x-panel>
    <x-panel title="Fiche du projet">
      <div class="lib-dl">@if ($p->sector)<div class="lib-dl-row"><span>Secteur</span><b>{{ $p->sector }}</b></div>@endif @if ($p->channels)<div class="lib-dl-row"><span>Canaux / outils</span><b>{{ $p->channels }}</b></div>@endif</div>
      <p class="small mt-3 mb-1" style="white-space:pre-line">{{ $p->description }}</p>
      @if ($p->objectives)<p class="small muted mb-0" style="white-space:pre-line"><b>Objectifs :</b> {{ $p->objectives }}</p>@endif
    </x-panel>
    <x-panel title="Rapport final et évaluation">
      @if ($p->report_path)<a class="file-chip mb-3" href="{{ route('fichier.reco.rapport', $p) }}"><i class="fa-solid fa-file-arrow-down"></i>{{ $p->report_name }}</a>@else<p class="small muted">Rapport final pas encore rendu.</p>@endif
      @if ($p->status === 'evalue')
        <div class="alert-strip as-ok"><i class="fa-solid fa-award"></i><span><b>{{ $p->appreciation }}</b> — {{ $p->evaluator?->name }}, le {{ $p->evaluated_at?->translatedFormat('j F Y') }}<br>{{ $p->evaluation }}</span></div>
      @elseif ($p->status === 'rendu')
        <div class="d-flex flex-column gap-2">
          <select class="form-select" wire:model="appreciation">@foreach (\App\Models\RecoProject::APPRECIATIONS as $a)<option>{{ $a }}</option>@endforeach</select>
          <textarea class="form-control @error('evaluation') is-invalid @enderror" rows="4" wire:model="evaluation" placeholder="Résultats obtenus, méthode, points forts et axes de progrès…"></textarea>
          @error('evaluation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
          <button class="btn btn-primary" wire:click="evaluer"><i class="fa-solid fa-award"></i>Enregistrer l'évaluation</button>
          <button class="btn btn-ghost btn-sm" wire:click="rouvrir" wire:confirm="Rouvrir le RECO 60 pour que l'étudiant le complète ?">Demander des compléments</button>
        </div>
      @endif
    </x-panel>
  </div>
</div>
