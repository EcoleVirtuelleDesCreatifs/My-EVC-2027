<div wire:poll.15s.visible>
  <p class="muted mb-3">Retrouvez les questions des étudiants et les réponses de l’équipe, du message le plus récent au plus ancien.</p>
  <x-panel title="Discussions des projets" :p0="true">
    @forelse ($items as $a)
      <div class="row-item" wire:key="echange-{{ $a->id }}">
        <span class="icon-sq"><i class="fa-solid fa-comments"></i></span>
        <div class="ri-main">
          <div class="ri-t">{{ $a->project->title }}</div>
          <div class="ri-s">{{ $a->student->name }} · {{ $a->student->matricule }} · {{ $a->project->formation->code }}</div>
          <div class="small muted">{{ $a->messages_count }} message{{ $a->messages_count > 1 ? 's' : '' }} · Dernier échange le {{ \Carbon\Carbon::parse($a->last_message_at)->format('d/m/Y à H:i') }}</div>
        </div>
        <a class="btn btn-primary btn-sm" href="{{ route('projet.echanges', $a) }}" wire:navigate>Lire et répondre</a>
      </div>
    @empty
      <div class="p-4"><x-empty title="Aucun échange pour le moment" text="Les messages envoyés depuis les projets des étudiants apparaîtront ici." /></div>
    @endforelse
  </x-panel>
  <div class="mt-3">{{ $items->links() }}</div>
</div>
