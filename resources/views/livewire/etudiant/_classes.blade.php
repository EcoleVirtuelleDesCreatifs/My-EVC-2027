@if ($classes->isNotEmpty())
  <section class="vc-wrap" aria-label="Classes virtuelles">
    <div class="vc-head"><span><i class="fa-brands fa-whatsapp"></i></span><div><b>Mes classes virtuelles</b><small>Rejoignez le groupe de votre formation pour suivre les échanges, annonces et sessions en direct.</small></div></div>
    <div class="vc-grid">
      @foreach ($classes as $v)
        @php([$pl, $pi] = $v->platform())
        <article class="vc-card c-{{ $v->formation->code }}">
          <div class="d-flex align-items-start gap-2">
            <span class="vc-ico {{ $pl === 'WhatsApp' ? 'wa' : '' }}"><i class="{{ $pi }}"></i></span>
            <div class="min-w-0 flex-grow-1"><b class="d-block">{{ $v->name }}</b><small class="muted">{{ $pl }}{{ $v->promotion ? ' · '.$v->promotion->name : '' }}</small></div>
            <x-fchip :code="$v->formation->code" />
          </div>
          @if ($v->schedule)<p class="vc-meta"><i class="fa-regular fa-clock"></i>{{ $v->schedule }}</p>@endif
          @if ($v->description)<p class="vc-desc">{{ $v->description }}</p>@endif
          <a class="btn {{ $pl === 'WhatsApp' ? 'btn-wa' : 'btn-primary' }} btn-sm mt-auto" href="{{ $v->url }}" target="_blank" rel="noopener"><i class="{{ $pi }}"></i>Rejoindre la classe</a>
        </article>
      @endforeach
    </div>
  </section>
@endif
