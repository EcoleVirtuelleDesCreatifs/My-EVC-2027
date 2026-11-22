@props(['title' => '', 'type' => 'Projet', 'editor' => false])
@php($heading = \App\Support\ProjectBrief::heading($title))
<article class="project-sheet" data-level="{{ mb_strtolower($type) }}" @if ($editor) :data-level="($wire.p.type || 'Projet').toLowerCase()" @endif>
  <header class="project-sheet-header">
    <span class="project-sheet-code" @if ($editor) x-text="sheetCode" @endif>{{ $heading['code'] }}</span>
    <h3 class="project-sheet-title" @if ($editor) x-text="sheetTitle" @endif>{{ $heading['title'] ?: 'Nouveau projet' }}</h3>
    <span class="project-sheet-level" @if ($editor) x-text="$wire.p.type || 'Projet'" @endif>{{ $type }}</span>
  </header>
  <div class="project-sheet-content project-brief">{{ $slot }}</div>
  <footer class="project-sheet-footer"><strong>EVC</strong><span>École Virtuelle des Créatifs · Catalogue des projets</span></footer>
</article>
