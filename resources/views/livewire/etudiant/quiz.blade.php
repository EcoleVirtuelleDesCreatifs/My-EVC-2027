<div>
  @include('livewire.etudiant._fsel')
  <div class="stat-grid mb-4">
    <x-kpi label="À faire" :value="$todo" icon="fa-list-check" :tone="$todo ? 'cor' : 'ok'" />
    <x-kpi label="Réussis" :value="$passed.' / '.$quizzes->count()" icon="fa-circle-check" tone="ok" />
    <x-kpi label="Moyenne" :value="$avg !== null ? $avg.' %' : '—'" icon="fa-chart-simple" tone="lav" />
    <x-kpi label="Obligatoires" :value="$quizzes->filter(fn ($x) => $x['q']->required)->count()" icon="fa-award" tone="info" sub="Comptent pour le certificat" />
  </div>
  <x-panel :p0="true">
    @forelse ($quizzes as ['q' => $q, 'state' => [$label, $tone, $can, $best]])
      @php($used = $q->attempts->whereNotNull('submitted_at')->count())
      <div class="row-item c-{{ $q->formation->code }}">
        <span class="icon-sq"><i class="fa-solid fa-list-check"></i></span>
        <div class="ri-main"><div class="ri-t">{{ $q->title }} @if ($q->required)<x-pill tone="info" nodot>Obligatoire</x-pill>@endif</div>
          <div class="ri-s"><x-fchip :code="$q->formation->code" /> {{ $q->questions_count }} questions · {{ $q->duration_min }} min · réussite à {{ $q->pass_percent }} % · {{ $used }}/{{ $q->attempts_allowed }} tentative{{ $q->attempts_allowed > 1 ? 's' : '' }}@if ($q->closes_at) · jusqu'au {{ $q->closes_at->translatedFormat('j M à H\hi') }}@endif</div></div>
        <x-pill :tone="$tone" :class="$tone === 'mute' && $q->opens_at?->isFuture() ? 'text-white' : ''">{{ $label }}</x-pill>
        @if ($can)<a class="btn btn-primary btn-sm" href="{{ route('etudiant.quiz.passer', $q) }}" wire:navigate>{{ $best ? 'Repasser' : 'Commencer' }}</a>
        @elseif ($best)<a class="btn btn-ghost btn-sm" href="{{ route('etudiant.quiz.passer', $q) }}" wire:navigate>Voir la correction</a>@endif
      </div>
    @empty
      <div class="p-4"><x-empty title="Aucun quiz pour le moment" text="Vos formateurs publient un quiz à la fin de chaque module." /></div>
    @endforelse
  </x-panel>
</div>
