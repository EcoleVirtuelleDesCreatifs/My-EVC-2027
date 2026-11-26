@php($size = fn (?int $b) => ! $b ? null : ($b >= 1048576 ? number_format($b / 1048576, 1, ',', ' ').' Mo' : max(1, round($b / 1024)).' Ko'))
<div>
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item"><button class="nav-link {{ $onglet === 'classes' ? 'active' : '' }}" wire:click="$set('onglet','classes')"><i class="fa-brands fa-whatsapp me-1"></i>Classes virtuelles <span class="tab-n">{{ $classes->count() }}</span></button></li>
    <li class="nav-item"><button class="nav-link {{ $onglet === 'outils' ? 'active' : '' }}" wire:click="$set('onglet','outils')"><i class="fa-solid fa-toolbox me-1"></i>Outils de démarrage <span class="tab-n">{{ $tools->flatten()->count() }}</span></button></li>
  </ul>

  @if ($onglet === 'classes')
    <p class="small muted">Les étudiants voient les classes de leurs formations en haut de la page « Mes projets » et dans « Outils de démarrage ». Une classe peut être réservée à une session.</p>
    <x-panel :p0="true">
      @forelse ($classes as $v)
        @php([$pl, $pi] = $v->platform())
        <div class="row-item {{ $v->active ? '' : 'opacity-50' }}">
          <span class="icon-sq vc-ico"><i class="{{ $pi }}"></i></span>
          <div class="ri-main"><div class="ri-t">{{ $v->name }} <x-fchip :code="$v->formation->code" /> @unless ($v->active)<x-pill tone="mute" nodot>Masquée</x-pill>@endunless</div>
            <div class="ri-s">{{ collect([$pl, $v->promotion?->name ?? 'Toutes les sessions', $v->schedule])->filter()->implode(' · ') }} · <a href="{{ $v->url }}" target="_blank" rel="noopener" class="link-plain">{{ \Illuminate\Support\Str::limit($v->url, 48) }}</a></div></div>
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="basculerClasse({{ $v->id }})" aria-label="{{ $v->active ? 'Masquer' : 'Afficher' }}" title="{{ $v->active ? 'Masquer' : 'Afficher' }}"><i class="fa-solid {{ $v->active ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifierClasse({{ $v->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimerClasse({{ $v->id }})" wire:confirm="Supprimer cette classe virtuelle ?" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
        </div>
      @empty
        <div class="p-4"><x-empty title="Aucune classe virtuelle" text="Ajoutez le lien d’invitation de chaque groupe WhatsApp (ou visioconférence) pour que les étudiants le rejoignent en un clic."><button class="btn btn-primary btn-sm mt-2" wire:click="nouvelleClasse"><i class="fa-solid fa-plus"></i>Ajouter une classe</button></x-empty></div>
      @endforelse
    </x-panel>
  @else
    <p class="small muted">Logiciels à installer, supports et programmes de formation : en fichier hébergé par l’EVC ou en lien vers le site officiel de l’éditeur.</p>
    @foreach (\App\Models\StarterTool::KINDS as $k => [$plural, $one, $icon, $hint])
      <x-panel :title="$plural" :sub="$hint" :p0="true" class="mb-3">
        <x-slot:tools><button class="btn btn-ghost btn-sm" wire:click="nouvelOutil('{{ $k }}')"><i class="fa-solid fa-plus"></i>Ajouter</button></x-slot:tools>
        @forelse ($tools->get($k, collect()) as $t)
          <div class="row-item {{ $t->published ? '' : 'opacity-50' }}">
            <span class="icon-sq"><i class="fa-solid {{ $icon }}"></i></span>
            <div class="ri-main"><div class="ri-t">{{ $t->title }} @if ($t->formation)<x-fchip :code="$t->formation->code" />@else<x-pill tone="lav" nodot>Toutes formations</x-pill>@endif @unless ($t->published)<x-pill tone="mute" nodot>Masqué</x-pill>@endunless</div>
              <div class="ri-s">{{ collect([$t->version, $t->path ? $t->extension().' · '.$size($t->size) : null, $t->url ? 'lien officiel' : null, $t->downloads.' téléchargement'.($t->downloads > 1 ? 's' : '')])->filter()->implode(' · ') }}</div></div>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="basculerOutil({{ $t->id }})" aria-label="{{ $t->published ? 'Masquer' : 'Publier' }}"><i class="fa-solid {{ $t->published ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="modifierOutil({{ $t->id }})" aria-label="Modifier"><i class="fa-solid fa-pen"></i></button>
            <button class="btn btn-quiet btn-sm btn-icon" wire:click="supprimerOutil({{ $t->id }})" wire:confirm="Supprimer cet outil ?" aria-label="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
          </div>
        @empty
          <div class="px-4 py-3 small muted">Aucun élément pour le moment.</div>
        @endforelse
      </x-panel>
    @endforeach
  @endif

  @if ($classeForm)
    <x-modal :title="$classeId ? 'Modifier la classe virtuelle' : 'Nouvelle classe virtuelle'" icon="fa-chalkboard-user">
      <div class="row g-3">
        <div class="col-12"><label class="form-label" for="cn">Nom</label><input class="form-control @error('c.name') is-invalid @enderror" id="cn" wire:model="c.name" placeholder="Ex. Classe DG · Session Octobre 2026">@error('c.name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label" for="cu">Lien d’invitation</label><input class="form-control @error('c.url') is-invalid @enderror" id="cu" wire:model="c.url" placeholder="https://chat.whatsapp.com/…">@error('c.url')<div class="invalid-feedback">{{ $message }}</div>@enderror<div class="small muted mt-1">WhatsApp, Google Meet, Zoom, Teams ou Telegram : la plateforme est reconnue automatiquement.</div></div>
        <div class="col-sm-6"><label class="form-label" for="cf">Formation</label><select class="form-select" id="cf" wire:model="c.formation_id">@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label" for="cp">Session</label><select class="form-select" id="cp" wire:model="c.promotion_id"><option value="">Toutes les sessions</option>@foreach ($promotions as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label" for="cs">Horaires <span class="small muted">(facultatif)</span></label><input class="form-control" id="cs" wire:model="c.schedule" placeholder="Ex. Lundi et jeudi, 19 h – 21 h"></div>
        <div class="col-12"><label class="form-label" for="cd">Consignes <span class="small muted">(facultatif)</span></label><textarea class="form-control" id="cd" rows="2" wire:model="c.description" placeholder="Ex. Présentez-vous avec votre nom et votre matricule en rejoignant le groupe."></textarea></div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="ca" wire:model="c.active"><label class="form-check-label" for="ca">Visible par les étudiants</label></div></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerClasse" wire:loading.attr="disabled"><i class="fa-solid fa-check"></i>Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif

  @if ($outilForm)
    @php($current = $outilId ? \App\Models\StarterTool::find($outilId) : null)
    <x-modal :title="$outilId ? 'Modifier l’outil' : 'Nouvel outil de démarrage'" icon="fa-toolbox">
      <div class="row g-3">
        <div class="col-sm-6"><label class="form-label" for="ok">Rubrique</label><select class="form-select" id="ok" wire:model="o.kind">@foreach (\App\Models\StarterTool::KINDS as $k => $kind)<option value="{{ $k }}">{{ $kind[0] }}</option>@endforeach</select></div>
        <div class="col-sm-6"><label class="form-label" for="of">Formation</label><select class="form-select" id="of" wire:model="o.formation_id"><option value="">Toutes les formations</option>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select></div>
        <div class="col-sm-8"><label class="form-label" for="ot">Titre</label><input class="form-control @error('o.title') is-invalid @enderror" id="ot" wire:model="o.title" placeholder="Ex. Adobe Photoshop, Programme DG…">@error('o.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-sm-4"><label class="form-label" for="ov">Version / système</label><input class="form-control" id="ov" wire:model="o.version" placeholder="Ex. 2025 · Windows et macOS"></div>
        <div class="col-12"><label class="form-label" for="od">Description</label><textarea class="form-control" id="od" rows="2" wire:model="o.description" placeholder="À quoi sert-il, comment l’installer…"></textarea></div>
        <div class="col-12"><label class="form-label" for="ofi">Fichier <span class="small muted">(PDF, ZIP, installateur… 500 Mo max.)</span></label>
          @if ($current?->path)<div class="d-flex align-items-center gap-2 mb-2 small"><i class="fa-solid fa-paperclip"></i><span class="flex-grow-1 text-truncate">{{ $current->original_name }} · {{ $size($current->size) }}</span>@if ($current->url)<button type="button" class="btn btn-ghost btn-sm" wire:click="retirerFichierOutil">Retirer</button>@endif</div>@endif
          <input class="form-control @error('fichier') is-invalid @enderror" type="file" id="ofi" wire:model="fichier">
          <div wire:loading wire:target="fichier" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi du fichier…</div>
          @error('fichier')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
        <div class="col-12"><label class="form-label" for="ou">Lien de téléchargement <span class="small muted">(site officiel, Drive… ; conseillé pour les logiciels)</span></label><input class="form-control @error('o.url') is-invalid @enderror" id="ou" wire:model="o.url" placeholder="https://">@error('o.url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="op" wire:model="o.published"><label class="form-check-label" for="op">Publié pour les étudiants</label></div></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerOutil" wire:loading.attr="disabled" wire:target="enregistrerOutil,fichier"><i class="fa-solid fa-check"></i>Enregistrer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
