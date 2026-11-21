<div>
  @if ($fs->isEmpty())
    <x-empty title="Pas de RECO 60 dans votre formation" text="Le RECO 60 concerne les formations Community management et Bureautique." />
  @else
    @if ($fs->count() > 1)
      <div class="seg seg-soft mb-3">@foreach ($fs as $fo)<button type="button" class="{{ $f->id === $fo->id ? 'on' : '' }}" wire:click="$set('code', '{{ $fo->code }}')"><span class="sw" style="background:var(--{{ strtolower($fo->code) }})"></span>RECO 60 {{ $fo->code }}</button>@endforeach</div>
    @endif

    @if (! $p)
      <div class="u-grid u-2-1 align-items-start">
        <x-panel title="Lancer mon RECO 60" sub="60 jours pour mener un vrai projet professionnel, suivi par votre formateur">
          <form wire:submit="creer" class="row g-3">
            <div class="col-md-8"><label class="form-label">Nom du projet</label><input class="form-control @error('creation.name') is-invalid @enderror" wire:model="creation.name" placeholder="{{ $f->code === 'CM' ? 'Ex. : Bissap Délice — lancement digital' : 'Ex. : Gestion numérique d\'une boutique de quartier' }}">@error('creation.name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Secteur</label><input class="form-control" wire:model="creation.sector" placeholder="Restauration, commerce…"></div>
            <div class="col-12"><label class="form-label">Description</label><textarea class="form-control @error('creation.description') is-invalid @enderror" rows="3" wire:model="creation.description" placeholder="Le client ou la structure, le besoin, ce que vous allez réaliser."></textarea>@error('creation.description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">Objectifs</label><textarea class="form-control" rows="2" wire:model="creation.objectives" placeholder="Ex. : atteindre 1 000 abonnés, 50 commandes WhatsApp…"></textarea></div>
            <div class="col-12"><label class="form-label">{{ $f->code === 'CM' ? 'Canaux utilisés' : 'Outils utilisés' }}</label><input class="form-control" wire:model="creation.channels" placeholder="{{ $f->code === 'CM' ? 'Facebook, Instagram, TikTok, WhatsApp Business' : 'Excel, Word, Google Sheets' }}"></div>
            <div class="col-12"><button class="btn btn-primary btn-lg" type="submit"><i class="fa-solid fa-rocket"></i>Lancer le jour 1</button></div>
          </form>
        </x-panel>
        <x-panel title="Comment ça marche ?">
          <ol class="reco-steps">@foreach (\App\Models\RecoProject::TEMPLATES[$f->code] ?? \App\Models\RecoProject::TEMPLATES['default'] as $i => $st)<li><span class="rs-n">{{ $i + 1 }}</span><div><b class="small">{{ $st['t'] }}</b><div class="small muted">{{ $st['d'] }}</div></div></li>@endforeach</ol>
        </x-panel>
      </div>
    @else
      <section class="panel mb-4"><div class="panel-b d-flex flex-wrap align-items-center gap-4">
        <div class="reco-day"><b>Jour {{ $p->day() }}</b><span>/ 60</span></div>
        <div class="flex-grow-1" style="min-width:220px"><div class="d-flex justify-content-between small muted mb-1"><span>{{ $p->name }}</span><span>Fin le {{ $p->endsOn()->translatedFormat('j F Y') }}</span></div><x-bar :pct="$p->day() / 60 * 100" tone="cor" /></div>
        <x-pill :tone="$p->statusTone()">{{ $p->statusLabel() }}</x-pill>
      </div></section>

      @if ($p->status === 'evalue')
        <div class="alert-strip as-ok mb-4"><i class="fa-solid fa-award"></i><span>RECO 60 évalué par {{ $p->evaluator?->name }} le {{ $p->evaluated_at?->translatedFormat('j F Y') }} — appréciation : <b>{{ $p->appreciation }}</b>@if ($p->evaluation)<br>{{ $p->evaluation }}@endif</span></div>
      @elseif ($p->status === 'rendu')
        <div class="alert-strip as-info mb-4"><i class="fa-solid fa-hourglass-half"></i><span>Rapport final rendu le {{ $p->submitted_at->translatedFormat('j F') }} : en attente de l'évaluation de votre formateur.</span></div>
      @endif

      <div class="u-grid u-2-1 align-items-start">
        <div class="stack">
          <x-panel title="Indicateurs">
            <x-slot:tools>@if ($p->status === 'en_cours')<button class="btn btn-ghost btn-sm" wire:click="ouvrirKpi"><i class="fa-solid fa-pen"></i>Mettre à jour</button>@endif</x-slot:tools>
            @if ($p->kpis)
              <div class="kpi-mini">@foreach ($p->kpis as $k)<div><b>{{ $k['v'] }}</b><span>{{ $k['l'] }}</span></div>@endforeach<div><b>{{ rtrim(rtrim(number_format($p->entries->sum('hours'), 1, ',', ' '), '0'), ',') }} h</b><span>Heures déclarées</span></div></div>
            @else
              <p class="small muted mb-0">Ajoutez vos indicateurs clés (abonnés, portée, commandes, livrables…) pour suivre vos résultats.</p>
            @endif
          </x-panel>
          <x-panel title="Journal d'activités" :sub="$p->entries->count().' activité(s)'">
            <x-slot:tools>@if ($p->status === 'en_cours')<button class="btn btn-primary btn-sm" wire:click="ouvrirJournal"><i class="fa-solid fa-plus"></i>Ajouter une activité</button>@endif</x-slot:tools>
            @forelse ($p->entries as $en)
              <div class="entry">
                <div class="small muted">{{ ucfirst($en->entry_date->translatedFormat('l j F')) }} · jour {{ min(60, $p->started_on->diffInDays($en->entry_date) + 1) }} · {{ rtrim(rtrim(number_format($en->hours, 1, ',', ''), '0'), ',') }} h</div>
                <b>{{ $en->action }}</b>
                @if ($en->result)<div class="small" style="white-space:pre-line">{{ $en->result }}</div>@endif
                <div class="d-flex flex-wrap gap-2 mt-1">
                  @if ($en->file_path)<a class="file-chip" href="{{ route('fichier.reco', $en) }}"><i class="fa-solid fa-paperclip"></i>{{ $en->file_name }}</a>@endif
                  @if ($en->link)<a class="file-chip" href="{{ $en->link }}" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i>{{ \Illuminate\Support\Str::limit($en->link, 40) }}</a>@endif
                </div>
                @if ($en->feedback)<div class="fb"><b>{{ $en->feedbackAuthor?->name }}</b> : {{ $en->feedback }}</div>@endif
              </div>
            @empty
              <p class="small muted mb-0">Notez chaque action menée et ses résultats, avec une preuve (capture, fichier, lien).</p>
            @endforelse
          </x-panel>
        </div>
        <div class="stack">
          <x-panel title="Étapes" :sub="$p->stepsDone().' / '.count($p->steps)">
            <ol class="reco-steps">
              @foreach ($p->steps as $i => $st)
                <li class="{{ ! empty($st['done']) ? 'done' : '' }}">
                  @if ($p->status === 'en_cours')<button type="button" class="rs-n border-0" wire:click="basculerEtape({{ $i }})" title="Marquer comme {{ ! empty($st['done']) ? 'à faire' : 'faite' }}">@if (! empty($st['done']))<i class="fa-solid fa-check"></i>@else{{ $i + 1 }}@endif</button>
                  @else<span class="rs-n">@if (! empty($st['done']))<i class="fa-solid fa-check"></i>@else{{ $i + 1 }}@endif</span>@endif
                  <div><b class="small">{{ $st['t'] }}</b><div class="small muted">{{ $st['d'] }}</div></div>
                </li>
              @endforeach
            </ol>
          </x-panel>
          <x-panel title="Fiche du projet">
            <div class="lib-dl">
              @if ($p->sector)<div class="lib-dl-row"><span>Secteur</span><b>{{ $p->sector }}</b></div>@endif
              <div class="lib-dl-row"><span>Début</span><b>{{ $p->started_on->translatedFormat('j F Y') }}</b></div>
              @if ($p->channels)<div class="lib-dl-row"><span>{{ $f->code === 'CM' ? 'Canaux' : 'Outils' }}</span><b>{{ $p->channels }}</b></div>@endif
            </div>
            <p class="small mt-3 mb-1" style="white-space:pre-line">{{ $p->description }}</p>
            @if ($p->objectives)<p class="small muted mb-0" style="white-space:pre-line"><b>Objectifs :</b> {{ $p->objectives }}</p>@endif
          </x-panel>
          <x-panel title="Rapport final">
            @if ($p->report_path)
              <a class="file-chip" href="{{ route('fichier.reco.rapport', $p) }}"><i class="fa-solid fa-file-arrow-down"></i>{{ $p->report_name }}</a>
            @elseif ($p->status === 'en_cours')
              <p class="small muted">À rendre à la fin des 60 jours : bilan chiffré, difficultés, recommandations (PDF ou Word).</p>
              <form wire:submit="rendreRapport">
                <input type="file" class="form-control @error('rapport') is-invalid @enderror" wire:model="rapport" accept=".pdf,.doc,.docx,.ppt,.pptx">
                @error('rapport')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <button class="btn btn-primary btn-sm mt-2" type="submit" wire:loading.attr="disabled" wire:target="rendreRapport,rapport" wire:confirm="Rendre le rapport final ? Le RECO 60 ne sera plus modifiable."><i class="fa-solid fa-cloud-arrow-up"></i>Rendre le rapport final</button>
              </form>
            @endif
          </x-panel>
        </div>
      </div>

      @if ($journal)
        <x-modal title="Nouvelle activité" icon="fa-plus">
          <div class="row g-3">
            <div class="col-sm-6"><label class="form-label">Date</label><input type="date" class="form-control @error('e.entry_date') is-invalid @enderror" wire:model="e.entry_date">@error('e.entry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-sm-6"><label class="form-label">Temps passé (h)</label><input type="number" step="0.5" class="form-control" wire:model="e.hours"></div>
            <div class="col-12"><label class="form-label">Action menée</label><input class="form-control @error('e.action') is-invalid @enderror" wire:model="e.action" placeholder="Ex. : Campagne Meta Ads 10 000 FCFA">@error('e.action')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">Résultats</label><textarea class="form-control" rows="2" wire:model="e.result" placeholder="Ex. : 260 clics, CPC 38 FCFA"></textarea></div>
            <div class="col-12"><label class="form-label">Lien <span class="small muted">(facultatif)</span></label><input class="form-control @error('e.link') is-invalid @enderror" wire:model="e.link" placeholder="https://">@error('e.link')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">Preuve <span class="small muted">(capture, fichier — facultatif)</span></label><input type="file" class="form-control @error('preuve') is-invalid @enderror" wire:model="preuve">@error('preuve')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          </div>
          <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="ajouterEntree" wire:loading.attr="disabled" wire:target="ajouterEntree,preuve">Ajouter</button></x-slot:footer>
        </x-modal>
      @endif
      @if ($editKpi)
        <x-modal title="Mes indicateurs" icon="fa-chart-line">
          @foreach ($kpis as $i => $k)
            <div class="row g-2 mb-2"><div class="col-7"><input class="form-control @error('kpis.'.$i.'.l') is-invalid @enderror" placeholder="Indicateur (ex. : Abonnés)" wire:model="kpis.{{ $i }}.l"></div><div class="col-4"><input class="form-control @error('kpis.'.$i.'.v') is-invalid @enderror" placeholder="Valeur" wire:model="kpis.{{ $i }}.v"></div>
              <div class="col-1"><button type="button" class="btn btn-quiet btn-icon" wire:click="$set('kpis', {{ json_encode(array_values(array_diff_key($kpis, [$i => 1]))) }})" aria-label="Retirer"><i class="fa-solid fa-xmark"></i></button></div></div>
          @endforeach
          <button type="button" class="btn btn-ghost btn-sm" wire:click="ajouterKpi"><i class="fa-solid fa-plus"></i>Ajouter un indicateur</button>
          <x-slot:footer><button class="btn btn-ghost" wire:click="closeModal">Annuler</button><button class="btn btn-primary" wire:click="enregistrerKpi">Enregistrer</button></x-slot:footer>
        </x-modal>
      @endif
    @endif
  @endif
</div>
