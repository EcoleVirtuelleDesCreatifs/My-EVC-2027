@php($size = fn (int $b) => $b >= 1048576 ? number_format($b / 1048576, 1, ',', ' ').' Mo' : max(1, round($b / 1024)).' Ko')
<div class="stack">
  <section class="rp-hero">
    <span class="rp-hero-i"><i class="fa-solid fa-file-signature"></i></span>
    <div>
      <h2>Votre rapport de fin de formation</h2>
      <p class="mb-0">Chaque étudiant joint un rapport de fin de formation pour chacune de ses formations. Il est relu par l’équipe pédagogique, qui le valide ou vous demande de le compléter.@if ($s->promotion?->ends_on) À déposer au plus tard à la fin de votre session, le <b>{{ $s->promotion->ends_on->translatedFormat('j F Y') }}</b>.@endif</p>
    </div>
  </section>

  <div class="u-grid u-2-1 align-items-start">
    <div class="stack">
      @foreach ($s->formations as $f)
        @php($r = $reports->get($f->id))
        @php([$label, $tone] = \App\Models\FinalReport::label($r))
        <x-panel :title="$f->name">
          <x-slot:tools><x-pill :tone="$tone">{{ $label }}</x-pill></x-slot:tools>
          @if ($r)
            <a class="rp-file" href="{{ route('fichier.rapport', $r) }}">
              <span class="pf-dl-ico c-{{ $f->code }}"><i class="fa-solid {{ \App\Support\Portfolio::FORMATS[strtolower(pathinfo($r->original_name, PATHINFO_EXTENSION))][1] ?? 'fa-file' }}"></i></span>
              <span class="pf-dl-name"><b>{{ $r->original_name }}</b><small>Version {{ $r->version }} · {{ $size($r->size) }} · déposé le {{ $r->submitted_at->translatedFormat('j F Y à H\hi') }}</small></span>
              <i class="fa-solid fa-download muted"></i>
            </a>
            @if ($r->feedback)
              <div class="rp-feedback {{ $r->status === 'a_reprendre' ? 'bad' : 'ok' }}"><b><i class="fa-solid fa-comment-dots me-1"></i>Retour de l’équipe pédagogique</b>
                <p class="mb-0">{{ $r->feedback }}</p>@if ($r->reviewer)<small>{{ $r->reviewer->name }} · {{ $r->reviewed_at?->translatedFormat('j F Y') }}</small>@endif</div>
            @elseif ($r->status === 'valide')
              <p class="small text-success mb-0 mt-2"><i class="fa-solid fa-circle-check me-1"></i>Validé le {{ $r->reviewed_at?->translatedFormat('j F Y') }}. Félicitations !</p>
            @endif
          @else
            <p class="small muted mb-0">Aucun rapport déposé pour cette formation.</p>
          @endif
        </x-panel>
      @endforeach
    </div>

    <div class="stack">
      @php($open = $s->formations->filter(fn ($f) => ($reports->get($f->id)?->status) !== 'valide'))
      @if ($open->isNotEmpty())
        <x-panel title="Déposer mon rapport" sub="PDF, Word, OpenDocument ou PowerPoint · 20 Mo max.">
          <form wire:submit="deposer" class="d-flex flex-column gap-3">
            @if ($s->formations->count() > 1)
              <div><label class="form-label" for="rpF">Formation</label>
                <select class="form-select" id="rpF" wire:model="formationId">@foreach ($open as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach</select>@error('formationId')<div class="small text-danger">{{ $message }}</div>@enderror</div>
            @endif
            <div><label class="form-label" for="rpFile">Rapport</label>
              <input type="file" id="rpFile" class="form-control @error('fichier') is-invalid @enderror" wire:model="fichier" accept=".pdf,.doc,.docx,.odt,.ppt,.pptx">
              <div wire:loading wire:target="fichier" class="small muted mt-1"><i class="fa-solid fa-spinner fa-spin me-1"></i>Envoi du fichier…</div>
              @error('fichier')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
            <div><label class="form-label" for="rpMsg">Message pour l’équipe <span class="small muted">(facultatif)</span></label><textarea id="rpMsg" class="form-control" rows="3" wire:model="message"></textarea></div>
            @error('rapport')<div class="alert alert-danger small mb-0">{{ $message }}</div>@enderror
            <p class="small muted mb-0">Un nouveau dépôt remplace la version précédente tant que le rapport n’est pas validé.</p>
            <div><button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="deposer,fichier"><i class="fa-solid fa-cloud-arrow-up"></i>Déposer mon rapport</button></div>
          </form>
        </x-panel>
      @endif
      <x-panel title="Contenu conseillé">
        <ul class="small mb-0 ps-3">
          <li>Présentation de votre parcours et de vos objectifs de départ ;</li>
          <li>Projets réalisés pendant la formation et livrables marquants ;</li>
          <li>Compétences et outils acquis ;</li>
          <li>Difficultés rencontrées et solutions trouvées ;</li>
          <li>Bilan personnel et projet professionnel.</li>
        </ul>
      </x-panel>
    </div>
  </div>
</div>
