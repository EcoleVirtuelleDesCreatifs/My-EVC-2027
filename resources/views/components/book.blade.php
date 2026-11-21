@props(['r', 'size' => '', 'got' => false])
{{-- Un livre de la bibliothèque : couverture, tranche, pages. Taille : '' (étagère), 'lg' (fiche), 'xs' (vignette). --}}
@php([$c, $a] = $r->cover())
@if ($size === 'xs')
  <span class="book bk-xs bk-{{ $r->category }}" style="--c:{{ $c }};--a:{{ $a }}" aria-hidden="true"><i class="fa-solid {{ $r->icon() }}"></i></span>
@else
  @php([$w, $h] = $r->bookSize())
  <span class="book bk-{{ $r->category }} {{ $size ? 'bk-'.$size : '' }}" style="{{ $size ? '' : "--w:{$w}px;--h:{$h}px;" }}--c:{{ $c }};--a:{{ $a }}">
    <span class="bk-band"></span><span class="bk-k">{{ $r->kicker() }}</span>
    @switch($r->category)
      @case('logiciels')<span class="bk-art"><i class="fa-solid fa-laptop-code"></i></span>@break
      @case('polices')<span class="bk-art bk-aa">Aa</span>@break
      @case('tutos')<span class="bk-art bk-play"><i class="fa-solid fa-play"></i></span>@break
      @case('graphiques')<span class="bk-art bk-shapes"><i></i><i></i><i></i></span>@break
    @endswitch
    <span class="bk-t">{{ $r->coverTitle() }}</span>
    <span class="bk-f"><span>{{ $r->formationCode() }}</span><span>{{ $r->isExternal() ? 'Web' : explode(' · ', $r->format)[0] }}</span></span>
    @if ($r->isNew())<span class="bk-new">Nouveau</span>@endif
    @if ($got)<span class="bk-mark" title="Téléchargé"><i class="fa-solid fa-check"></i></span>@endif
  </span>
@endif
