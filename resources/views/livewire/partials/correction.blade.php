@if ($corrige)
  @php($a = \App\Models\Assignment::with('project.formation', 'student.user', 'submissions.files', 'comments.user')->find($corrige))
  <x-modal :title="'Corriger : '.$a->project->title" icon="fa-pen-to-square" size="modal-lg">
    <div class="d-flex align-items-center gap-2 mb-3 flex-wrap"><x-fchip :code="$a->project->formation->code" /><b>{{ $a->student->user->name }}</b><span class="small muted">{{ $a->student->matricule }}</span><x-pill :tone="$a->tone()" class="ms-auto">{{ $a->label(true) }}</x-pill></div>
    @forelse ($a->submissions as $sub)
      <div class="box-row d-block mb-2">
        <div class="small muted mb-1">Dépôt du {{ $sub->created_at->translatedFormat('j F à H\hi') }}</div>
        @if ($sub->message)<p class="mb-2 small">{{ $sub->message }}</p>@endif
        <div class="d-flex flex-wrap gap-2">
          @foreach ($sub->files as $file)<a class="file-chip" href="{{ route('fichier.depot', $file) }}"><i class="fa-solid fa-file-arrow-down"></i>{{ $file->original_name }}</a>@endforeach
          @foreach ($sub->links ?? [] as $l)<a class="file-chip" href="{{ $l }}" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i>{{ \Illuminate\Support\Str::limit($l, 40) }}</a>@endforeach
        </div>
      </div>
    @empty
      <p class="muted small">Aucun dépôt pour le moment.</p>
    @endforelse
    <div class="row g-3 mt-1">
      <div class="col-sm-5"><label class="form-label" for="app">Appréciation (si validé)</label>
        <select id="app" class="form-select" wire:model="appreciation">@foreach (\App\Models\Assignment::APPRECIATIONS as $ap)<option>{{ $ap }}</option>@endforeach</select></div>
      <div class="col-12"><label class="form-label" for="com">Commentaire pour l'étudiant</label>
        <textarea id="com" rows="3" class="form-control @error('commentaire') is-invalid @enderror" wire:model="commentaire" placeholder="Points forts, points à améliorer…"></textarea>
        @error('commentaire')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    </div>
    <x-slot:footer>
      <button class="btn btn-ghost" wire:click="closeModal">Annuler</button>
      <button class="btn btn-outline-danger" wire:click="aReprendre" wire:loading.attr="disabled"><i class="fa-solid fa-rotate"></i>À reprendre</button>
      <button class="btn btn-primary" wire:click="valider" wire:loading.attr="disabled"><i class="fa-solid fa-check"></i>Valider</button>
    </x-slot:footer>
  </x-modal>
@endif
