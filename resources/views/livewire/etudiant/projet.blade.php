<div class="u-grid u-2-1 align-items-start">
  <div class="stack">
    <x-panel :title="$a->project->title">
      <x-slot:tools><x-pill :tone="$a->tone()">{{ $a->isLate() ? 'En retard' : $a->label() }}</x-pill></x-slot:tools>
      <div class="d-flex flex-wrap gap-3 small muted mb-3">
        <span><x-fchip :code="$a->project->formation->code" /> {{ $a->project->formation->short_name }}</span>
        <span><i class="fa-regular fa-calendar me-1"></i>À rendre le {{ $a->due_on->translatedFormat('l j F') }}</span>
        <span><i class="fa-solid fa-tag me-1"></i>{{ $a->project->type }}</span>
      </div>
      <h3 class="small-caps">Consignes</h3>
      <div class="mb-3"><x-project-sheet :title="$a->project->title" :type="$a->project->type">{!! $a->project->briefHtml() !!}</x-project-sheet></div>
      @if ($a->status === 'afaire')
        <button class="btn btn-primary btn-lg" wire:click="demarrer" wire:loading.attr="disabled"><i class="fa-solid fa-play"></i>Je démarre</button>
      @endif
      @if ($a->status === 'valide')
        <div class="alert-strip as-ok mt-2"><i class="fa-solid fa-circle-check"></i><span>Projet validé le {{ $a->validated_at?->translatedFormat('j F Y') }} — appréciation : <b>{{ $a->appreciation }}</b></span></div>
      @endif
    </x-panel>

    @if (in_array($a->status, ['encours', 'reprendre', 'afaire']))
      <x-panel title="Rendre mon travail" sub="Fichiers (50 Mo max. chacun) et/ou liens (Behance, Drive, Canva…)">
        <form wire:submit="deposer" class="d-flex flex-column gap-3">
          <div>
            <label class="form-label" for="fichiers">Fichiers</label>
            <input type="file" id="fichiers" class="form-control @error('fichiers.*') is-invalid @enderror" wire:model="fichiers" multiple>
            <div wire:loading wire:target="fichiers" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi des fichiers…</div>
            @error('fichiers.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @if ($fichiers)
              <div class="d-flex flex-wrap gap-2 mt-2">@foreach ($fichiers as $i => $fi)<span class="file-chip"><i class="fa-solid fa-paperclip"></i>{{ $fi->getClientOriginalName() }}<button type="button" class="btn-close btn-sm ms-1" style="font-size:9px" wire:click="retirerFichier({{ $i }})" aria-label="Retirer"></button></span>@endforeach</div>
            @endif
          </div>
          <div><label class="form-label" for="liens">Liens <span class="small muted">(un par ligne)</span></label><textarea id="liens" class="form-control" rows="2" wire:model="liens" placeholder="https://"></textarea></div>
          <div><label class="form-label" for="msg">Message pour le formateur <span class="small muted">(facultatif)</span></label><textarea id="msg" class="form-control" rows="2" wire:model="message"></textarea></div>
          @error('depot')<div class="alert alert-danger small mb-0">{{ $message }}</div>@enderror
          <div><button class="btn btn-primary btn-lg" type="submit" wire:loading.attr="disabled" wire:target="deposer,fichiers"><i class="fa-solid fa-cloud-arrow-up"></i>Rendre mon projet</button></div>
        </form>
      </x-panel>
    @endif

    @if ($a->status === 'soumis' && ($last = $a->submissions->first()))
      <x-panel title="Mon rendu en cours de validation" sub="Vous pouvez encore le modifier tant que votre formateur ne l’a pas corrigé.">
        @if (! $modification)
          <div class="alert-strip as-info mb-3"><i class="fa-solid fa-hourglass-half"></i><span>Rendu le {{ $a->submitted_at?->translatedFormat('j F Y à H\hi') }}@if ($last->updated_at->gt($last->created_at->copy()->addMinute())) · modifié le {{ $last->updated_at->translatedFormat('j F à H\hi') }}@endif</span></div>
          <button class="btn btn-ghost" wire:click="modifierRendu"><i class="fa-solid fa-pen"></i>Modifier mon rendu</button>
        @else
          <form wire:submit="enregistrerModification" class="d-flex flex-column gap-3">
            @if ($last->files->isNotEmpty())
              <div><p class="form-label mb-2">Fichiers déposés</p>
                <div class="d-flex flex-column gap-2">
                  @foreach ($last->files as $file)
                    @php($off = in_array($file->id, $aRetirer, true))
                    <div class="amend-file {{ $off ? 'off' : '' }}"><i class="fa-solid fa-paperclip"></i><span class="flex-grow-1 text-truncate">{{ $file->original_name }}</span>
                      <button type="button" class="btn btn-ghost btn-sm" wire:click="basculerRetrait({{ $file->id }})">{{ $off ? 'Conserver' : 'Retirer' }}</button></div>
                  @endforeach
                </div></div>
            @endif
            <div>
              <label class="form-label" for="ajout">Ajouter des fichiers <span class="small muted">(50 Mo max. chacun)</span></label>
              <input type="file" id="ajout" class="form-control @error('fichiers.*') is-invalid @enderror" wire:model="fichiers" multiple>
              <div wire:loading wire:target="fichiers" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi des fichiers…</div>
              @error('fichiers.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
              @if ($fichiers)
                <div class="d-flex flex-wrap gap-2 mt-2">@foreach ($fichiers as $i => $fi)<span class="file-chip"><i class="fa-solid fa-paperclip"></i>{{ $fi->getClientOriginalName() }}<button type="button" class="btn-close btn-sm ms-1" style="font-size:9px" wire:click="retirerFichier({{ $i }})" aria-label="Retirer"></button></span>@endforeach</div>
              @endif
            </div>
            <div><label class="form-label" for="liensM">Liens <span class="small muted">(un par ligne)</span></label><textarea id="liensM" class="form-control" rows="2" wire:model="liens" placeholder="https://"></textarea></div>
            <div><label class="form-label" for="msgM">Message pour le formateur</label><textarea id="msgM" class="form-control" rows="2" wire:model="message"></textarea></div>
            @error('depot')<div class="alert alert-danger small mb-0">{{ $message }}</div>@enderror
            <div class="d-flex gap-2 flex-wrap"><button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="enregistrerModification,fichiers"><i class="fa-solid fa-floppy-disk"></i>Enregistrer les modifications</button>
              <button class="btn btn-ghost" type="button" wire:click="annulerModification">Annuler</button></div>
          </form>
        @endif
      </x-panel>
    @endif

    @if ($a->submissions->isNotEmpty())
      <x-panel title="Mes dépôts">
        @foreach ($a->submissions as $sub)
          <div class="mb-3 pb-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
            <div class="small muted mb-2">Déposé le {{ $sub->created_at->translatedFormat('j F Y à H\hi') }}</div>
            @if ($sub->message)<p class="mb-2">{{ $sub->message }}</p>@endif
            <div class="d-flex flex-wrap gap-2">
              @foreach ($sub->files as $file)<a class="file-chip" href="{{ route('fichier.depot', $file) }}"><i class="fa-solid fa-file-arrow-down"></i>{{ $file->original_name }}</a>@endforeach
              @foreach ($sub->links ?? [] as $l)<a class="file-chip" href="{{ $l }}" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i>{{ \Illuminate\Support\Str::limit($l, 40) }}</a>@endforeach
            </div>
          </div>
        @endforeach
      </x-panel>
    @endif
  </div>

  <x-panel title="Historique et échanges">
    <ul class="timeline-c" wire:poll.15s.visible>
      @foreach ($a->comments as $c)
        <li class="k-{{ $c->kind }}"><span class="tc-dot"></span>
          <div class="small"><b>{{ $c->user?->name ?? 'EVC' }}</b> <span class="muted">· {{ $c->created_at->diffForHumans() }}</span></div>
          <div class="small" style="white-space:pre-line">{{ $c->body }}</div></li>
      @endforeach
    </ul>
    <form wire:submit="commenter" class="mt-2">
      <textarea class="form-control @error('commentaire') is-invalid @enderror" rows="2" wire:model="commentaire" placeholder="Une question sur ce projet ?"></textarea>
      <button class="btn btn-ghost btn-sm mt-2" type="submit"><i class="fa-solid fa-paper-plane"></i>Envoyer</button>
    </form>
  </x-panel>
</div>
