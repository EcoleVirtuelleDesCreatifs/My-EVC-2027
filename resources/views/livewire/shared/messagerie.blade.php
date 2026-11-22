<div class="msg-app {{ $c ? 'has-thread' : '' }}" wire:poll.5s.visible="actualiser(document.visibilityState === 'visible')" x-data="{ soundEnabled: window.evcMessageSoundsEnabled() }" x-on:message-sound-setting.window="soundEnabled = $event.detail.enabled" x-on:visibilitychange.document="if (document.visibilityState === 'visible') $wire.actualiser(true)">
  <aside class="msg-list panel">
    <div class="px-3 pt-3 d-flex flex-wrap gap-2">
      <button type="button" class="btn btn-ghost btn-sm" x-on:click="window.evcToggleMessageSounds()" x-bind:aria-pressed="soundEnabled" aria-label="Activer ou couper les sonneries"><i class="fa-solid" x-bind:class="soundEnabled ? 'fa-volume-high' : 'fa-volume-xmark'" aria-hidden="true"></i><span x-text="soundEnabled ? 'Sonneries activées' : 'Activer les sonneries'">Sonneries activées</span></button>
      <button type="button" class="btn btn-ghost btn-sm" data-message-sound-test>Tester le son</button>
      <p class="small muted mb-0 w-100">Envoi et réception · Cliquez sur « Tester le son » pour autoriser l’audio dans ce navigateur.</p>
    </div>
    <div class="p-3 border-bottom d-flex gap-2">
      <label class="search-field flex-grow-1 mb-0"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" placeholder="Rechercher…" wire:model.live.debounce.300ms="recherche"></label>
      <button class="btn btn-primary btn-icon d-sm-none" wire:click="ouvrirNouveau" aria-label="Nouveau message"><i class="fa-solid fa-pen"></i></button>
    </div>
    <div class="msg-items">
      @forelse ($list as $conv)
        @php($others = $conv->participants->where('id', '!=', $u->id))
        @php($unread = $conv->isUnreadFor($u))
        <button type="button" class="msg-item {{ $c?->id === $conv->id ? 'on' : '' }} {{ $unread ? 'unread' : '' }}" wire:click="ouvrir({{ $conv->id }})">
          <span class="ini">{{ $others->first()?->initials() ?? '?' }}</span>
          <span class="mi-main"><span class="mi-top"><b>{{ $others->pluck('name')->implode(', ') ?: 'Moi' }}</b><small>{{ $conv->last_message_at?->diffForHumans(short: true) }}</small></span>
            <span class="mi-sub">{{ $conv->subject }}</span>
            <span class="mi-prev">{{ $conv->lastMessage?->user_id === $u->id ? 'Vous : ' : '' }}{{ \Illuminate\Support\Str::limit($conv->lastMessage?->body, 70) }}</span></span>
          @if ($unread)<span class="mi-dot" aria-label="Non lu"></span>@endif
        </button>
      @empty
        <div class="p-4"><x-empty title="Aucune conversation" text="Écrivez à vos formateurs ou à l'équipe de l'école."><button class="btn btn-primary btn-sm" wire:click="ouvrirNouveau">Nouveau message</button></x-empty></div>
      @endforelse
    </div>
  </aside>

  <section class="msg-thread panel">
    @if ($c)
      @php($others = $c->participants->where('id', '!=', $u->id))
      <header class="mt-head">
        <button class="btn btn-quiet btn-icon d-md-none" wire:click="fermer" aria-label="Retour"><i class="fa-solid fa-arrow-left"></i></button>
        <div class="mt-who"><b>{{ $c->subject }}</b><small>{{ $others->map(fn ($o) => $o->name.' · '.$o->roleLabel())->implode(' — ') }}</small></div>
      </header>
      <div class="mt-body" id="mt-body" x-data x-init="$el.scrollTop = $el.scrollHeight" x-on:message-envoye.window="$nextTick(() => $el.scrollTop = $el.scrollHeight)">
        @foreach ($c->messages as $m)
          @php($mine = $m->user_id === $u->id)
          @if ($loop->first || ! $m->created_at->isSameDay($c->messages[$loop->index - 1]->created_at))
            <div class="mt-day">{{ ucfirst(jour($m->created_at)) }}</div>
          @endif
          <div class="bubble {{ $mine ? 'me' : '' }}">
            @unless ($mine)<small class="b-who">{{ $m->user?->name ?? 'Utilisateur supprimé' }}</small>@endunless
            <div class="b-txt">{{ $m->body }}</div>
            @if ($m->file_path)<a class="file-chip mt-1" href="{{ route('fichier.message', $m) }}"><i class="fa-solid fa-paperclip"></i>{{ $m->file_name }}</a>@endif
            <small class="b-time">{{ $m->created_at->format('H\hi') }}
              @if ($mine)
                @php($read = $others->isNotEmpty() && $others->every(fn ($participant) => (int) $participant->pivot->last_read_id >= $m->id))
                <span class="b-receipt {{ $read ? 'is-read' : '' }}" data-read="{{ $read ? 'true' : 'false' }}" role="img" aria-label="{{ $read ? 'Lu par tous les destinataires' : 'Envoyé, pas encore lu par tous les destinataires' }}" title="{{ $read ? 'Lu par tous les destinataires' : 'Envoyé, pas encore lu par tous les destinataires' }}"><i class="fa-solid {{ $read ? 'fa-check-double' : 'fa-check' }}" aria-hidden="true"></i></span>
              @endif
            </small>
          </div>
        @endforeach
      </div>
      <form class="mt-compose" wire:submit="envoyer">
        <label class="btn btn-quiet btn-icon mb-0" title="Joindre un fichier"><i class="fa-solid fa-paperclip"></i><input type="file" class="d-none" wire:model="piece"></label>
        <div class="flex-grow-1">
          @if ($piece)<span class="file-chip mb-1"><i class="fa-solid fa-paperclip"></i>{{ $piece->getClientOriginalName() }}<button type="button" class="btn-close ms-1" style="font-size:9px" wire:click="$set('piece', null)" aria-label="Retirer"></button></span>@endif
          <textarea class="form-control @error('texte') is-invalid @enderror" rows="1" placeholder="Votre message…" wire:model="texte" x-data x-on:keydown.enter="if (!$event.shiftKey) { $event.preventDefault(); $wire.envoyer() }"></textarea>
          @error('texte')<div class="invalid-feedback">{{ $message }}</div>@enderror @error('piece')<div class="small text-danger">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-primary btn-icon" type="submit" wire:loading.attr="disabled" wire:target="envoyer,piece" aria-label="Envoyer"><i class="fa-solid fa-paper-plane"></i></button>
      </form>
    @else
      <div class="d-flex h-100 align-items-center justify-content-center p-4"><x-empty title="Sélectionnez une conversation" text="Ou écrivez un nouveau message à un formateur ou à l'équipe de l'école." /></div>
    @endif
  </section>

  @if ($nouveau)
    <x-modal title="Nouveau message" icon="fa-pen" size="modal-lg">
      <div class="d-flex flex-column gap-3">
        <div>
          <label class="form-label">Destinataire(s)</label>
          <input class="form-control mb-2" placeholder="Rechercher un nom ou un rôle…" wire:model.live.debounce.250ms="filtreDest">
          <div class="border rounded-3 p-2" style="max-height:220px;overflow:auto">
            @forelse ($recipients as $r)
              <label class="d-flex align-items-center gap-2 p-1 small"><input type="checkbox" class="form-check-input mt-0" value="{{ $r->id }}" wire:model="destinataires"><span class="ini" style="width:26px;height:26px;font-size:11px">{{ $r->initials() }}</span><b>{{ $r->name }}</b><span class="muted">{{ $r->roleLabel() }}</span></label>
            @empty
              <p class="small muted mb-0 p-2">Aucun destinataire trouvé.</p>
            @endforelse
          </div>
          @error('destinataires')<div class="small text-danger mt-1">{{ $message }}</div>@enderror
        </div>
        <div><label class="form-label">Objet</label><input class="form-control @error('objet') is-invalid @enderror" wire:model="objet" placeholder="Ex. : Question sur le projet logo">@error('objet')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><label class="form-label">Message</label><textarea class="form-control @error('premier') is-invalid @enderror" rows="4" wire:model="premier"></textarea>@error('premier')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="demarrer" wire:loading.attr="disabled"><i class="fa-solid fa-paper-plane"></i>Envoyer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
