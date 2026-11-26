<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Étudiants actifs" :value="$actifs" icon="fa-user-graduate" tone="lav" />
    @can('admin.candidatures')<x-kpi label="Candidatures à traiter" :value="$aTraiter" icon="fa-user-plus" :tone="$aTraiter ? 'cor' : 'ok'" sub="Dossiers reçus et 1ers versements" />@endcan
    @can('admin.finances')<x-kpi label="Solde de caisse" :value="fcfa($caisse['balance'])" icon="fa-cash-register" :tone="$caisse['balance'] >= 0 ? 'ok' : 'bad'" :sub="'Ce mois : +'.fcfa($caisse['periodIn']).' / −'.fcfa($caisse['periodOut']).' · reste à encaisser '.fcfa($caisse['due'])" />@endcan
    @can('admin.projets')<x-kpi label="Projets à valider" :value="$aValider" icon="fa-pen-to-square" :tone="$aValider ? 'warn' : 'ok'" sub="Rendus par les étudiants" />@endcan
  </div>

  @if ($etats)
    <section class="panel dispo-band mb-4">
      <div class="db-h"><div><h2>Projets : qui est libre ?</h2><p class="small muted mb-0">{{ $etats['total'] }} étudiants actifs</p></div>
        <div class="ms-auto d-flex gap-2"><a class="btn btn-ghost btn-sm" href="{{ route('admin.disponibilite') }}" wire:navigate>Voir le détail</a><a class="btn btn-primary btn-sm" href="{{ route('admin.disponibilite', ['etat' => 'libre']) }}" wire:navigate><i class="fa-solid fa-paper-plane"></i>Donner un projet</a></div></div>
      @include('livewire.admin._dispo-stack', ['e' => $etats])
      <div class="db-legend">
        @foreach (['libre' => 'Libres', 'recu' => 'Projet reçu', 'encours' => 'En cours'] as $k => $l)<a class="db-item text-decoration-none" href="{{ route('admin.disponibilite', ['etat' => $k]) }}" wire:navigate><i class="ds-{{ $k }}"></i><b>{{ $etats[$k] }}</b><span>{{ $l }}</span></a>@endforeach
        @if ($etats['retard'])<a class="db-item text-decoration-none" href="{{ route('admin.disponibilite', ['etat' => 'retard']) }}" wire:navigate><i class="ds-retard"></i><b>{{ $etats['retard'] }}</b><span>En retard</span></a>@endif
      </div>
    </section>
  @endif

  <div class="u-grid u-1-1 align-items-start">
    @can('admin.candidatures')
      <x-panel title="Dernières préinscriptions" :p0="true">
        <x-slot:tools><a class="btn btn-quiet btn-sm" href="{{ route('admin.candidatures') }}" wire:navigate>Tout voir</a></x-slot:tools>
        @forelse ($candidatures as $c)
          <a class="row-item text-decoration-none" href="{{ route('admin.candidature', $c) }}" wire:navigate><span class="ini">{{ mb_substr($c->first_name, 0, 1).mb_substr($c->last_name, 0, 1) }}</span>
            <div class="ri-main"><div class="ri-t">{{ $c->full_name }}</div><div class="ri-s">{{ implode(' + ', $c->formations) }} · {{ $c->created_at->diffForHumans() }}</div></div><x-pill :tone="$c->stageTone()">{{ $c->stageLabel() }}</x-pill></a>
        @empty
          <div class="p-4"><x-empty title="Aucune préinscription" /></div>
        @endforelse
      </x-panel>
    @endcan
    <div class="stack">
      <x-panel title="Séances du jour" :p0="true">
        @forelse ($today as $x)
          <div class="row-item c-{{ $x->formation->code }}"><x-fchip :code="$x->formation->code" /><div class="ri-main"><div class="ri-t">{{ $x->title }}</div><div class="ri-s">{{ $x->hours() }} · {{ $x->trainer?->name }} · {{ $x->modeLabel() }}</div></div>@if ($x->isLive())<x-pill tone="acc">En cours</x-pill>@endif</div>
        @empty
          <div class="p-4 small muted">Aucune séance aujourd'hui.</div>
        @endforelse
      </x-panel>
      @if ($retards->isNotEmpty())
        <x-panel title="Paiements en retard" :p0="true">
          <x-slot:tools><a class="btn btn-quiet btn-sm" href="{{ route('admin.finances', ['vue' => 'retards']) }}" wire:navigate>Relancer</a></x-slot:tools>
          @foreach ($retards as $r)
            <a class="row-item text-decoration-none" href="{{ route('admin.etudiant', $r['s']) }}" wire:navigate><span class="ini">{{ $r['s']->user->initials() }}</span><div class="ri-main"><div class="ri-t">{{ $r['s']->user->name }}</div><div class="ri-s mono">{{ $r['s']->matricule }}</div></div><b class="text-danger small">{{ fcfa($r['late']) }}</b></a>
          @endforeach
        </x-panel>
      @endif
    </div>
  </div>
</div>
