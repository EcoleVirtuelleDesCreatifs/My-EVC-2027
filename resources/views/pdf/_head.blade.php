<div class="head">
  <div class="school"><b>École Virtuelle des Créatifs</b><br>{{ $address }}<br>{{ $email }} · {{ $phone }}@if ($rccm)<br>RCCM {{ $rccm }}@endif</div>
  <img src="{{ public_path('assets/site/img/logo.png') }}" alt="EVC">
</div>
<div class="foot">École Virtuelle des Créatifs — SARL de droit ivoirien — {{ $email }} — Document généré par MY EVC le {{ now()->translatedFormat('j F Y à H\hi') }}</div>
