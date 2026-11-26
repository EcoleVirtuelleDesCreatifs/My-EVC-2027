<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Vidéos" :value="$stats['total']" icon="fa-tv" tone="lav" />
    <x-kpi label="Vues" :value="number_format($stats['vues'], 0, ',', ' ')" icon="fa-eye" tone="info" />
    <x-kpi label="Replays à publier" :value="$sansReplay->count()" icon="fa-clapperboard" :tone="$sansReplay->count() ? 'cor' : 'ok'" sub="Séances des 3 dernières semaines" />
    <x-kpi label="Formations" :value="$formations->count()" icon="fa-book-open" tone="ok" />
  </div>
  @if ($sansReplay->isNotEmpty())
    <x-panel title="Séances sans replay" :p0="true" class="mb-4">
      @foreach ($sansReplay as $se)
        <div class="row-item"><x-fchip :code="$se->formation->code" /><div class="ri-main"><div class="ri-t">{{ $se->title }}</div><div class="ri-s">{{ $se->starts_at->translatedFormat('l j F') }}</div></div><button class="btn btn-ghost btn-sm" wire:click="creer({{ $se->id }})"><i class="fa-solid fa-upload"></i>Publier le replay</button></div>
      @endforeach
    </x-panel>
  @endif
  <div class="filters-row mb-3">
    @if ($formations->count() > 1)<select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select>@endif
    <button class="btn btn-primary d-sm-none" wire:click="creer"><i class="fa-solid fa-plus"></i>Ajouter une vidéo</button>
  </div>
  <div class="panel tbl"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Vidéo</th><th>Module</th><th>Source</th><th class="text-end">Vues</th><th class="text-end">Vues complètes</th><th>Statut</th><th></th></tr></thead><tbody>
    @forelse ($items as $x)
      <tr><td><x-fchip :code="$x->formation->code" /> <span class="fw-sb">{{ $x->title }}</span><div class="small muted">{{ $x->seance ? 'Séance du '.$x->seance->starts_at->translatedFormat('j M') : $x->created_at->translatedFormat('j M Y') }}{{ $x->duration_min ? ' · '.$x->durationLabel() : '' }}</div></td>
        <td class="small">{{ $x->module }}</td><td class="small">{{ ['fichier' => 'Fichier', 'youtube' => 'YouTube', 'vimeo' => 'Vimeo'][$x->source] }}</td>
        <td class="text-end">{{ $x->views_count }}</td><td class="text-end">{{ $x->vues_completes }}</td>
        <td>@if ($x->published)<x-pill tone="ok">Publiée</x-pill>@else<x-pill>Brouillon</x-pill>@endif</td>
        <td class="table-actions">
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifier({{ $x->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="publier({{ $x->id }})" aria-label="{{ $x->published ? 'Masquer' : 'Publier' }}"><i class="fa-solid {{ $x->published ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimer({{ $x->id }})" wire:confirm="Supprimer cette vidéo ?" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button></td></tr>
    @empty
      <tr><td colspan="7"><x-empty title="Aucune vidéo" text="Publiez les replays des séances et vos tutoriels." /></td></tr>
    @endforelse
  </tbody></table></div></div>
  <div class="mt-3">{{ $items->links() }}</div>

  @if ($form)
    <x-modal :title="$editId ? 'Modifier la vidéo' : 'Ajouter une vidéo'" icon="fa-tv" size="modal-lg">
      <div class="row g-3">
        <div class="col-sm-4"><label class="form-label">Formation</label><select class="form-select" wire:model.live="v.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach</select></div>
        <div class="col-sm-4"><label class="form-label">Module</label><select class="form-select" wire:model="v.module">@foreach (\App\Models\Video::MODULES as $m)<option>{{ $m }}</option>@endforeach</select></div>
        <div class="col-sm-4"><label class="form-label">Séance <span class="small muted">(replay)</span></label><select class="form-select" wire:model="v.seance_id"><option value="">—</option>@foreach ($seances as $se)<option value="{{ $se->id }}">{{ $se->starts_at->translatedFormat('j M') }} — {{ \Illuminate\Support\Str::limit($se->title, 40) }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('v.title') is-invalid @enderror" wire:model="v.title">@error('v.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" rows="2" wire:model="v.description" placeholder="Ce que couvre la vidéo, les passages clés…"></textarea></div>
        <div class="col-12"><span class="form-label d-block">Source</span><div class="lib-src" style="grid-template-columns:repeat(3,1fr)">
          @foreach (['fichier' => 'Fichier vidéo (MP4)', 'youtube' => 'YouTube (non répertoriée)', 'vimeo' => 'Vimeo'] as $k => $l)<label class="form-check-card"><input class="form-check-input me-2" type="radio" value="{{ $k }}" wire:model.live="v.source">{{ $l }}</label>@endforeach</div></div>
        @if (($v['source'] ?? '') === 'fichier')
          <div class="col-12"><label class="form-label">Fichier @if ($editId)<span class="small muted">(laisser vide pour garder la vidéo actuelle)</span>@endif</label><input type="file" class="form-control @error('fichier') is-invalid @enderror" wire:model="fichier" accept="video/mp4,video/webm,video/quicktime">
            <div wire:loading wire:target="fichier" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi de la vidéo…</div>@error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">Jusqu'à 500 Mo. Pour les longs replays, une vidéo YouTube « non répertoriée » est plus légère pour les étudiants.</div></div>
        @else
          <div class="col-12"><label class="form-label">Lien de la vidéo</label><input class="form-control @error('v.url') is-invalid @enderror" wire:model.live.debounce.500ms="v.url" placeholder="https://">@error('v.url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          @if (($v['source'] ?? '') === 'youtube')
            <div class="col-12">
              <button type="button" class="btn btn-ghost btn-sm" wire:click="verifierYoutube" wire:loading.attr="disabled" wire:target="verifierYoutube"><i class="fa-solid fa-play" aria-hidden="true"></i><span wire:loading.remove wire:target="verifierYoutube">Vérifier et prévisualiser sur YouTube</span><span wire:loading wire:target="verifierYoutube">Vérification…</span></button>
              <p class="small muted mt-2 mb-0">Utilisez le lien d’une vidéo précise, même non répertoriée. Les liens watch, youtu.be, Shorts et directs sont acceptés. Vérifiez que le propriétaire autorise l’intégration ; certaines restrictions d’âge, de région ou de droits peuvent empêcher la lecture ici.</p>
              @php($previewVideo = new \App\Models\Video(['source' => 'youtube', 'url' => $v['url'] ?? '']))
              @if ($apercuYoutube && $previewVideo->embedUrl() && ($apercuYoutube['url'] ?? null) === $previewVideo->externalUrl())
                <div class="alert-strip as-info mt-3">
                  <b>Vidéo identifiée par YouTube : {{ $apercuYoutube['title'] }}</b>
                  <p class="small mb-2">Chaîne : {{ $apercuYoutube['author'] }}. Assurez-vous que cette vidéo correspond bien à votre cours.</p>
                  <div class="ratio ratio-16x9 mb-2"><iframe src="{{ $previewVideo->embedUrl() }}" title="Aperçu : {{ $apercuYoutube['title'] }}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share; fullscreen" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>
                  <a class="btn btn-ghost btn-sm" href="{{ $previewVideo->externalUrl() }}" target="_blank" rel="noopener noreferrer">Ouvrir sur YouTube<i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                  <p class="small mt-2 mb-0">Lancez l’aperçu avant de publier. Une vérification réussie du titre ne garantit pas la lecture pour tous les navigateurs et toutes les régions.</p>
                </div>
              @endif
            </div>
          @endif
        @endif
        <div class="col-sm-4"><label class="form-label">Durée (min)</label><input type="number" class="form-control" wire:model="v.duration_min" placeholder="Auto pour un fichier"></div>
        <div class="col-sm-8 d-flex flex-column justify-content-end gap-1">
          <div class="form-check"><input class="form-check-input" type="checkbox" id="vp" wire:model="v.published"><label class="form-check-label" for="vp">Publiée : visible par les étudiants</label></div>
          @unless ($editId)<div class="form-check"><input class="form-check-input" type="checkbox" id="vn" wire:model="notifier"><label class="form-check-label" for="vn">Prévenir les étudiants</label></div>@endunless
        </div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer" wire:loading.attr="disabled" wire:target="enregistrer,fichier">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
