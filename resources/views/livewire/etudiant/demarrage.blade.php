@php($size = fn (?int $b) => ! $b ? null : ($b >= 1048576 ? number_format($b / 1048576, 1, ',', ' ').' Mo' : max(1, round($b / 1024)).' Ko'))
<div class="stack">
  <section class="st-hero">
    <div class="st-hero-t">
      <p class="pf-eyebrow mt-0">Bien démarrer à l’EVC</p>
      <h2>Vos outils de démarrage</h2>
      <p class="mb-0">Installez vos logiciels, téléchargez vos supports et le programme de votre formation, puis rejoignez votre classe virtuelle : vous êtes prêt pour le premier cours.</p>
    </div>
    <ol class="st-steps">
      @foreach ([['fa-laptop-code', 'Installer', 'les logiciels'], ['fa-file-arrow-down', 'Télécharger', 'supports et programme'], ['fa-users', 'Rejoindre', 'la classe virtuelle']] as $i => [$ic, $t, $sub])
        <li><span><i class="fa-solid {{ $ic }}"></i></span><b>{{ $i + 1 }}. {{ $t }}</b><small>{{ $sub }}</small></li>
      @endforeach
    </ol>
  </section>

  @include('livewire.etudiant._classes')

  @php($empty = $tools->isEmpty())
  @foreach (\App\Models\StarterTool::KINDS as $k => [$plural, $one, $icon, $hint])
    @continue(! $tools->has($k))
    <section>
      <div class="st-sec"><span class="st-sec-i"><i class="fa-solid {{ $icon }}"></i></span><div><h3>{{ $plural }}</h3><small class="muted">{{ $hint }}</small></div></div>
      <div class="st-grid">
        @foreach ($tools[$k] as $t)
          <article class="st-card {{ $t->formation ? 'c-'.$t->formation->code : '' }}">
            <div class="st-card-top"><span class="st-ico"><i class="fa-solid {{ $k === 'logiciel' ? 'fa-laptop-code' : ($t->extension() === 'PDF' || ! $t->path ? $icon : 'fa-file-zipper') }}"></i></span>
              <div class="min-w-0"><b>{{ $t->title }}</b><small>{{ collect([$t->version, $t->formation?->short_name ?? 'Toutes formations'])->filter()->implode(' · ') }}</small></div></div>
            @if ($t->description)<p>{{ $t->description }}</p>@endif
            <div class="st-actions">
              @if ($t->path)<a class="btn btn-primary btn-sm" href="{{ route('fichier.outil', $t) }}"><i class="fa-solid fa-download"></i>Télécharger <span class="opacity-75">· {{ $t->extension() }} {{ $size($t->size) }}</span></a>@endif
              @if ($t->url)<a class="btn {{ $t->path ? 'btn-ghost' : 'btn-primary' }} btn-sm" href="{{ $t->url }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i>{{ $k === 'logiciel' ? 'Site officiel' : 'Ouvrir le lien' }}</a>@endif
            </div>
          </article>
        @endforeach
      </div>
    </section>
  @endforeach

  @if ($empty && $classes->isEmpty())
    <x-empty title="Vos outils arrivent bientôt" text="L’équipe pédagogique ajoutera ici les logiciels, supports et programmes de votre formation." />
  @endif
</div>
