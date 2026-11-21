@php($p = $prog->get($v->id))
<a class="tv-card" href="{{ route('etudiant.video', $v) }}" wire:navigate>
  <span class="tv-thumb f-{{ $v->formation->code }}"><span class="tv-mod">{{ $v->module }}</span><span class="tv-play"><i class="fa-solid fa-play"></i></span>
    @if ($v->duration_min)<span class="tv-dur">{{ $v->durationLabel() }}</span>@endif
    @if ($p?->completed_at)<span class="tv-done" title="Vue"><i class="fa-solid fa-check"></i></span>@endif
    @if ($p && ! $p->completed_at && $p->percent)<span class="tv-prog"><span style="width:{{ $p->percent }}%"></span></span>@endif</span>
  <span class="tv-body"><b>{{ $v->title }}</b><small><x-fchip :code="$v->formation->code" /> {{ $v->seance ? 'Séance du '.$v->seance->starts_at->translatedFormat('j M') : $v->created_at->translatedFormat('j M Y') }}</small></span>
</a>
