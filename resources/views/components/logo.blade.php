@props(['variant' => 'logo-horizontal-negatif', 'class' => ''])
{{-- Logos MY EVC (SVG intégré) : logo-horizontal-*, symbole-*, logotype-court-* en couleur | negatif | blanc --}}
<span {{ $attributes->merge(['class' => 'logo '.$class]) }} role="img" aria-label="myEVC">{!! file_get_contents(public_path('assets/app/img/'.$variant.'.svg')) !!}</span>
