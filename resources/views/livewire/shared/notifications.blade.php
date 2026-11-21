<div>
  @if (auth()->user()->unreadNotifications()->exists())
    <div class="d-sm-none mb-3"><button class="btn btn-ghost btn-sm" wire:click="toutLu"><i class="fa-solid fa-check-double"></i>Tout marquer comme lu</button></div>
  @endif
  <x-panel :p0="true">
    @forelse ($items as $n)
      <button type="button" class="row-item w-100 text-start border-0 bg-transparent {{ $n->read_at ? '' : 'fw-sb' }}" wire:click="ouvrir('{{ $n->id }}')">
        <span class="nsq {{ $n->data['tone'] ?? 'lav' }}"><i class="fa-solid {{ $n->data['icon'] ?? 'fa-bell' }}"></i></span>
        <div class="ri-main"><div class="ri-t" style="white-space:normal">{{ $n->data['title'] ?? 'Notification' }}</div><div class="ri-s" style="white-space:normal">{{ $n->data['body'] ?? '' }}</div></div>
        <span class="small muted text-nowrap">{{ $n->created_at->diffForHumans() }}</span>
        @unless ($n->read_at)<span class="dot-new" style="width:8px;height:8px;border-radius:50%;background:var(--accent)"></span>@endunless
      </button>
    @empty
      <div class="p-4"><x-empty title="Aucune notification" text="Vous serez prévenu ici des nouveaux projets, corrections, paiements et ressources." /></div>
    @endforelse
  </x-panel>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
