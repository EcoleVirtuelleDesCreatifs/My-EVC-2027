@props(['title', 'icon' => null, 'size' => '', 'close' => 'closeModal'])
{{-- Fenêtre pilotée par Livewire : afficher avec @if ($...) dans la vue parente. --}}
<div class="modal d-block evc-modal" tabindex="-1" role="dialog" aria-modal="true" wire:keydown.escape.window="{{ $close }}">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable {{ $size }}">
    <div class="modal-content">
      <div class="modal-header"><h2 class="modal-title fs-5 d-flex align-items-center gap-2">@if ($icon)<i class="fa-solid {{ $icon }}"></i>@endif{{ $title }}</h2><button type="button" class="btn-close" wire:click="{{ $close }}" aria-label="Fermer"></button></div>
      <div class="modal-body">{{ $slot }}</div>
      @isset($footer)<div class="modal-footer">{{ $footer }}</div>@endisset
    </div>
  </div>
</div>
<div class="modal-backdrop show evc-backdrop" wire:click="{{ $close }}"></div>
