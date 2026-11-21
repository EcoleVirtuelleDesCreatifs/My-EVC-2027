<div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link {{ $onglet === 'messages' ? 'active' : '' }}" wire:click="$set('onglet','messages')">Contact et partenariats <span class="badge rounded-pill text-bg-warning">{{ $messages->whereNull('handled_at')->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link {{ $onglet === 'newsletter' ? 'active' : '' }}" wire:click="$set('onglet','newsletter')">Newsletter <span class="badge rounded-pill text-bg-secondary">{{ $nbSubs }}</span></button></li>
  </ul>
  @if ($onglet === 'messages')
    @forelse ($messages as $m)
      <x-panel :title="$m->name ?: 'Visiteur'" class="mb-3 {{ $m->handled_at ? 'opacity-75' : '' }}">
        <x-slot:tools><x-pill :tone="$m->source === 'partenariat' ? 'acc' : 'info'" nodot>{{ $m->source === 'partenariat' ? 'Partenariat' : 'Contact' }}</x-pill><span class="small muted ms-2">{{ $m->created_at->diffForHumans() }}</span></x-slot:tools>
        <div class="lib-dl mb-3">@foreach ($m->fields as $k => $v)<div class="lib-dl-row"><span>{{ $k }}</span><b style="white-space:pre-line;text-align:left">{{ $v }}</b></div>@endforeach</div>
        <div class="d-flex gap-2 flex-wrap">
          @if ($m->email)<a class="btn btn-ghost btn-sm" href="mailto:{{ $m->email }}"><i class="fa-solid fa-reply"></i>Répondre</a>@endif
          @if ($m->phone)<a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $m->phone) }}"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>@endif
          <button class="btn btn-sm {{ $m->handled_at ? 'btn-ghost' : 'btn-primary' }} ms-auto" wire:click="traiter({{ $m->id }})">{{ $m->handled_at ? 'Rouvrir' : 'Marquer comme traité' }}</button>
        </div>
      </x-panel>
    @empty
      <x-empty title="Aucun message" text="Les messages envoyés depuis les formulaires du site arrivent ici." />
    @endforelse
  @else
    <x-panel title="Abonnés à la newsletter" :p0="true">
      <x-slot:tools><button class="btn btn-ghost btn-sm" wire:click="exporter"><i class="fa-solid fa-file-csv"></i>Exporter (CSV)</button></x-slot:tools>
      @forelse ($subscribers as $s)
        <div class="row-item"><span class="icon-sq"><i class="fa-solid fa-envelope"></i></span><div class="ri-main"><div class="ri-t">{{ $s->email }}</div></div><span class="small muted">{{ $s->created_at->translatedFormat('j M Y') }}</span></div>
      @empty
        <div class="p-4"><x-empty title="Aucun abonné pour le moment" /></div>
      @endforelse
    </x-panel>
  @endif
</div>
