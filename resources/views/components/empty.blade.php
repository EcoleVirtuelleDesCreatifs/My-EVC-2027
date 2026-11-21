@props(['title', 'text' => null])
<div class="u-empty">
  <div class="ue-i"><x-logo variant="symbole-couleur" class="logo-sym" /></div>
  <h3>{{ $title }}</h3>@if ($text)<p>{{ $text }}</p>@endif
  {{ $slot }}
</div>
