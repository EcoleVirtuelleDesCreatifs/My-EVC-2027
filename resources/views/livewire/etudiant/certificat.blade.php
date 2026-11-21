<div>
  <p class="muted mb-4">À l'EVC, le certificat se mérite : il est délivré quand toutes les conditions ci-dessous sont remplies, après délibération du jury.</p>
  <div class="u-grid" style="grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px">
    @foreach ($rows as $row)
      @php($ok = collect($row['checks'])->filter()->count())
      <x-panel :title="$row['f']->name" class="c-{{ $row['f']->code }}">
        <x-slot:tools><x-fchip :code="$row['f']->code" /></x-slot:tools>
        @if ($row['cert'])
          <div class="alert-strip as-ok mb-3"><i class="fa-solid fa-award"></i><span>Certificat délivré le {{ $row['cert']->issued_on->translatedFormat('j F Y') }} — {{ $row['cert']->appreciation }}</span></div>
          <a class="btn btn-primary w-100 mb-2" href="{{ route('pdf.certificat', $row['cert']) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>Télécharger mon certificat</a>
          <p class="small muted mb-0">Référence de vérification : <span class="mono">{{ $row['cert']->code }}</span></p>
        @else
          <div class="steps-line mb-3">@foreach ($row['checks'] as $done)<span class="{{ $done ? 'on' : '' }}"></span>@endforeach</div>
          <ul class="list-unstyled d-flex flex-column gap-2 mb-3">
            @foreach ($row['checks'] as $label => $done)
              <li class="d-flex gap-2 align-items-center small"><i class="fa-solid {{ $done ? 'fa-circle-check text-success' : 'fa-circle muted' }}"></i>{{ $label }}</li>
            @endforeach
          </ul>
          <x-pill :tone="$ok === count($row['checks']) ? 'ok' : 'mute'">{{ $ok === count($row['checks']) ? 'Éligible : en attente du jury' : $ok.' / '.count($row['checks']).' conditions remplies' }}</x-pill>
        @endif
      </x-panel>
    @endforeach
  </div>
  <x-panel title="Documents" class="mt-4">
    <div class="row-item px-0"><span class="icon-sq"><i class="fa-solid fa-file-pdf"></i></span><div class="ri-main"><div class="ri-t">Attestation d'inscription</div><div class="ri-s">Générée automatiquement, avec votre matricule</div></div>
      <a class="btn btn-ghost btn-sm" href="{{ route('pdf.attestation', $s) }}" target="_blank"><i class="fa-solid fa-download"></i>Télécharger</a></div>
  </x-panel>
</div>
