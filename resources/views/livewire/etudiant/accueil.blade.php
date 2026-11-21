<div class="e-wrap">
  <p class="e-hello">Bonjour {{ $s->firstName() }}</p>

  <section class="e-now t-{{ $action['tone'] }}">
    <div class="en-k">@if ($action['tone'] === 'live')<span class="live-dot"></span>@endif{{ $action['k'] }}</div>
    <h2 class="en-t">{{ $action['t'] }}</h2>
    <p class="en-s">{{ $action['s'] }}</p>
    <div class="en-b">
      @php($b = $action['btn'])
      @if (isset($b['wire']))
        <button class="btn btn-primary btn-lg" wire:click="{{ $b['wire'] }}" wire:loading.attr="disabled"><i class="fa-solid {{ $b['ic'] }}"></i>{{ $b['l'] }}</button>
      @elseif (isset($b['url']))
        <a class="btn btn-primary btn-lg" href="{{ $b['url'] }}" target="_blank" rel="noopener"><i class="fa-solid {{ $b['ic'] }}"></i>{{ $b['l'] }}</a>
      @else
        <a class="btn btn-primary btn-lg" href="{{ $b['route'] }}" wire:navigate><i class="fa-solid {{ $b['ic'] }}"></i>{{ $b['l'] }}</a>
      @endif
    </div>
  </section>

  <div class="e-cards">
    @php($s0 = $next->first())
    <a class="e-card" href="{{ route('etudiant.cours') }}" wire:navigate><span class="ec-i"><i class="fa-solid fa-calendar-days"></i></span><span class="ec-k">Prochain cours</span>
      <span class="ec-t">{{ $s0?->title ?? 'Aucun cours prévu' }}</span>
      <span class="ec-s">{{ $s0 ? jour($s0->starts_at).' · '.$s0->hours().' · '.($s0->mode === 'virtuel' ? 'en ligne' : 'en salle') : 'Le planning sera publié bientôt' }}</span></a>
    @php($p0 = $open->first())
    <a class="e-card" href="{{ $p0 ? route('etudiant.projet', $p0) : route('etudiant.projets') }}" wire:navigate><span class="ec-i"><i class="fa-solid fa-pen-nib"></i></span><span class="ec-k">Mon projet</span>
      <span class="ec-t">{{ $p0?->project->title ?? 'Aucun projet à rendre' }}</span>
      <span class="ec-s">{{ $p0 ? $p0->label().' · '.mb_strtolower($p0->dueText()) : $validated.' projet'.($validated > 1 ? 's' : '').' validé'.($validated > 1 ? 's' : '') }}</span>
      @if ($open->count() > 1)<span class="ec-more">+ {{ $open->count() - 1 }} autre{{ $open->count() > 2 ? 's' : '' }}</span>@endif</a>
    @php($bal = $s->balance())
    <a class="e-card" href="{{ route('etudiant.paiements') }}" wire:navigate><span class="ec-i"><i class="fa-solid fa-credit-card"></i></span><span class="ec-k">Paiements</span>
      <span class="ec-t">{{ $bal ? fcfa($bal).' restant' : 'Formation soldée' }}</span>
      <span class="ec-s">{{ $nextPay ? 'Prochaine échéance : '.$nextPay->due_on->translatedFormat('j F') : 'Merci, tout est réglé' }}</span>
      <span class="ec-bar"><x-bar :pct="$s->totalDue() ? $s->totalPaid() / $s->totalDue() * 100 : 100" tone="ok" /></span></a>
  </div>

  <section class="e-progress">
    <div class="ep-h"><span>Ma progression</span><b>{{ $progress->count() ? round($progress->avg('pct')) : 0 }} %</b></div>
    @foreach ($progress as $p)
      <div class="ep-row"><span class="ep-f"><x-fchip :code="$p['f']->code" /><span class="d-none d-sm-inline">{{ $p['f']->short_name }}</span></span><span class="ep-bar"><x-bar :pct="$p['pct']" :tone="'c-'.$p['f']->code" /></span><span class="ep-n">{{ $p['pct'] }} %</span></div>
    @endforeach
    <div class="ep-foot"><span><i class="fa-solid fa-check"></i>{{ $validated }} / {{ $total }} projets validés</span><span><i class="fa-solid fa-user-check"></i>Assiduité {{ $s->attendanceRate() }} %</span>
      @if ($s->promotion)<span><i class="fa-regular fa-hourglass-half"></i>Fin de formation le {{ $s->promotion->ends_on->translatedFormat('j F Y') }}</span>@endif</div>
  </section>
</div>
