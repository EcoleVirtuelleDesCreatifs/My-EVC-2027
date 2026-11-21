@php($pub = $all->where('published', true))
@php($top = $all->sortByDesc('downloads_count')->first())
<div>
  <div class="lib-kpis">
    <x-kpi label="Ressources publiées" :value="$pub->count()" icon="fa-book-bookmark" tone="lav" :sub="($all->count() - $pub->count()).' brouillon(s)'" />
    <x-kpi label="Téléchargements" :value="number_format($all->sum('downloads_count'), 0, ',', ' ')" icon="fa-download" tone="cor" sub="Depuis la rentrée" />
    <x-kpi label="Ajoutées ce mois" :value="$all->where('created_at', '>=', now()->subDays(30))->count()" icon="fa-plus" tone="ok" sub="30 derniers jours" />
    <x-kpi label="La plus téléchargée" :value="$top ? number_format($top->downloads_count, 0, ',', ' ') : '—'" icon="fa-trophy" tone="warn" :sub="$top?->title" />
  </div>
  <div class="filters-row mb-3">
    <label class="search-field flex-grow-1 mb-0" style="min-width:220px"><i class="fa-solid fa-magnifying-glass"></i><input class="form-control" style="background:var(--surface)" placeholder="Rechercher une ressource…" wire:model.live.debounce.300ms="q"></label>
    @if (! $trainer)<select class="form-select" wire:model.live="formation" aria-label="Formation"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->short_name }}</option>@endforeach<option value="ALL">Communes à toutes</option></select>@endif
    <select class="form-select" wire:model.live="rayon" aria-label="Rayon"><option value="">Tous les rayons</option>@foreach (\App\Models\Resource::CATEGORIES as $k => $c)<option value="{{ $k }}">{{ $c[0] }}</option>@endforeach</select>
    <select class="form-select" wire:model.live="statut" aria-label="Statut"><option value="">Tous les statuts</option><option value="pub">Publiées</option><option value="brouillon">Brouillons</option></select>
    <button class="btn btn-primary d-sm-none" wire:click="creer"><i class="fa-solid fa-plus"></i>Ajouter</button>
  </div>
  <div class="panel tbl" wire:loading.class="wire-loading">
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Ressource</th><th>Rayon</th><th>Formation</th><th>Licence</th><th class="text-end">Téléch.</th><th>Ajoutée</th><th>Statut</th><th></th></tr></thead><tbody>
      @forelse ($items as $x)
        <tr>
          <td><div class="lib-adm-t"><x-book :r="$x" size="xs" /><div class="lr-m"><b title="{{ $x->title }}">{{ $x->title }}</b><small>{{ $x->format }}{{ $x->size ? ' · '.$x->size : '' }}{!! $x->essential ? ' · <i class="fa-solid fa-star"></i> Indispensable' : '' !!}</small></div></div></td>
          <td class="small">{{ $x->categoryLabel() }}</td>
          <td>@if ($x->formation)<x-fchip :code="$x->formation->code" />@else<x-pill tone="mute" nodot>Toutes</x-pill>@endif</td>
          <td class="small">{{ $x->licence }}</td>
          <td class="text-end">{{ number_format($x->downloads_count, 0, ',', ' ') }}</td>
          <td class="small text-nowrap">{{ $x->created_at->translatedFormat('j M') }}</td>
          <td>@if ($x->published)<x-pill tone="ok">Publiée</x-pill>@else<x-pill>Brouillon</x-pill>@endif</td>
          <td class="table-actions">
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifier({{ $x->id }})" title="Modifier" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="publier({{ $x->id }})" title="{{ $x->published ? 'Repasser en brouillon' : 'Publier' }}" aria-label="{{ $x->published ? 'Repasser en brouillon' : 'Publier' }}"><i class="fa-solid {{ $x->published ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimer({{ $x->id }})" wire:confirm="Supprimer « {{ $x->title }} » de la bibliothèque ? Cette action est définitive." title="Supprimer" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
          </td>
        </tr>
      @empty
        <tr><td colspan="8"><x-empty title="Aucune ressource" text="Ajoutez des logiciels, e-books, polices, modèles ou supports de cours." /></td></tr>
      @endforelse
    </tbody></table></div>
  </div>
  <div class="mt-3">{{ $items->links() }}</div>

  @if ($form)
    <x-modal :title="$editId ? 'Modifier la ressource' : 'Ajouter une ressource'" icon="fa-book-bookmark" size="modal-lg">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('r.title') is-invalid @enderror" wire:model="r.title" placeholder="Ex. : Pack de polices pour l'affiche">@error('r.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-6"><label class="form-label">Rayon</label><select class="form-select" wire:model="r.category">@foreach (\App\Models\Resource::CATEGORIES as $k => $c)<option value="{{ $k }}">{{ $c[0] }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label">Formation</label><select class="form-select @error('r.formation_id') is-invalid @enderror" wire:model="r.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach @unless ($trainer)<option value="">Toutes les formations</option>@endunless</select>@error('r.formation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label">Description</label><textarea class="form-control @error('r.description') is-invalid @enderror" rows="2" wire:model="r.description" maxlength="300" placeholder="À quoi sert cette ressource, en une ou deux phrases."></textarea>@error('r.description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><span class="form-label d-block">Source</span><div class="lib-src">
          <label class="form-check-card"><input class="form-check-input me-2" type="radio" value="fichier" wire:model.live="r.source">Fichier hébergé par l'EVC</label>
          <label class="form-check-card"><input class="form-check-input me-2" type="radio" value="lien" wire:model.live="r.source">Lien vers le site officiel</label></div></div>
        @if (($r['source'] ?? 'fichier') === 'fichier')
          <div class="col-12"><label class="form-label">Fichier @if ($editId)<span class="small muted">(laisser vide pour garder le fichier actuel)</span>@endif</label>
            <input type="file" class="form-control @error('fichier') is-invalid @enderror" wire:model="fichier">
            <div wire:loading wire:target="fichier" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi du fichier…</div>
            @error('fichier')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @else
          <div class="col-12"><label class="form-label">Adresse du site officiel</label><input class="form-control @error('r.url') is-invalid @enderror" type="url" wire:model="r.url" placeholder="https://">@error('r.url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        @endif
        <div class="col-sm-4"><label class="form-label">Format</label><input class="form-control" wire:model="r.format" placeholder="PDF, ZIP · TTF…"></div>
        <div class="col-sm-4"><label class="form-label">Taille</label><input class="form-control" wire:model="r.size" placeholder="4,2 Mo"></div>
        <div class="col-sm-4"><label class="form-label">Licence</label><select class="form-select" wire:model="r.licence">@foreach (\App\Models\Resource::LICENCES as $l)<option>{{ $l }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Compatible <span class="small muted">(facultatif)</span></label><input class="form-control" wire:model="r.compatible" placeholder="Windows · macOS · mobile"></div>
        <div class="col-12 d-flex flex-column gap-2">
          <div class="form-check"><input class="form-check-input" type="checkbox" id="key" wire:model="r.essential"><label class="form-check-label" for="key">Indispensable : afficher dans « Pour bien démarrer »</label></div>
          <div class="form-check"><input class="form-check-input" type="checkbox" id="pubr" wire:model="r.published"><label class="form-check-label" for="pubr">Publier : visible tout de suite par les étudiants</label></div>
          @unless ($editId)<div class="form-check"><input class="form-check-input" type="checkbox" id="ntf" wire:model="notifier"><label class="form-check-label" for="ntf">Prévenir les étudiants concernés</label></div>@endunless
        </div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer" wire:loading.attr="disabled" wire:target="enregistrer,fichier"><i class="fa-solid fa-check"></i>{{ $editId ? 'Enregistrer' : 'Ajouter' }}</button></x-slot:footer>
    </x-modal>
  @endif
</div>
