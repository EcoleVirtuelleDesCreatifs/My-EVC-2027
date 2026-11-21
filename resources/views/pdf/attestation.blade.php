<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Attestation d'inscription</title>@include('pdf._style')</head><body>
@include('pdf._head')
<h1 style="text-align:center;margin-top:30px">Attestation d'inscription</h1>
<p style="text-align:center" class="muted">Réf. ATT-{{ $s->matricule }}-{{ now()->format('Ymd') }}</p>
<p style="margin-top:34px;font-size:12px">Je soussigné, {{ $director }}, Directeur général de l'École Virtuelle des Créatifs, atteste que :</p>
<div class="box" style="margin:18px 0;font-size:12px">
  <b style="font-size:15px">{{ $s->user->name }}</b><br>
  Matricule : {{ $s->matricule }}@if ($s->birth_date) · Né(e) le {{ $s->birth_date->translatedFormat('j F Y') }}@endif
</div>
<p style="font-size:12px">est régulièrement inscrit(e) à l'École Virtuelle des Créatifs pour la ou les formations suivantes :</p>
<table class="t"><tr><th>Formation</th><th>Durée</th><th>Session</th></tr>
  @foreach ($s->formations as $f)<tr><td>{{ $f->name }}</td><td>{{ $f->months }} mois</td><td>{{ $s->promotion?->name ?? '—' }}</td></tr>@endforeach
</table>
<p style="font-size:12px">En foi de quoi, la présente attestation lui est délivrée pour servir et valoir ce que de droit.</p>
<div class="sign">Fait à Abidjan, le {{ now()->translatedFormat('j F Y') }}<div class="line">{{ $director }}<br>Directeur général</div></div>
</body></html>
