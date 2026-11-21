<div>
  <div class="d-flex align-items-center gap-2 mb-3 flex-wrap"><x-fchip :code="$q->formation->code" />@if ($q->published)<x-pill :tone="$q->isOpen() ? 'ok' : 'mute'">{{ $q->isOpen() ? 'Publié et ouvert' : 'Publié, fermé' }}</x-pill>@else<x-pill>Brouillon</x-pill>@endif
    <button class="btn btn-sm d-sm-none ms-auto {{ $q->published ? 'btn-ghost' : 'btn-primary' }}" wire:click="publier">{{ $q->published ? 'Dépublier' : 'Publier' }}</button></div>
  <ul class="nav nav-tabs mb-3">
    @foreach (['questions' => 'Questions ('.$q->questions->count().')', 'parametres' => 'Paramètres', 'resultats' => 'Résultats'] as $k => $l)<li class="nav-item"><button class="nav-link {{ $onglet === $k ? 'active' : '' }}" wire:click="$set('onglet','{{ $k }}')">{{ $l }}</button></li>@endforeach
  </ul>

  @if ($onglet === 'questions')
    @foreach ($q->questions as $n => $qq)
      <x-panel class="mb-3">
        <div class="d-flex gap-2 align-items-start">
          <span class="small-caps mt-1">Q{{ $n + 1 }}</span>
          <div class="flex-grow-1"><p class="fw-sb mb-2" style="white-space:pre-line">{{ $qq->text }}</p>
            <ol type="A" class="small mb-1 ps-3">@foreach ($qq->options as $i => $o)<li class="{{ $i === $qq->answer ? 'fw-sb text-success' : '' }}">{{ $o }} @if ($i === $qq->answer)<i class="fa-solid fa-check"></i>@endif</li>@endforeach</ol>
            @if ($qq->explanation)<p class="small muted mb-0"><i class="fa-regular fa-lightbulb me-1"></i>{{ $qq->explanation }}</p>@endif</div>
          <div class="d-flex flex-column gap-1">
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="deplacer({{ $qq->id }}, -1)" @disabled($loop->first) aria-label="Monter"><i class="fa-solid fa-arrow-up"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="deplacer({{ $qq->id }}, 1)" @disabled($loop->last) aria-label="Descendre"><i class="fa-solid fa-arrow-down"></i></button>
          </div>
          <div class="d-flex flex-column gap-1">
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifierQuestion({{ $qq->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimerQuestion({{ $qq->id }})" wire:confirm="Supprimer cette question ?" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
          </div>
        </div>
      </x-panel>
    @endforeach
    <button class="btn btn-primary" wire:click="nouvelleQuestion"><i class="fa-solid fa-plus"></i>Ajouter une question</button>
  @elseif ($onglet === 'parametres')
    <x-panel style="max-width:760px">
      <form wire:submit="enregistrerParametres" class="row g-3">
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('p.title') is-invalid @enderror" wire:model="p.title">@error('p.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Consignes</label><textarea class="form-control" rows="3" wire:model="p.description"></textarea></div>
        <div class="col-4"><label class="form-label">Durée (min)</label><input type="number" class="form-control" wire:model="p.duration_min"></div>
        <div class="col-4"><label class="form-label">Tentatives</label><input type="number" class="form-control" wire:model="p.attempts_allowed"></div>
        <div class="col-4"><label class="form-label">Réussite (%)</label><input type="number" class="form-control" wire:model="p.pass_percent"></div>
        <div class="col-sm-6"><label class="form-label">Ouverture <span class="small muted">(facultatif)</span></label><input type="datetime-local" class="form-control" wire:model="p.opens_at"></div>
        <div class="col-sm-6"><label class="form-label">Clôture <span class="small muted">(facultatif)</span></label><input type="datetime-local" class="form-control @error('p.closes_at') is-invalid @enderror" wire:model="p.closes_at">@error('p.closes_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="qreq" wire:model="p.required"><label class="form-check-label" for="qreq">Obligatoire pour le certificat</label></div></div>
        <div class="col-12"><button class="btn btn-primary" type="submit">Enregistrer</button></div>
      </form>
    </x-panel>
  @else
    <div class="u-grid u-2-1 align-items-start">
      <div class="panel tbl"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Étudiant</th><th class="text-end">Tentatives</th><th class="text-end">Meilleur score</th><th>Résultat</th><th></th></tr></thead><tbody>
        @foreach ($results as $r)
          <tr><td><b>{{ $r['s']->user->name }}</b><div class="small muted mono">{{ $r['s']->matricule }}</div></td><td class="text-end">{{ $r['tries']->count() }} / {{ $q->attempts_allowed }}</td>
            <td class="text-end">{{ $r['best'] !== null ? $r['best'].' %' : '—' }}</td>
            <td>@if ($r['best'] === null)<x-pill>Pas encore passé</x-pill>@elseif ($r['best'] >= $q->pass_percent)<x-pill tone="ok">Réussi</x-pill>@else<x-pill tone="bad">Non atteint</x-pill>@endif</td>
            <td class="table-actions">@if ($r['tries']->count())<button class="btn btn-quiet btn-sm" wire:click="autoriserNouvelEssai({{ $r['s']->id }})" wire:confirm="Annuler la dernière tentative de {{ $r['s']->user->name }} pour lui permettre de repasser le quiz ?">Nouvel essai</button>@endif</td></tr>
        @endforeach
      </tbody></table></div></div>
      <x-panel title="Réussite par question" :p0="true">
        @foreach ($q->questions as $n => $qq)
          <div class="row-item"><span class="small-caps">Q{{ $n + 1 }}</span><div class="ri-main"><div class="ri-t small">{{ \Illuminate\Support\Str::limit($qq->text, 70) }}</div>@if (isset($stats[$qq->id]))<x-bar :pct="$stats[$qq->id]" :tone="$stats[$qq->id] < 50 ? 'cor' : 'ok'" />@endif</div><b class="small">{{ isset($stats[$qq->id]) && $stats[$qq->id] !== null ? $stats[$qq->id].' %' : '—' }}</b></div>
        @endforeach
      </x-panel>
    </div>
  @endif

  @if ($editQ)
    <x-modal :title="$qId ? 'Modifier la question' : 'Nouvelle question'" icon="fa-circle-question" size="modal-lg">
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label">Question</label><textarea class="form-control @error('qq.text') is-invalid @enderror" rows="2" wire:model="qq.text"></textarea>@error('qq.text')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div><label class="form-label">Réponses <span class="small muted">(cochez la bonne réponse)</span></label>
          @foreach ($qq['options'] as $i => $o)
            <div class="d-flex align-items-center gap-2 mb-2" wire:key="opt-{{ $i }}">
              <input type="radio" class="form-check-input mt-0" name="good" value="{{ $i }}" wire:model="qq.answer" aria-label="Bonne réponse">
              <b class="small">{{ chr(65 + $i) }}</b>
              <input class="form-control @error('qq.options.'.$i) is-invalid @enderror" wire:model="qq.options.{{ $i }}">
              <button type="button" class="btn btn-quiet btn-icon" wire:click="retirerOption({{ $i }})" @disabled(count($qq['options']) <= 2) aria-label="Retirer"><i class="fa-solid fa-xmark"></i></button>
            </div>
          @endforeach
          @if (count($qq['options']) < 6)<button type="button" class="btn btn-ghost btn-sm" wire:click="ajouterOption"><i class="fa-solid fa-plus"></i>Ajouter une réponse</button>@endif
        </div>
        <div><label class="form-label">Explication <span class="small muted">(affichée dans la correction)</span></label><textarea class="form-control" rows="2" wire:model="qq.explanation"></textarea></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerQuestion">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
