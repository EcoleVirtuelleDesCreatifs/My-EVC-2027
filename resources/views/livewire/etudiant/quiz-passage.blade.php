<div style="max-width:880px">
  @if ($mode === 'passage')
    @php($qs = $q->questions)
    @php($cur = $qs[$index] ?? $qs->first())
    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
      <div class="flex-grow-1"><div class="small muted mb-1">Question {{ $index + 1 }} sur {{ $qs->count() }} · {{ count($answers) }} répondue(s)</div><x-bar :pct="count($answers) / max(1, $qs->count()) * 100" /></div>
      <div class="qz-timer" wire:ignore x-data="{ left: {{ $a->secondsLeft() }}, t: null, fmt(s) { return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0') } }"
        x-init="t = setInterval(() => { left = Math.max(0, left - 1); if (left === 0) { clearInterval(t); $wire.soumettre() } }, 1000)" x-bind:class="left < 60 && 'low'"><i class="fa-regular fa-clock me-1"></i><span x-text="fmt(left)"></span></div>
    </div>
    <x-panel>
      <h2 class="h5 mb-3" style="white-space:pre-line">{{ $cur->text }}</h2>
      @foreach ($cur->options as $i => $opt)
        <label class="qz-opt {{ ($answers[$cur->id] ?? null) === $i ? 'on' : '' }}" wire:key="o-{{ $cur->id }}-{{ $i }}">
          <input type="radio" class="form-check-input" name="q{{ $cur->id }}" wire:click="repondre({{ $cur->id }}, {{ $i }})" @checked(($answers[$cur->id] ?? null) === $i)>
          <span><b class="me-2">{{ chr(65 + $i) }}.</b>{{ $opt }}</span>
        </label>
      @endforeach
      <div class="d-flex gap-2 mt-3 flex-wrap">
        <button class="btn btn-ghost" wire:click="aller({{ $index - 1 }})" @disabled($index === 0)><i class="fa-solid fa-arrow-left"></i>Précédente</button>
        @if ($index < $qs->count() - 1)
          <button class="btn btn-primary ms-auto" wire:click="aller({{ $index + 1 }})">Suivante<i class="fa-solid fa-arrow-right ms-1"></i></button>
        @else
          <button class="btn btn-primary ms-auto" wire:click="soumettre" wire:confirm="{{ count($answers) < $qs->count() ? 'Il reste '.($qs->count() - count($answers)).' question(s) sans réponse. Terminer quand même ?' : 'Terminer et voir le résultat ?' }}"><i class="fa-solid fa-flag-checkered"></i>Terminer le quiz</button>
        @endif
      </div>
    </x-panel>
    <div class="qz-nav mt-3">@foreach ($qs as $i => $qq)<button type="button" class="{{ isset($answers[$qq->id]) ? 'ok' : '' }} {{ $i === $index ? 'on' : '' }}" wire:click="aller({{ $i }})" aria-label="Question {{ $i + 1 }}">{{ $i + 1 }}</button>@endforeach</div>
  @elseif ($mode === 'resultat')
    @php($passed = $last->percent >= $q->pass_percent)
    <section class="panel mb-4"><div class="panel-b d-flex align-items-center gap-4 flex-wrap">
      <div class="reco-day"><b>{{ $last->percent }} %</b><span>{{ $last->score }} / {{ $last->total }} bonnes réponses</span></div>
      <x-pill :tone="$passed ? 'ok' : 'bad'">{{ $passed ? 'Réussi' : 'Non atteint (réussite à '.$q->pass_percent.' %)' }}</x-pill>
      @if ($state[2])<button class="btn btn-primary ms-auto" wire:click="commencer"><i class="fa-solid fa-rotate"></i>Repasser le quiz</button>@endif
    </div></section>
    @foreach ($q->questions as $n => $qq)
      @php($given = $last->answers[$qq->id] ?? null)
      <x-panel class="mb-3">
        <div class="d-flex gap-2 mb-2"><span class="small-caps">Question {{ $n + 1 }}</span>@if ($given === $qq->answer)<x-pill tone="ok">Juste</x-pill>@elseif ($given === null)<x-pill>Sans réponse</x-pill>@else<x-pill tone="bad">Fausse</x-pill>@endif</div>
        <p class="fw-sb" style="white-space:pre-line">{{ $qq->text }}</p>
        @foreach ($qq->options as $i => $opt)
          <div class="qz-opt {{ $i === $qq->answer ? 'good' : ($i === $given ? 'bad' : '') }}"><b>{{ chr(65 + $i) }}.</b> {{ $opt }} @if ($i === $qq->answer)<i class="fa-solid fa-check ms-auto text-success"></i>@endif</div>
        @endforeach
        @if ($qq->explanation)<p class="small muted mb-0"><i class="fa-regular fa-lightbulb me-1"></i>{{ $qq->explanation }}</p>@endif
      </x-panel>
    @endforeach
  @else
    <x-panel :title="$q->title">
      <x-slot:tools><x-fchip :code="$q->formation->code" /></x-slot:tools>
      @if ($q->description)<p style="white-space:pre-line">{{ $q->description }}</p>@endif
      <div class="lib-dl mb-3">
        <div class="lib-dl-row"><span>Questions</span><b>{{ $q->questions->count() }}</b></div>
        <div class="lib-dl-row"><span>Durée</span><b>{{ $q->duration_min }} minutes</b></div>
        <div class="lib-dl-row"><span>Réussite</span><b>{{ $q->pass_percent }} % de bonnes réponses</b></div>
        <div class="lib-dl-row"><span>Tentatives</span><b>{{ $q->attempts->whereNotNull('submitted_at')->count() }} / {{ $q->attempts_allowed }}</b></div>
        @if ($q->required)<div class="lib-dl-row"><span>Certificat</span><b>Quiz obligatoire</b></div>@endif
      </div>
      <p class="small muted">Le chronomètre démarre dès que vous commencez. Vos réponses sont enregistrées au fur et à mesure ; à la fin du temps, le quiz est remis automatiquement.</p>
      @if ($state[2])<button class="btn btn-primary btn-lg" wire:click="commencer"><i class="fa-solid fa-play"></i>Commencer</button>
      @else<x-pill :tone="$state[1]">{{ $state[0] }}</x-pill>@endif
    </x-panel>
  @endif
</div>
