<div>
  <button class="btn btn-primary d-sm-none mb-3" wire:click="creer"><i class="fa-solid fa-plus"></i>Nouvelle actualité</button>
  @if ($items->isNotEmpty())
    <div class="stat-grid mb-3">
      <x-kpi label="Lecteurs" :value="number_format($items->sum('views'), 0, ',', ' ')" icon="fa-eye" tone="info" sub="toutes actualités confondues" />
      <x-kpi label="La plus lue" :value="number_format((int) $items->max('views'), 0, ',', ' ')" icon="fa-fire" tone="cor" :sub="\Illuminate\Support\Str::limit($items->sortByDesc('views')->first()->title, 48)" />
    </div>
  @endif
  <x-panel :p0="true">
    @forelse ($items as $x)
      <div class="row-item">
        <img src="{{ $x->cover }}" alt="" style="width:72px;height:48px;object-fit:cover;border-radius:8px">
        <div class="ri-main"><div class="ri-t">{{ $x->title }} @if ($x->featured)<x-pill tone="acc" nodot>À la une</x-pill>@endif</div><div class="ri-s">{{ $x->category }} · {{ $x->published_at ? 'publiée le '.$x->published_at->translatedFormat('j M Y') : 'brouillon' }} @if ($x->newsletter_sent_at)· <i class="fa-solid fa-paper-plane"></i> newsletter envoyée le {{ $x->newsletter_sent_at->translatedFormat('j M Y') }}@endif</div></div>
        <span class="news-readers" title="Lecteurs uniques : une visite par personne et par session, robots et équipe exclus"><i class="fa-solid fa-eye"></i><b>{{ number_format($x->views, 0, ',', ' ') }}</b> lecteur{{ $x->views > 1 ? 's' : '' }}</span>
        @if ($x->published_at)<a class="btn btn-quiet btn-sm btn-icon" href="{{ route('site.actualite', $x) }}" target="_blank" aria-label="Voir sur le site"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>@endif
        <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifier({{ $x->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
        <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimer({{ $x->id }})" wire:confirm="Supprimer cette actualité ?" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
      </div>
    @empty
      <div class="p-4"><x-empty title="Aucune actualité" /></div>
    @endforelse
  </x-panel>

  @if ($form)
    <x-modal :title="$editId ? 'Modifier l\'actualité' : 'Nouvelle actualité'" icon="fa-newspaper" size="modal-xl">
      <div class="row g-3">
        <div class="col-12"><label class="form-label">Titre</label><input class="form-control @error('n.title') is-invalid @enderror" wire:model="n.title">@error('n.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-4"><label class="form-label">Catégorie</label><input class="form-control" wire:model="n.category" list="cats"><datalist id="cats">@foreach (['Vie de l\'école', 'Partenariats', 'Studio Creative', 'Événements', 'Engagement', 'Résultats', 'Innovation'] as $c)<option value="{{ $c }}">@endforeach</datalist></div>
        <div class="col-sm-4"><label class="form-label">Étiquette <span class="small muted">(ex. « Promo 2026 »)</span></label><input class="form-control" wire:model="n.label"></div>
        <div class="col-sm-4"><label class="form-label">Date de publication</label><input class="form-control" type="date" wire:model="n.date"></div>
        <div class="col-12"><label class="form-label">Chapeau</label><textarea class="form-control @error('n.excerpt') is-invalid @enderror" rows="2" wire:model="n.excerpt"></textarea>@error('n.excerpt')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><x-rich-editor field="n.body" label="Texte de l’actualité" wire:key="news-editor-{{ $editId ?? 'new' }}" /></div>
        <div class="col-sm-6"><label class="form-label">Image de couverture</label><input type="file" class="form-control @error('couverture') is-invalid @enderror" wire:model="couverture" accept="image/*">@error('couverture')<div class="invalid-feedback">{{ $message }}</div>@enderror
          @if ($couverture)<img src="{{ $couverture->temporaryUrl() }}" alt="" class="mt-2 rounded" style="max-height:110px">@endif</div>
        <div class="col-sm-6 d-flex flex-column gap-2 justify-content-end">
          <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="pb" wire:model.live="n.publie"><label class="form-check-label" for="pb">Publiée sur le site</label></div>
          <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="ft" wire:model="n.featured"><label class="form-check-label" for="ft">À la une</label></div>
          @if ($n['publie'] ?? false)
            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="nl" wire:model="n.newsletter"><label class="form-check-label" for="nl">Notifier les abonnés newsletter <span class="small muted">({{ \App\Models\NewsletterSubscriber::actifs()->count() }} abonné{{ \App\Models\NewsletterSubscriber::actifs()->count() > 1 ? 's' : '' }})</span></label></div>
            @if (($n['newsletter'] ?? false) && $editId && ($sent = \App\Models\News::find($editId)?->newsletter_sent_at))<div class="small muted"><i class="fa-solid fa-circle-info"></i> Un envoi a déjà été fait le {{ $sent->translatedFormat('j M Y à H:i') }} — l'enregistrement en fera un nouveau.</div>@endif
          @endif
        </div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrer" wire:loading.attr="disabled" wire:target="enregistrer,couverture">Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
