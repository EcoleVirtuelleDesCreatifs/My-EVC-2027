<div>
  <div class="filters-row mb-3">
    <div class="seg seg-soft"><button type="button" class="{{ $vue === 'avenir' ? 'on' : '' }}" wire:click="$set('vue','avenir')">À venir</button><button type="button" class="{{ $vue === 'passees' ? 'on' : '' }}" wire:click="$set('vue','passees')">Passées</button></div>
    <select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>
    <button class="btn btn-primary d-sm-none" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouvelle séance</button>
  </div>
  <div class="panel tbl" wire:loading.class="wire-loading">
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Date</th><th>Séance</th><th>Formateur</th><th>Mode</th><th class="text-end">Présences</th><th></th></tr></thead><tbody>
      @forelse ($items as $x)
        <tr class="{{ $x->cancelled ? 'opacity-50' : '' }}">
          <td class="text-nowrap"><b>{{ jour($x->starts_at) }}</b><div class="small muted">{{ $x->hours() }}</div></td>
          <td><x-fchip :code="$x->formation->code" /> <span class="fw-sb">{{ $x->title }}</span>@if ($x->cancelled) <x-pill tone="bad">Annulée</x-pill>@endif<div class="small muted">{{ $x->module }} · {{ $x->promotion?->name }}</div></td>
          <td class="small">{{ $x->trainer?->name ?? '—' }}</td>
          <td><x-pill :tone="$x->mode === 'virtuel' ? 'info' : 'acc'" nodot>{{ $x->modeLabel() }}</x-pill></td>
          <td class="text-end">{{ $x->presences_count ?: '—' }}</td>
          <td class="table-actions">
            @if ($x->starts_at->subMinutes(30)->isPast())<a class="btn btn-ghost btn-sm" href="{{ route('formateur.appel', $x) }}" wire:navigate>Appel</a>@endif
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifier({{ $x->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="annuler({{ $x->id }})" @unless ($x->cancelled) wire:confirm="Annuler cette séance ? Les étudiants seront prévenus." @endunless aria-label="{{ $x->cancelled ? 'Rétablir' : 'Annuler' }}"><i class="fa-solid {{ $x->cancelled ? 'fa-rotate-left' : 'fa-ban' }}"></i></button>
          </td>
        </tr>
      @empty
        <tr><td colspan="6"><x-empty title="Aucune séance" /></td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  <div class="mt-3">{{ $items->links() }}</div>

  @if ($form)
    <x-modal :title="$editId ? 'Modifier la séance' : 'Nouvelle séance'" icon="fa-calendar-plus" size="modal-lg">
      <div class="row g-3">
        <div class="col-sm-4"><label class="form-label">Formation</label><select class="form-select" wire:model="s.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select></div>
        <div class="col-sm-4"><label class="form-label">Session</label><select class="form-select" wire:model="s.promotion_id"><option value="">Toutes</option>@foreach ($promotions as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div class="col-sm-4"><label class="form-label">Formateur</label><select class="form-select" wire:model="s.trainer_id"><option value="">—</option>@foreach ($trainers as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
        <div class="col-sm-8"><label class="form-label">Titre</label><input class="form-control @error('s.title') is-invalid @enderror" wire:model="s.title">@error('s.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-4"><label class="form-label">Module</label><input class="form-control" wire:model="s.module"></div>
        <div class="col-sm-4"><label class="form-label">Date</label><input class="form-control" type="date" wire:model="s.date"></div>
        <div class="col-sm-4"><label class="form-label">Heure</label><input class="form-control" type="time" wire:model="s.heure"></div>
        <div class="col-sm-4"><label class="form-label">Durée (h)</label><input class="form-control" type="number" step="0.5" wire:model="s.duree"></div>
        <div class="col-sm-4"><label class="form-label">Mode</label><select class="form-select" wire:model.live="s.mode"><option value="virtuel">En ligne</option><option value="presentiel">Présentiel</option></select></div>
        <div class="col-sm-8">@if (($s['mode'] ?? '') === 'virtuel')<label class="form-label">Lien Google Meet</label><input class="form-control @error('s.meet_url') is-invalid @enderror" wire:model="s.meet_url" placeholder="https://meet.google.com/…">@error('s.meet_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
          @else<label class="form-label">Salle</label><input class="form-control" wire:model="s.room">@endif</div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
