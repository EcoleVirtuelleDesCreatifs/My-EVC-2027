@props(['label', 'value', 'sub' => null, 'icon' => 'fa-circle', 'tone' => 'lav', 'subTone' => null])
<div {{ $attributes->merge(['class' => 'ukpi']) }}>
  <div class="uk-h"><span>{{ $label }}</span>@if ($icon)<span class="uk-i t-{{ $tone }}"><i class="fa-solid {{ $icon }}"></i></span>@endif</div>
  <div class="uk-v">{{ $value }}</div>
  @if ($sub)<div class="uk-s {{ $subTone ? 'tx-'.$subTone : '' }}">{{ $sub }}</div>@endif
</div>
