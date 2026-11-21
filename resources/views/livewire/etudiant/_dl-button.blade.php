@php($label = $r->isExternal() ? 'Site officiel' : ($r->isVideo() ? 'Regarder' : 'Télécharger'))
@php($icon = $r->isExternal() ? 'fa-arrow-up-right-from-square' : ($r->isVideo() ? 'fa-play' : 'fa-download'))
@if ($compact ?? false)
  <a class="btn btn-quiet btn-sm btn-icon" href="{{ route('etudiant.ressource', $r) }}" @if ($r->isExternal()) target="_blank" rel="noopener" @endif x-on:click="setTimeout(() => $wire.$refresh(), 900)" title="{{ $label }}" aria-label="{{ $label }} : {{ $r->title }}"><i class="fa-solid {{ $icon }}"></i></a>
@else
  <a class="btn btn-primary {{ $cls ?? 'btn-sm' }}" href="{{ route('etudiant.ressource', $r) }}" @if ($r->isExternal()) target="_blank" rel="noopener" @endif x-on:click="setTimeout(() => $wire.$refresh(), 900)"><i class="fa-solid {{ $icon }}"></i>{{ $label }}{{ in_array($r->id, $got) && ! $r->isExternal() ? ' à nouveau' : '' }}</a>
@endif
