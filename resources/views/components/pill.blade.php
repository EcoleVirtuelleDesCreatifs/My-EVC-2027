@props(['tone' => 'mute', 'nodot' => false])
<span {{ $attributes->merge(['class' => 'pill p-'.$tone.($nodot ? ' nodot' : '')]) }}>{{ $slot }}</span>
