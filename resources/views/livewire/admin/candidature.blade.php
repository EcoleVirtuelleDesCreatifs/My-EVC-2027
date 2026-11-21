<div class="u-grid u-2-1 align-items-start">
  <div class="stack">
    <x-panel :title="$c->full_name">
      <x-slot:tools><x-pill :tone="$c->stageTone()">{{ $c->stageLabel() }}</x-pill></x-slot:tools>
      <div class="d-flex flex-wrap gap-3 small mb-3">
        <span class="mono">{{ $c->reference }}</span><span><i class="fa-solid fa-envelope me-1"></i>{{ $c->email }}</span>
        <span><i class="fa-solid fa-phone me-1"></i>{{ $c->phone }}</span><span><i class="fa-regular fa-clock me-1"></i>{{ $c->created_at->translatedFormat('j F Y à H\hi') }}</span>
      </div>
      <div class="d-flex gap-2 flex-wrap mb-3">@foreach ($c->formations as $code)<x-fchip :code="$code" />@endforeach <span class="small muted">{{ $c->mode }}</span></div>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $c->phone) }}?text={{ rawurlencode('Bonjour '.$c->first_name.', ici l\'École Virtuelle des Créatifs au sujet de votre préinscription '.$c->reference.'.') }}"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
        <a class="btn btn-ghost btn-sm" href="mailto:{{ $c->email }}"><i class="fa-solid fa-envelope"></i>E-mail</a>
      </div>
    </x-panel>
    @foreach ($groups as $g => $keys)
      @php($rows = collect($keys)->filter(fn ($k) => ! empty($c->answers[$k])))
      @if ($rows->isNotEmpty())
        <x-panel :title="$g"><div class="lib-dl">@foreach ($rows as $k)<div class="lib-dl-row"><span>{{ str_replace('Contact - ', '', $k) }}</span><b style="white-space:pre-line">{{ implode(', ', $c->answers[$k]) }}</b></div>@endforeach</div></x-panel>
      @endif
    @endforeach
    @if ($c->files->isNotEmpty())
      <x-panel title="Pièces jointes"><div class="d-flex flex-wrap gap-2">@foreach ($c->files as $file)<a class="file-chip" href="{{ route('admin.candidature.fichier', $file) }}"><i class="fa-solid fa-paperclip"></i>{{ $file->label }} — {{ $file->original_name }}</a>@endforeach</div></x-panel>
    @endif
  </div>

  <div class="stack">
    <x-panel title="Parcours d'admission">
      <ol class="list-unstyled d-flex flex-column gap-2 mb-3">
        @foreach (array_slice(\App\Models\Candidature::STAGES, 0, 5, true) as $k => [$l, $t, $hint])
          <li class="d-flex gap-2 align-items-center small"><i class="fa-solid {{ $c->stage !== 'refusee' && $c->stageIndex() >= $loop->index ? 'fa-circle-check text-success' : 'fa-circle muted' }}"></i><span class="{{ $c->stage === $k ? 'fw-sb' : '' }}">{{ $l }}</span></li>
        @endforeach
      </ol>
      @if ($c->stage === 'activee')
        <a class="btn btn-primary w-100" href="{{ route('admin.etudiant', $c->student_id) }}" wire:navigate>Voir la fiche étudiant</a>
      @else
        <div class="d-flex flex-column gap-2">
          @if ($c->stage === 'recue')<button class="btn btn-primary" wire:click="etape('eligible')"><i class="fa-solid fa-check"></i>Profil éligible : envoyer le test</button>@endif
          @if ($c->stage === 'eligible')<button class="btn btn-primary" wire:click="etape('test')"><i class="fa-solid fa-check"></i>Test réussi</button>@endif
          @if ($c->stage === 'test')<button class="btn btn-primary" wire:click="etape('paiement')"><i class="fa-solid fa-money-bill"></i>1er versement reçu</button>@endif
          @if (in_array($c->stage, ['test', 'paiement', 'eligible']))<button class="btn {{ $c->stage === 'paiement' ? 'btn-primary' : 'btn-ghost' }}" wire:click="$set('activer', true)"><i class="fa-solid fa-user-check"></i>Activer le compte étudiant</button>@endif
          @if ($c->stage !== 'refusee')<button class="btn btn-outline-danger btn-sm" wire:click="etape('refusee')" wire:confirm="Marquer cette candidature comme non retenue ?">Non retenue</button>
          @else<button class="btn btn-ghost btn-sm" wire:click="etape('recue')">Rouvrir le dossier</button>@endif
        </div>
      @endif
    </x-panel>
    <x-panel title="Évaluation et notes">
      <form wire:submit="enregistrer" class="d-flex flex-column gap-3">
        <div><label class="form-label" for="score">Note au test (sur 20)</label><input class="form-control" type="number" min="0" max="20" id="score" wire:model="score">@error('score')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div><label class="form-label" for="notes">Notes internes</label><textarea class="form-control" rows="4" id="notes" wire:model="notes" placeholder="Échange d'orientation, points d'attention…"></textarea></div>
        <button class="btn btn-ghost" type="submit">Enregistrer</button>
      </form>
    </x-panel>
  </div>

  @if ($activer)
    <x-modal title="Activer le compte étudiant" icon="fa-user-check">
      <p class="small">Le compte MY EVC de <b>{{ $c->full_name }}</b> est créé avec un matricule, ses inscriptions ({{ implode(', ', $c->formations) }}) et son échéancier. Un e-mail d'activation lui est envoyé.</p>
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label" for="promo">Session</label><select class="form-select" id="promo" wire:model="promotion">@foreach ($promotions as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ \App\Models\Promotion::STATUSES[$p->status] }})</option>@endforeach</select>@error('promotion')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div><label class="form-label" for="mode">Modalité</label><select class="form-select" id="mode" wire:model="mode"><option>En ligne</option><option>Présentiel</option></select></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="confirmerActivation" wire:loading.attr="disabled"><i class="fa-solid fa-user-check"></i>Activer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
