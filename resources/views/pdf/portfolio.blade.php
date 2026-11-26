<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Portfolio — {{ $s->user->name }}</title>
<style>
  @page { margin: 0; }
  body { margin: 0; font-family: "DejaVu Sans", sans-serif; color: #14182B; font-size: 10.5px; line-height: 1.5; }
  .cover { height: 831pt; background: #1E1B4B; color: #fff; border-bottom: 10pt solid #EE5A36; page-break-after: always; }
  .cover .ci { padding: 40pt 44pt 0; }
  .cover .logo { height: 42pt; }
  .eyebrow { margin: 34pt 0 6pt; font-size: 9pt; letter-spacing: 3pt; text-transform: uppercase; color: #FFB59F; font-weight: bold; }
  .cover h1 { margin: 0; font-size: 38pt; line-height: 1.05; color: #fff; }
  .cover .sub { margin: 8pt 0 0; font-size: 11pt; color: #D7D5F0; }
  table.mosaic { width: 100%; border-collapse: separate; border-spacing: 8pt; margin: 26pt -8pt 0; }
  table.mosaic td { padding: 0; }
  table.mosaic td.cell { height: 186pt; width: 50%; background: #2B2770; border-radius: 8pt; text-align: center; vertical-align: middle; }
  table.stats { width: 100%; border-collapse: separate; border-spacing: 8pt; margin: 18pt -8pt 0; }
  table.stats td { background: #2B2770; border-radius: 8pt; padding: 10pt 12pt; color: #CFCDEB; font-size: 8.5pt; width: 33%; }
  table.stats b { display: block; color: #fff; font-size: 22pt; line-height: 1.1; }
  .cover .date { margin-top: 22pt; font-size: 8.5pt; color: #ABA8D6; }
  .pg { padding: 36pt 40pt 0; }
  .pg.next { page-break-before: always; }
  .top { border-bottom: 2pt solid #EE5A36; padding-bottom: 8pt; margin-bottom: 16pt; font-size: 8.5pt; color: #5E6470; }
  .top b { color: #1E1B4B; }
  .num { float: right; color: #EE5A36; font-weight: bold; }
  .chip { display: inline-block; padding: 2pt 7pt; border-radius: 8pt; font-size: 8pt; font-weight: bold; background: #EFEEF7; color: #1E1B4B; }
  .chip.top3 { background: #EE5A36; color: #fff; }
  h2 { margin: 8pt 0 4pt; font-size: 18pt; color: #1E1B4B; line-height: 1.2; }
  .meta { color: #5E6470; font-size: 9pt; margin-bottom: 12pt; }
  .visual { text-align: center; background: #F4F3F8; border-radius: 8pt; padding: 10pt 0; margin-bottom: 10pt; page-break-inside: avoid; }
  table.dl { width: 100%; border-collapse: collapse; margin: 4pt 0 10pt; font-size: 8.5pt; }
  table.dl th { text-align: left; background: #1E1B4B; color: #fff; padding: 4pt 7pt; font-size: 8pt; }
  table.dl td { border-bottom: 1pt solid #E4E2F3; padding: 4pt 7pt; vertical-align: top; word-break: break-all; }
  .novisual { background: #F4F3F8; border-radius: 8pt; padding: 26pt; text-align: center; color: #5E6470; margin-bottom: 14pt; }
  .review { border-left: 3pt solid #EE5A36; background: #F7F6FB; padding: 8pt 12pt; margin-bottom: 12pt; }
  .review small { display: block; color: #5E6470; margin-top: 3pt; }
  .brief { color: #3A3F55; font-size: 9.5pt; }
  .links { font-size: 8.5pt; color: #3563AD; margin-top: 8pt; word-break: break-all; }
  .foot { margin-top: 18pt; border-top: 1pt solid #E4E2F3; padding-top: 6pt; font-size: 8pt; color: #5E6470; text-align: center; }
</style>
</head>
<body>
  <div class="cover"><div class="ci">
    <img class="logo" src="{{ public_path('assets/site/img/logo-white.png') }}" alt="EVC">
    <p class="eyebrow">Portfolio de projets validés</p>
    <h1>{{ $s->user->name }}</h1>
    <p class="sub">{{ $s->formations->pluck('name')->implode(' · ') }}@if ($s->promotion) — {{ $s->promotion->name }}@endif<br>Matricule {{ $s->matricule }}</p>
    <table class="mosaic"><tr>
      @for ($i = 0; $i < 2; $i++)<td class="cell">@if (isset($tiles[$i]))<img src="{{ $tiles[$i]['src'] }}" style="width:{{ $tiles[$i]['width'] }}pt;height:{{ $tiles[$i]['height'] }}pt" alt="">@endif</td>@endfor
    </tr><tr>
      @for ($i = 2; $i < 4; $i++)<td class="cell">@if (isset($tiles[$i]))<img src="{{ $tiles[$i]['src'] }}" style="width:{{ $tiles[$i]['width'] }}pt;height:{{ $tiles[$i]['height'] }}pt" alt="">@endif</td>@endfor
    </tr></table>
    <table class="stats"><tr>
      <td><b>{{ $stats['total'] }}</b>projet{{ $stats['total'] > 1 ? 's' : '' }} validé{{ $stats['total'] > 1 ? 's' : '' }}</td>
      <td><b>{{ $stats['deliverables'] }}</b>livrable{{ $stats['deliverables'] > 1 ? 's' : '' }}</td>
      <td><b>{{ $stats['distinctions'] }}</b>Excellent ou Très bien</td>
    </tr></table>
    <div class="date">École Virtuelle des Créatifs · Abidjan · Catalogue édité le {{ now()->translatedFormat('j F Y') }}</div>
  </div></div>

  @forelse ($items as $n => $a)
    <div class="pg{{ $n ? ' next' : '' }}">
      <div class="top"><span class="num">{{ str_pad((string) ($n + 1), 2, '0', STR_PAD_LEFT) }} / {{ str_pad((string) $items->count(), 2, '0', STR_PAD_LEFT) }}</span><b>{{ $s->user->name }}</b> · Portfolio EVC</div>
      <span class="chip">{{ $a->project->formation->name }}</span>
      @if ($a->appreciation) <span class="chip {{ in_array($a->appreciation, ['Excellent', 'Très bien']) ? 'top3' : '' }}">{{ $a->appreciation }}</span>@endif
      <h2>{{ $a->project->title }}</h2>
      <div class="meta">{{ $a->project->type }} · Validé le {{ $a->validated_at?->translatedFormat('j F Y') }}</div>
      @forelse ($a->pdfVisuals as $v)
        <div class="visual"><img src="{{ $v['src'] }}" style="width:{{ $v['width'] }}pt;height:{{ $v['height'] }}pt" alt=""></div>
      @empty
        <div class="novisual">Réalisation remise sous forme de document ou de lien : voir la liste des livrables.</div>
      @endforelse
      @if ($a->review)<div class="review">« {{ \Illuminate\Support\Str::limit($a->review, 600) }} »@if ($a->reviewer)<small>{{ $a->reviewer }}, équipe pédagogique EVC</small>@endif</div>@endif
      <div class="brief">{{ \Illuminate\Support\Str::limit($a->project->briefExcerpt(), 420) }}</div>
      @if ($a->deliverables->isNotEmpty())
        <table class="dl"><tr><th style="width:24%">Format</th><th>Livrable</th><th style="width:26%">Détails</th></tr>
          @foreach ($a->deliverables as $d)
            <tr><td>{{ $d['label'] }}</td><td>{{ $d['kind'] === 'link' ? $d['url'] : $d['name'] }}</td>
              <td>{{ collect([$d['kind'] === 'image' ? $d['width'].' × '.$d['height'].' px, '.$d['orientation'] : null, $d['kind'] !== 'link' && $d['size'] ? ($d['size'] >= 1048576 ? number_format($d['size'] / 1048576, 1, ',', ' ').' Mo' : max(1, round($d['size'] / 1024)).' Ko') : null, $d['kind'] === 'link' ? 'En ligne' : null])->filter()->implode(' · ') }}</td></tr>
          @endforeach
        </table>
      @endif
      <div class="foot">École Virtuelle des Créatifs — {{ $email }} — Projet validé par l’équipe pédagogique de l’EVC</div>
    </div>
  @empty
    <div class="pg"><h2>Aucun projet validé pour le moment</h2><p class="meta">Les projets validés par l’équipe pédagogique apparaîtront dans ce catalogue.</p></div>
  @endforelse
</body>
</html>
