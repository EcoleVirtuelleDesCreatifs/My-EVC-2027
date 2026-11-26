<div class="project-conversation" wire:poll.15s.visible>
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <a class="btn btn-ghost btn-sm" href="{{ route('projets.echanges') }}" wire:navigate><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Toutes les conversations</a>
    <div class="d-flex gap-2 align-items-center"><x-fchip :code="$a->project->formation->code" /><x-pill :tone="$a->tone()">{{ $a->label(true) }}</x-pill></div>
  </div>
  <section class="pc-chat" aria-label="Conversation du projet">
    <header class="pc-head">
      <span class="pc-avatar" aria-hidden="true">
        @if ($avatar = $a->student->user->avatarUrl())<img src="{{ $avatar }}" alt="">@else{{ $a->student->user->initials() }}@endif
      </span>
      <div class="pc-person">
        <h2>{{ $a->student->name }}</h2>
        <p>{{ $a->student->matricule }} · Étudiant</p>
        <span class="small muted">Conversation avec l’équipe pédagogique</span>
      </div>
      @can('admin.etudiants')<a class="btn btn-ghost btn-sm" href="{{ route('admin.etudiant', $a->student) }}" wire:navigate>Fiche étudiant</a>@endcan
    </header>
    <div class="pc-context">
      <i class="fa-solid fa-pen-nib" aria-hidden="true"></i>
      <div><b>{{ $a->project->title }}</b><span>À rendre le {{ $a->due_on->format('d/m/Y') }} · {{ $a->comments->where('kind', 'commentaire')->count() }} message(s)</span></div>
    </div>
    <div class="pc-thread" role="log" aria-live="polite" aria-relevant="additions" tabindex="0" aria-label="Messages et événements du projet" x-data x-init="$nextTick(() => $el.scrollTop = $el.scrollHeight)" x-on:projet-message-envoye.window="$nextTick(() => { $el.scrollTop = $el.scrollHeight })">
      @forelse ($a->comments as $comment)
        @if ($loop->first || ! $comment->created_at->isSameDay($a->comments[$loop->index - 1]->created_at))
          <div class="pc-day">{{ $comment->created_at->isToday() ? 'Aujourd’hui' : $comment->created_at->translatedFormat('j F Y') }}</div>
        @endif
        @if ($comment->kind !== 'commentaire')
          <div class="pc-event" wire:key="comment-{{ $comment->id }}">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
            <div><span class="pc-event-label">Événement du projet · {{ $comment->created_at->format('H:i') }}</span><p>{{ $comment->body }}</p></div>
          </div>
        @else
          @php($mine = $comment->user_id === auth()->id())
          @php($studentMessage = $comment->user_id === $a->student->user_id)
          <article class="pc-message {{ $mine ? 'pc-mine' : '' }}" wire:key="comment-{{ $comment->id }}" aria-label="Message de {{ $comment->user?->name ?? 'Utilisateur supprimé' }}">
            <span class="pc-avatar pc-avatar-sm" aria-hidden="true">{{ $comment->user?->initials() ?? '?' }}</span>
            <div class="pc-bubble">
              <div class="pc-author"><b>{{ $mine ? 'Vous' : ($comment->user?->name ?? 'Utilisateur supprimé') }}</b><span>{{ $studentMessage ? 'Étudiant' : 'Équipe pédagogique' }}</span></div>
              <p>{{ $comment->body }}</p>
              <time datetime="{{ $comment->created_at->toAtomString() }}">{{ $comment->created_at->format('H:i') }}@if ($mine)<span class="ms-2"><i class="fa-solid fa-check" aria-hidden="true"></i> Envoyé</span>@endif</time>
            </div>
          </article>
        @endif
      @empty
        <div class="pc-empty"><i class="fa-regular fa-comments" aria-hidden="true"></i><h3>La conversation commence ici</h3><p>Écrivez à l’étudiant pour l’accompagner dans son projet.</p></div>
      @endforelse
    </div>
    <form wire:submit="envoyer" class="pc-compose">
      <div class="pc-input">
        <label class="form-label" for="reponse-projet">Votre message à {{ $a->student->firstName() }}</label>
        <textarea id="reponse-projet" class="form-control @error('message') is-invalid @enderror" wire:model="message" rows="3" maxlength="2000" required placeholder="Écrivez votre réponse à l’étudiant…" aria-describedby="pc-compose-help" x-on:keydown.ctrl.enter.prevent="$el.form.requestSubmit()" x-on:keydown.meta.enter.prevent="$el.form.requestSubmit()"></textarea>
        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <p id="pc-compose-help" class="small muted mb-0 mt-2"><i class="fa-regular fa-bell me-1" aria-hidden="true"></i>Notification MY EVC et e-mail à l’étudiant · Ctrl / ⌘ + Entrée pour envoyer</p>
      </div>
      <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="envoyer"><i class="fa-solid fa-paper-plane" aria-hidden="true"></i><span wire:loading.remove wire:target="envoyer">Envoyer</span><span wire:loading wire:target="envoyer">Envoi…</span></button>
    </form>
  </section>
</div>
