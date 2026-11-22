@php($due = $s->totalDue())
@php($paid = $s->totalPaid())
<div>
  <div class="stat-grid mb-4">
    <x-kpi label="Montant de la formation" :value="fcfa($due)" icon="fa-graduation-cap" tone="lav" :sub="$s->formations->pluck('code')->implode(' + ')" />
    <x-kpi label="Déjà payé" :value="fcfa($paid)" icon="fa-circle-check" tone="ok" :sub="$due ? round($paid / $due * 100).' % réglé' : ''" />
    <x-kpi label="Reste à payer" :value="fcfa($s->balance())" icon="fa-hourglass-half" :tone="$s->balance() ? 'cor' : 'ok'" :sub="$s->nextSchedule() ? 'Prochaine échéance : '.$s->nextSchedule()->due_on->translatedFormat('j F') : 'Formation soldée'" />
    <x-kpi label="Versements" :value="$s->payments->count()" icon="fa-receipt" tone="info" sub="Reçus disponibles" />
  </div>

  <div class="u-grid u-2-1 align-items-start">
    <div class="stack">
      <x-panel title="Échéancier" :p0="true">
        <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Échéance</th><th>Date</th><th class="text-end">Montant</th><th class="text-end">Réglé</th><th>État</th></tr></thead><tbody>
          @foreach ($schedules as $x)
            @php([$lbl, $tone] = $x->state())
            <tr><td><x-fchip :code="$x->formation->code" /> {{ $x->label }}</td><td>{{ $x->due_on->translatedFormat('j M Y') }}</td><td class="text-end">{{ fcfa($x->amount) }}</td><td class="text-end">{{ fcfa($x->paid()) }}</td><td><x-pill :tone="$tone" :class="$lbl === 'À venir' ? 'text-white' : ''">{{ $lbl }}</x-pill></td></tr>
          @endforeach
        </tbody></table></div>
      </x-panel>
      <x-panel title="Mes versements et reçus" :p0="true">
        @forelse ($s->payments as $p)
          <div class="row-item"><span class="icon-sq"><i class="fa-solid fa-receipt"></i></span>
            <div class="ri-main"><div class="ri-t">{{ fcfa($p->amount) }} · {{ $p->method }}</div><div class="ri-s">{{ $p->paid_on->translatedFormat('j F Y') }} · Reçu {{ $p->receipt_number }}</div></div>
            <a class="btn btn-ghost btn-sm" href="{{ route('pdf.recu', $p) }}" target="_blank"><i class="fa-solid fa-file-pdf"></i>Reçu</a></div>
        @empty
          <div class="p-4"><x-empty title="Aucun versement enregistré" /></div>
        @endforelse
      </x-panel>
    </div>
    <x-panel title="Comment payer ?">
      <ol class="ps-3 mb-3 small d-flex flex-column gap-2">
        <li>Payez par <b>Orange Money, MTN MoMo, Moov Money ou Wave</b>, ou en espèces à l'accueil.</li>
        <li>Indiquez votre matricule <b class="mono">{{ $s->matricule }}</b> en motif.</li>
        <li>Envoyez la capture du paiement sur WhatsApp : votre reçu apparaît ici dès validation.</li>
      </ol>
      <a class="btn btn-primary w-100" target="_blank" rel="noopener" href="https://wa.me/{{ $wa }}?text={{ rawurlencode('Paiement MY EVC — '.$s->name.' ('.$s->matricule.') : je vous envoie ma preuve de paiement.') }}"><i class="fa-brands fa-whatsapp"></i>Envoyer ma preuve de paiement</a>
      <p class="small muted mt-3 mb-0">Conditions : {{ $s->formations->map(fn ($f) => $f->code.' — '.$f->pay_rule)->implode(' · ') }}</p>
    </x-panel>
  </div>
</div>
