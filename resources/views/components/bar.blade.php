@props(['pct' => 0, 'tone' => ''])
<div class="u-bar {{ $tone }}" role="progressbar" aria-valuenow="{{ round($pct) }}" aria-valuemin="0" aria-valuemax="100"><span style="width:{{ max(0, min(100, $pct)) }}%"></span></div>
