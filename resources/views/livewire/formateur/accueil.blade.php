<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Séances cette semaine" :value="$week" icon="fa-calendar-days" tone="lav" :sub="$formations->pluck('code')->implode(' + ')" />
    <x-kpi label="Projets à corriger" :value="$pending" icon="fa-pen-to-square" :tone="$pending ? 'cor' : 'ok'" :sub="$pending ? 'En attente de votre retour' : 'Tout est corrigé'" />
    <x-kpi label="Étudiants suivis" :value="$students" icon="fa-user-graduate" tone="info" sub="Actifs" />
    <x-kpi label="Assiduité moyenne" :value="$rate.' %'" icon="fa-user-check" :tone="$rate >= 80 ? 'ok' : 'warn'" :sub="$toCall ? $toCall.' appel(s) à faire' : 'Appels à jour'" :sub-tone="$toCall ? 'bad' : null" />
  </div>
  <div class="u-grid u-1-1 align-items-start">
    <x-panel title="Prochaines séances" :p0="true">
      <x-slot:tools><a class="btn btn-quiet btn-sm" href="{{ route('formateur.seances') }}" wire:navigate>Tout voir</a></x-slot:tools>
      @forelse ($next as $x)
        <div class="row-item c-{{ $x->formation->code }}"><div class="dbox"><span>{{ $x->starts_at->translatedFormat('M') }}</span><b>{{ $x->starts_at->day }}</b></div>
          <div class="ri-main"><div class="ri-t">{{ $x->title }}</div><div class="ri-s">{{ jour($x->starts_at) }} · {{ $x->hours() }} · {{ $x->modeLabel() }}</div></div>
          @if ($x->checkInOpen())<a class="btn btn-primary btn-sm" href="{{ route('formateur.appel', $x) }}" wire:navigate><i class="fa-solid fa-list-check"></i>Appel</a>
          @if ($x->meet_url)<a class="btn btn-ghost btn-sm" href="{{ $x->meet_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-video"></i></a>@endif @endif
        </div>
      @empty
        <div class="p-4"><x-empty title="Aucune séance à venir" /></div>
      @endforelse
    </x-panel>
    <x-panel title="À corriger" :p0="true">
      <x-slot:tools><a class="btn btn-quiet btn-sm" href="{{ route('formateur.corrections') }}" wire:navigate>Tout voir</a></x-slot:tools>
      @forelse ($queue as $a)
        <a class="row-item text-decoration-none" href="{{ route('formateur.corrections') }}" wire:navigate><x-fchip :code="$a->project->formation->code" />
          <div class="ri-main"><div class="ri-t">{{ $a->project->title }}</div><div class="ri-s">{{ $a->student->user->name }} · rendu {{ $a->submitted_at?->diffForHumans() }}</div></div><i class="fa-solid fa-chevron-right muted"></i></a>
      @empty
        <div class="p-4"><x-empty title="Rien à corriger" text="Les projets rendus apparaîtront ici." /></div>
      @endforelse
    </x-panel>
  </div>
</div>
