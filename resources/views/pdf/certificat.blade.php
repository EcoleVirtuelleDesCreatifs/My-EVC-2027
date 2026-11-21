<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Certificat {{ $c->code }}</title>
<style>
  @page { margin: 0; }
  body { font-family: "DejaVu Sans", sans-serif; margin: 0; color: #14182B; }
  .frame { position: absolute; top: 26px; left: 26px; right: 26px; bottom: 26px; border: 3px solid #1E1B4B; }
  .frame2 { position: absolute; top: 34px; left: 34px; right: 34px; bottom: 34px; border: 1px solid #EE5A36; }
  .in { position: absolute; top: 70px; left: 90px; right: 90px; text-align: center; }
  .in img { height: 70px; }
  .k { letter-spacing: 6px; font-size: 12px; color: #EE5A36; font-weight: bold; margin-top: 18px; }
  h1 { font-size: 40px; color: #1E1B4B; margin: 6px 0 18px; letter-spacing: 2px; }
  .name { font-size: 30px; font-weight: bold; color: #1E1B4B; border-bottom: 1px solid #C9CFE0; display: inline-block; padding: 0 40px 6px; margin: 10px 0; }
  .txt { font-size: 13px; color: #3A4060; line-height: 1.7; }
  .bottom { position: absolute; bottom: 70px; left: 90px; right: 90px; }
  .bottom td { font-size: 11px; color: #3A4060; vertical-align: bottom; }
  .line { border-top: 1px solid #14182B; padding-top: 4px; width: 220px; display: inline-block; }
</style></head><body>
<div class="frame"></div><div class="frame2"></div>
<div class="in">
  <img src="{{ public_path('assets/site/img/logo.png') }}" alt="EVC">
  <div class="k">ÉCOLE VIRTUELLE DES CRÉATIFS</div>
  <h1>CERTIFICAT PROFESSIONNEL</h1>
  <div class="txt">Le jury de certification atteste que</div>
  <div class="name">{{ $c->student->user->name }}</div>
  <div class="txt">a suivi avec succès la formation<br><b style="font-size:17px;color:#1E1B4B">{{ $c->formation->name }}</b><br>
    et validé l'ensemble des compétences, projets et conditions d'assiduité requis, avec l'appréciation <b>{{ $c->appreciation }}</b>.</div>
</div>
<div class="bottom"><table style="width:100%"><tr>
  <td>Délivré à Abidjan, le {{ $c->issued_on->translatedFormat('j F Y') }}<br>Référence : <b>{{ $c->code }}</b><br>Vérification : {{ url('/certificats/verifier') }}</td>
  <td style="text-align:right"><span class="line">{{ $director }}<br>Directeur général</span></td>
</tr></table></div>
</body></html>
