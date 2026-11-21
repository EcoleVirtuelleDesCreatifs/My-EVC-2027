<?php

use Illuminate\Support\Carbon;

if (! function_exists('fcfa')) {
    /** 185000 → « 185 000 FCFA » */
    function fcfa(int|float|null $n): string
    {
        return number_format((float) $n, 0, ',', ' ').' FCFA';
    }
}

if (! function_exists('jour')) {
    /** « Aujourd'hui », « Demain », « Hier » ou « lun. 12 oct. » */
    function jour(Carbon $d): string
    {
        $diff = (int) now()->startOfDay()->diffInDays($d->copy()->startOfDay(), false);

        return match ($diff) {
            0 => 'Aujourd\'hui', 1 => 'Demain', -1 => 'Hier',
            default => $d->translatedFormat('D j M'),
        };
    }
}

if (! function_exists('gestion_route')) {
    /** Route d'un écran de gestion partagé : « formateur.xxx » ou « admin.xxx » selon l'espace de l'utilisateur. */
    function gestion_route(string $name, mixed $params = []): string
    {
        return route((auth()->user()?->space() === 'formateur' ? 'formateur.' : 'admin.').$name, $params);
    }
}
