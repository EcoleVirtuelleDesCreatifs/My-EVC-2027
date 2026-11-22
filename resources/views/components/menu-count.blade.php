@props(['route', 'count' => 0])
@php($counter = match ($route) { 'myevc.messagerie' => 'unreadMessages', 'myevc.notifications' => 'unreadNotifications', default => null })
@if ($counter)
  <span class="count" x-show="{{ $counter }} > 0" x-text="{{ $counter }}" x-bind:aria-label="{{ $counter }} + '{{ $route === 'myevc.messagerie' ? ' conversations non lues' : ' notifications non lues' }}'" @if (! $count) style="display:none" @endif>{{ $count }}</span>
@elseif ($count)
  <span class="count">{{ $count }}</span>
@endif
