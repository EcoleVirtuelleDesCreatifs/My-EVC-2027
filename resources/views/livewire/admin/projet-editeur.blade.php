<div>
  <a class="btn btn-quiet btn-sm mb-3" href="{{ route('admin.projets', ['onglet' => 'catalogue']) }}" wire:navigate><i class="fa-solid fa-arrow-left"></i>Retour au catalogue</a>
  @if ($assigned)
    <div class="alert-strip as-warn mb-3">Ce projet est déjà attribué à {{ $assigned }} étudiant(s). Les modifications des consignes seront visibles sur leurs fiches. La formation ne peut plus être changée.</div>
  @endif
  <form wire:submit="enregistrer" x-data="evcProjectEditor($wire)" x-on:submit.capture="sync()" class="stack">
    <x-panel title="Informations du projet" sub="Choisissez la formation concernée, puis préparez les consignes et la publication.">
      <div class="row g-3">
        <div class="col-md-8"><label class="form-label" for="project-title">Titre du projet</label><input id="project-title" class="form-control @error('p.title') is-invalid @enderror" wire:model="p.title" maxlength="150" required>@error('p.title')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label class="form-label" for="project-type">Type / niveau</label><input id="project-type" class="form-control @error('p.type') is-invalid @enderror" wire:model="p.type" list="project-types" maxlength="60" required><datalist id="project-types"><option value="Débutant"><option value="Intermédiaire"><option value="Avancé"><option value="Mission complète"><option value="Projet"></datalist>@error('p.type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label" for="project-formation">Formation</label><select id="project-formation" class="form-select @error('p.formation_id') is-invalid @enderror" wire:model="p.formation_id" @disabled($assigned)>@foreach ($formations as $f)<option value="{{ $f->id }}">{{ $f->code }} — {{ $f->short_name }}</option>@endforeach</select>@error('p.formation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6 d-flex align-items-end"><div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" id="project-published" wire:model="p.published"><label class="form-check-label" for="project-published">Publié : disponible pour attribution</label><div class="small muted">Non coché : conserver le projet en brouillon.</div></div></div>
      </div>
    </x-panel>
    <x-panel title="Éditeur de consignes" sub="Structurez le brief, les objectifs, les livrables, les ressources et les critères d’évaluation.">
      <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
        <button type="button" class="btn btn-ghost btn-sm" @click="editing = true" :aria-pressed="editing">Écrire</button>
        <button type="button" class="btn btn-ghost btn-sm" @click="editing = false" :aria-pressed="!editing">Aperçu étudiant</button>
        <span class="small muted ms-auto"><span x-text="count">0</span> / 20 000 caractères</span>
      </div>
      <div class="project-editor" wire:ignore>
        <div class="project-editor-toolbar" role="toolbar" aria-label="Mise en forme des consignes" x-show="editing">
          <button type="button" @mousedown.prevent @click="format('bold')" title="Gras" aria-label="Gras"><strong>G</strong></button>
          <button type="button" @mousedown.prevent @click="format('italic')" title="Italique" aria-label="Italique"><em>I</em></button>
          <button type="button" @mousedown.prevent @click="format('underline')" title="Souligner" aria-label="Souligner"><u>S</u></button>
          <button type="button" @mousedown.prevent @click="format('formatBlock', 'h2')" title="Titre de section">Titre</button>
          <button type="button" @mousedown.prevent @click="format('formatBlock', 'h3')" title="Sous-titre">Sous-titre</button>
          <button type="button" @mousedown.prevent @click="format('formatBlock', 'p')">Paragraphe</button>
          <button type="button" @mousedown.prevent @click="format('insertUnorderedList')" title="Liste à puces" aria-label="Liste à puces"><i class="fa-solid fa-list-ul"></i></button>
          <button type="button" @mousedown.prevent @click="format('insertOrderedList')" title="Liste numérotée" aria-label="Liste numérotée"><i class="fa-solid fa-list-ol"></i></button>
          <button type="button" @mousedown.prevent @click="link()" title="Ajouter un lien" aria-label="Ajouter un lien"><i class="fa-solid fa-link"></i></button>
          <button type="button" @mousedown.prevent @click="format('unlink')" title="Retirer le lien" aria-label="Retirer le lien"><i class="fa-solid fa-link-slash"></i></button>
          <button type="button" @mousedown.prevent @click="format('removeFormat')" title="Effacer la mise en forme" aria-label="Effacer la mise en forme"><i class="fa-solid fa-eraser"></i></button>
          <button type="button" @mousedown.prevent @click="format('undo')" title="Annuler" aria-label="Annuler la dernière saisie"><i class="fa-solid fa-rotate-left"></i></button>
          <button type="button" @mousedown.prevent @click="format('redo')" title="Rétablir" aria-label="Rétablir"><i class="fa-solid fa-rotate-right"></i></button>
        </div>
        <x-project-sheet :title="$p['title']" :type="$p['type']" :editor="true">
          <div x-ref="body" class="project-editor-body project-brief" contenteditable="true" :contenteditable="editing" role="textbox" tabindex="0" aria-label="Consignes du projet" aria-multiline="true" :aria-readonly="!editing" aria-describedby="project-editor-help" @input="sync()" @paste="paste($event)" @drop.prevent @click="if (!editing && $event.target.closest('a')) $event.preventDefault()"></div>
        </x-project-sheet>
      </div>
      @error('p.brief')<div class="text-danger small mt-2" role="alert">{{ $message }}</div>@enderror
      <p id="project-editor-help" class="small muted mt-3 mb-0">La fiche reprend les deux colonnes du document : cliquez dans les cellules de droite pour rédiger les consignes. Le collage importe du texte simple pour éviter les mises en forme indésirables. Utilisez la barre d’outils pour le structurer. Les scripts, images et contenus intégrés ne sont pas acceptés.</p>
    </x-panel>
    <div class="d-flex flex-wrap gap-2 justify-content-end mb-4">
      <a class="btn btn-ghost" href="{{ route('admin.projets', ['onglet' => 'catalogue']) }}" wire:navigate>Annuler</a>
      <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="enregistrer"><i class="fa-solid fa-floppy-disk"></i><span wire:loading.remove wire:target="enregistrer">Enregistrer le projet</span><span wire:loading wire:target="enregistrer">Enregistrement…</span></button>
    </div>
  </form>
</div>
