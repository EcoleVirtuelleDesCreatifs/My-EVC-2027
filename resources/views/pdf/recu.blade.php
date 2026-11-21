<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Reçu {{ $p->receipt_number }}</title>@include('pdf._style')</head><body>
@include('pdf._head')
@php($s = $p->student)
<table style="width:100%"><tr>
  <td><h1>Reçu de paiement</h1><div class="muted">N° {{ $p->receipt_number }} — {{ $p->paid_on->translatedFormat('j F Y') }}</div></td>
  <td class="right"><div class="muted">Montant reçu</div><div class="big">{{ fcfa($p->amount) }}</div></td>
</tr></table>
<table class="t">
  <tr><th colspan="2">Étudiant</th></tr>
  <tr><td style="width:35%">Nom</td><td>{{ $s->user->name }}</td></tr>
  <tr><td>Matricule</td><td>{{ $s->matricule }}</td></tr>
  <tr><td>Formation(s)</td><td>{{ $s->formations->pluck('name')->implode(' + ') }}</td></tr>
</table>
<table class="t">
  <tr><th colspan="2">Paiement</th></tr>
  <tr><td style="width:35%">Mode</td><td>{{ $p->method }}</td></tr>
  @if ($p->transaction_ref)<tr><td>Référence de transaction</td><td>{{ $p->transaction_ref }}</td></tr>@endif
  <tr><td>Enregistré par</td><td>{{ $p->recorder?->name ?? 'Service financier' }}</td></tr>
  @if ($p->note)<tr><td>Note</td><td>{{ $p->note }}</td></tr>@endif
</table>
<div class="box">Total de la formation : <b>{{ fcfa($s->totalDue()) }}</b> · Total réglé à ce jour : <b>{{ fcfa($s->totalPaid()) }}</b> · Reste à payer : <b>{{ fcfa($s->balance()) }}</b></div>
<div class="sign">Le service financier<div class="line">École Virtuelle des Créatifs</div></div>
</body></html>
