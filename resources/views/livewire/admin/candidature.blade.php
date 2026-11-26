<div class="u-grid u-2-1 align-items-start">
  <div class="stack">
    <x-panel :title="$c->full_name">
      <x-slot:tools><x-pill :tone="$c->stageTone()">{{ $c->stageLabel() }}</x-pill></x-slot:tools>
      <div class="d-flex flex-wrap gap-3 small mb-3">
        <span class="mono">{{ $c->reference }}</span><span><i class="fa-solid fa-envelope me-1"></i>{{ $c->email }}</span>
        <span><i class="fa-solid fa-phone me-1"></i>{{ $c->phone }}</span><span><i class="fa-regular fa-clock me-1"></i>{{ $c->created_at->translatedFormat('j F Y à H\hi') }}</span>
      </div>
      <div class="d-flex gap-2 flex-wrap mb-3">@foreach ($c->formations as $code)<x-fchip :code="$code" />@endforeach <span class="small muted">{{ $c->mode }}</span></div>
      <p class="small mb-3"><i class="fa-solid fa-bullhorn me-1"></i>Provenance : <b>{{ $c->attributionLabel() }}</b>@if (! empty($c->attribution['landing']))<span class="muted"> · arrivée sur {{ $c->attribution['landing'] }}</span>@endif @if (! empty($c->attribution['content']))<span class="muted"> · contenu : {{ $c->attribution['content'] }}</span>@endif</p>
      <div class="d-flex gap-2 flex-wrap">
        <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D/', '', $c->phone) }}?text={{ rawurlencode('Bonjour '.$c->first_name.', ici l\'École Virtuelle des Créatifs au sujet de votre préinscription '.$c->reference.'.') }}"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
        <a class="btn btn-ghost btn-sm" href="mailto:{{ $c->email }}"><i class="fa-solid fa-envelope"></i>E-mail</a>
      </div>
    </x-panel>
    @foreach ($groups as $g => $keys)
      @php($rows = collect($keys)->filter(fn ($k) => ! empty($c->answers[$k])))
      @if ($rows->isNotEmpty())
        <x-panel :title="$g"><div class="lib-dl">@foreach ($rows as $k)<div class="lib-dl-row"><span>{{ str_replace('Contact - ', '', $k) }}</span><b style="white-space:pre-line">{{ $k === 'Date souhaitée d’inscription et de paiement' ? \Carbon\Carbon::parse($c->answers[$k][0])->format('d/m/Y') : implode(', ', $c->answers[$k]) }}</b></div>@endforeach</div></x-panel>
      @endif
    @endforeach
    <x-panel title="Questionnaire SAOP · 30 minutes">
      <p class="small muted">{{ $c->saop_submitted_at ? 'Soumis le '.$c->saop_submitted_at->translatedFormat('d/m/Y à H:i') : ($c->saopClosed() ? 'Temps écoulé · réponses verrouillées' : ($c->saop_started_at ? 'En cours · échéance : '.$c->saop_started_at->copy()->addMinutes(30)->format('d/m/Y H:i') : 'Pas encore démarré')) }}</p>
      <p class="small">Évaluation humaine : les contraintes techniques, la résidence et les besoins d’accompagnement ne constituent pas des motifs automatiques d’exclusion.</p>
      @foreach (\App\Services\SaopQuestionnaire::QUESTIONS as $number => [$title, $question, $hint])
        <details class="mb-3"><summary>{{ $number }}. {{ $title }}</summary><p class="small muted">{{ $question }}</p><div style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $c->saop_answers[$number] ?? 'Aucune réponse enregistrée.' }}</div></details>
      @endforeach
      @if ($c->stage === 'eligible' && ! $c->saopClosed())<button class="btn btn-ghost btn-sm" wire:click="envoyerQuestionnaire" wire:loading.attr="disabled" wire:confirm="Envoyer le lien du questionnaire par e-mail au candidat ?">Renvoyer le lien par e-mail</button>@endif
    </x-panel>
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
        @if ($c->student)
          <a class="btn btn-primary w-100" href="{{ route('admin.etudiant', ['student' => $c->student]) }}" wire:navigate>Voir la fiche étudiant</a>
        @else
          <div class="alert alert-warning mb-0" role="status">Le dossier étudiant associé n’est plus disponible. L’historique de cette candidature est conservé.</div>
        @endif
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
    @if ($c->first_payment || in_array($c->stage, ['paiement', 'activee']))
      <x-panel title="Premier versement">
        @if ($c->first_payment)
          <p class="mb-1"><b>{{ fcfa($c->first_payment['amount']) }}</b> · {{ $c->first_payment['method'] }}</p>
          <p class="small muted">Reçu le {{ \Carbon\Carbon::parse($c->first_payment['paid_on'])->format('d/m/Y') }}@if (! empty($c->first_payment['transaction_ref'])) · Réf. {{ $c->first_payment['transaction_ref'] }}@endif</p>
          @if ($c->firstPayment)
            <p class="small">Déjà déduit du solde étudiant.</p>
            <a class="btn btn-ghost btn-sm" href="{{ route('pdf.recu', $c->firstPayment) }}" target="_blank" rel="noopener">Reçu {{ $c->firstPayment->receipt_number }}</a>
          @else
            <p class="small muted mb-0">Ce versement sera automatiquement enregistré et déduit lors de l’activation du compte.</p>
          @endif
        @else
          <p class="small muted">L’ancien parcours indiquait « 1er versement reçu » sans créer de paiement. Confirmez les détails ou rattachez un reçu existant pour mettre le solde à jour sans doublon.</p>
          @if ($c->stage !== 'activee' || $c->student)<button class="btn btn-primary btn-sm" wire:click="ouvrirPremierVersement">Renseigner le versement reçu</button>@endif
        @endif
      </x-panel>
    @endif
    <x-panel title="Évaluation et notes">
      <form wire:submit="enregistrer" class="d-flex flex-column gap-3">
        <div><label class="form-label" for="score">Note au test (sur 20)</label><input class="form-control" type="number" min="0" max="20" id="score" wire:model="score">@error('score')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div><label class="form-label" for="notes">Notes internes</label><textarea class="form-control" rows="4" id="notes" wire:model="notes" placeholder="Échange d'orientation, points d'attention…"></textarea></div>
        <button class="btn btn-ghost" type="submit">Enregistrer</button>
      </form>
    </x-panel>
  </div>

  @if ($validerVersement)
    <x-modal title="Valider le premier versement reçu" icon="fa-money-bill">
      <p class="small">Saisissez le paiement réellement reçu pour <b>{{ $c->full_name }}</b>. Il sera déduit du montant total, sans supplément.</p>
      @if ($versementsExistants->isNotEmpty())
        <label class="form-label" for="type-versement">Ce paiement est-il déjà enregistré ?</label>
        <select id="type-versement" class="form-select mb-3" wire:model.live="typeVersement"><option value="existant">Oui : rattacher un reçu existant, sans nouvel encaissement</option><option value="nouveau">Non : enregistrer le versement manquant</option></select>
      @endif
      @if ($typeVersement === 'existant')
        <label class="form-label" for="paiement-existant">Reçu du premier versement</label>
        <select id="paiement-existant" class="form-select" wire:model="paiementExistant"><option value="">Choisir le reçu…</option>@foreach ($versementsExistants as $paiement)<option value="{{ $paiement->id }}">{{ $paiement->receipt_number }} · {{ fcfa($paiement->amount) }} · {{ $paiement->paid_on->format('d/m/Y') }} · {{ $paiement->method }}</option>@endforeach</select>
        @error('paiementExistant')<div class="small text-danger">{{ $message }}</div>@enderror
      @else
        <div class="row g-3">
          <div class="col-sm-6"><label class="form-label" for="premier-montant">Montant réellement reçu (FCFA) *</label><input id="premier-montant" type="number" min="1" step="1" class="form-control" wire:model="premier.amount">@error('premier.amount')<div class="small text-danger">{{ $message }}</div>@enderror</div>
          <div class="col-sm-6"><label class="form-label" for="premier-date">Date du paiement *</label><input id="premier-date" type="date" max="{{ now()->toDateString() }}" class="form-control" wire:model="premier.paid_on">@error('premier.paid_on')<div class="small text-danger">{{ $message }}</div>@enderror</div>
          <div class="col-12"><label class="form-label" for="premier-methode">Moyen de paiement *</label><select id="premier-methode" class="form-select" wire:model="premier.method"><option value="">Choisir…</option>@foreach (\App\Models\Payment::METHODS as $methode)<option>{{ $methode }}</option>@endforeach</select>@error('premier.method')<div class="small text-danger">{{ $message }}</div>@enderror</div>
          <div class="col-12"><label class="form-label" for="premier-reference">Référence de transaction (facultatif)</label><input id="premier-reference" class="form-control" wire:model="premier.transaction_ref" maxlength="150">@error('premier.transaction_ref')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        </div>
        <p class="small muted mt-3 mb-0">Le montant proposé est l’acompte du catalogue : vérifiez-le et adaptez-le au montant réellement payé. Ne créez pas un nouveau versement si un reçu existe déjà.</p>
      @endif
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="confirmerPremierVersement" wire:loading.attr="disabled" wire:confirm="Confirmez-vous que ce versement a réellement été reçu et qu’il n’est pas comptabilisé une seconde fois ?">Confirmer le versement reçu</button></x-slot:footer>
    </x-modal>
  @endif

  @if ($activer)
    <x-modal title="Activer le compte étudiant" icon="fa-user-check">
      <p class="small">Le compte MY EVC de <b>{{ $c->full_name }}</b> est créé avec un matricule, ses inscriptions ({{ implode(', ', $c->formations) }}) et son échéancier. Un e-mail d'activation lui est envoyé.</p>
      @if ($c->first_payment)<p class="small">Le premier versement de <b>{{ fcfa($c->first_payment['amount']) }}</b> sera automatiquement enregistré, avec son reçu, et déduit du solde.</p>@endif
      @error('premierVersement')<p class="small text-danger">{{ $message }}</p>@enderror
      @if ($c->stage === 'paiement' && ! $c->first_payment)<button class="btn btn-ghost btn-sm mb-3" wire:click="ouvrirPremierVersement">Préciser le premier versement reçu</button>@endif
      <div class="d-flex flex-column gap-3">
        <div><label class="form-label" for="promo">Session</label><select class="form-select" id="promo" wire:model="promotion">@foreach ($promotions as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ \App\Models\Promotion::STATUSES[$p->status] }})</option>@endforeach</select>@error('promotion')<div class="small text-danger">{{ $message }}</div>@enderror</div>
        <div><label class="form-label" for="mode">Modalité</label><select class="form-select" id="mode" wire:model="mode"><option>En ligne</option><option>Présentiel</option></select></div>
      </div>
      <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="confirmerActivation" wire:loading.attr="disabled"><i class="fa-solid fa-user-check"></i>Activer</button></x-slot:footer>
    </x-modal>
  @endif
</div>
