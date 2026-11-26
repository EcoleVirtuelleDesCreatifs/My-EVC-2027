<div>
  <div class="filters-row mb-3">
    @if ($formations->count() > 1)<select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>@endif
    <button class="btn btn-primary d-sm-none" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouveau quiz</button>
  </div>
  <div class="panel tbl"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Quiz</th><th class="text-end">Questions</th><th>Paramètres</th><th class="text-end">Passages</th><th class="text-end">Moyenne</th><th>Statut</th><th></th></tr></thead><tbody>
    @forelse ($items as $q)
      <tr><td><x-fchip :code="$q->formation->code" /> <span class="fw-sb">{{ $q->title }}</span>@if ($q->required) <x-pill tone="info" nodot>Obligatoire</x-pill>@endif</td>
        <td class="text-end">{{ $q->questions_count }}</td><td class="small">{{ $q->duration_min }} min · {{ $q->attempts_allowed }} tentative(s) · {{ $q->pass_percent }} %</td>
        <td class="text-end">{{ $q->passages }}</td><td class="text-end">{{ $q->moyenne !== null ? round($q->moyenne).' %' : '—' }}</td>
        <td>@if ($q->published)<x-pill :tone="$q->isOpen() ? 'ok' : 'mute'">{{ $q->isOpen() ? 'Ouvert' : 'Fermé' }}</x-pill>@else<x-pill class="text-white">Brouillon</x-pill>@endif</td>
        <td class="table-actions"><a class="btn btn-ghost btn-sm" href="{{ gestion_route('quiz.editeur', $q) }}" wire:navigate>Ouvrir</a></td></tr>
    @empty
      <tr><td colspan="7"><x-empty title="Aucun quiz" text="Créez un quiz pour vérifier les acquis à la fin d'un module." /></td></tr>
    @endforelse
  </tbody></table></div></div>
  @if ($form)
    <x-modal title="Nouveau quiz" icon="fa-list-check">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Formation</label><select class="form-select" wire:model="q.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('q.title') is-invalid @enderror" wire:model="q.title" placeholder="Ex. : QCM — Photoshop">@error('q.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Consignes</label><textarea class="form-control" rows="2" wire:model="q.description"></textarea></div>
        <div class="col-4"><label class="form-label">Durée (min)</label><input type="number" class="form-control" wire:model="q.duration_min"></div>
        <div class="col-4"><label class="form-label">Tentatives</label><input type="number" class="form-control" wire:model="q.attempts_allowed"></div>
        <div class="col-4"><label class="form-label">Réussite (%)</label><input type="number" class="form-control" wire:model="q.pass_percent"></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="qr" wire:model="q.required"><label class="form-check-label" for="qr">Obligatoire pour le certificat</label></div></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer">Créer et ajouter les questions</button></x-slot:footer>
    </x-modal>
  @endif
</div>
