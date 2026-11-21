@props(['title' => null, 'sub' => null, 'p0' => false])
<section {{ $attributes->merge(['class' => 'panel']) }}>
  @if ($title || isset($tools))
    <div class="panel-h"><h2>{{ $title }}</h2>@if ($sub)<span class="sub">{{ $sub }}</span>@endif @isset($tools)<div class="tools">{{ $tools }}</div>@endisset</div>
  @endif
  <div class="panel-b {{ $p0 ? 'p0' : '' }}">{{ $slot }}</div>
</section>
