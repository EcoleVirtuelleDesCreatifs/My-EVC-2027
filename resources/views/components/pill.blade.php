@props(['tone' => 'mute', 'nodot' => false])
<span {{ $attributes->merge(['class' => 'pill p-'.$tone.($nodot ? ' nodot' : '')]) }}>@if (in_array(trim((string) $slot), ['Projet reçu', 'À démarrer'], true))<span class="text-white">{{ $slot }}</span>@else{{ $slot }}@endif</span>
