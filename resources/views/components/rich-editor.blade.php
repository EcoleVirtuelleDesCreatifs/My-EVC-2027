@props(['field', 'label' => 'Texte'])
<div {{ $attributes }} x-data="evcProjectEditor($wire, @js($field))">
  <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
    <span class="form-label mb-0">{{ $label }}</span>
    <button type="button" class="btn btn-quiet btn-sm ms-auto" @click="editing = !editing" :aria-pressed="!editing" x-text="editing ? 'Aperçu' : 'Écrire'"></button>
    <span class="small muted"><span x-text="count">0</span> caractères</span>
  </div>
  <div class="project-editor" wire:ignore>
    <div class="project-editor-toolbar" role="toolbar" aria-label="Mise en forme du texte" x-show="editing">
      <button type="button" @mousedown.prevent @click="format('bold')" title="Gras" aria-label="Gras"><strong>G</strong></button>
      <button type="button" @mousedown.prevent @click="format('italic')" title="Italique" aria-label="Italique"><em>I</em></button>
      <button type="button" @mousedown.prevent @click="format('underline')" title="Souligner" aria-label="Souligner"><u>S</u></button>
      <button type="button" @mousedown.prevent @click="format('formatBlock', 'h2')" title="Titre de section">Titre</button>
      <button type="button" @mousedown.prevent @click="format('formatBlock', 'h3')">Sous-titre</button>
      <button type="button" @mousedown.prevent @click="format('formatBlock', 'p')">Paragraphe</button>
      <button type="button" @mousedown.prevent @click="format('insertUnorderedList')" title="Liste à puces" aria-label="Liste à puces"><i class="fa-solid fa-list-ul"></i></button>
      <button type="button" @mousedown.prevent @click="format('insertOrderedList')" title="Liste numérotée" aria-label="Liste numérotée"><i class="fa-solid fa-list-ol"></i></button>
      <button type="button" @mousedown.prevent @click="format('formatBlock', 'blockquote')" title="Citation">Citation</button>
      <button type="button" @mousedown.prevent @click="link()" title="Ajouter un lien" aria-label="Ajouter un lien"><i class="fa-solid fa-link"></i></button>
      <button type="button" @mousedown.prevent @click="format('unlink')" title="Retirer le lien" aria-label="Retirer le lien"><i class="fa-solid fa-link-slash"></i></button>
      <button type="button" @mousedown.prevent @click="format('removeFormat')" title="Effacer la mise en forme" aria-label="Effacer la mise en forme"><i class="fa-solid fa-eraser"></i></button>
      <button type="button" @mousedown.prevent @click="format('undo')" title="Annuler" aria-label="Annuler la dernière saisie"><i class="fa-solid fa-rotate-left"></i></button>
      <button type="button" @mousedown.prevent @click="format('redo')" title="Rétablir" aria-label="Rétablir"><i class="fa-solid fa-rotate-right"></i></button>
    </div>
    <div x-ref="body" class="project-editor-body project-brief" contenteditable="true" :contenteditable="editing" role="textbox" tabindex="0" aria-label="{{ $label }}" aria-multiline="true" :aria-readonly="!editing" @input="sync()" @paste="paste($event)" @drop.prevent @click="if (!editing && $event.target.closest('a')) $event.preventDefault()"></div>
  </div>
  @error($field)<div class="text-danger small mt-2" role="alert">{{ $message }}</div>@enderror
  <p class="small muted mt-2 mb-0">20 000 caractères HTML maximum. Le collage importe du texte simple ; utilisez la barre d’outils pour le mettre en forme. Les scripts et contenus intégrés ne sont pas acceptés.</p>
</div>
