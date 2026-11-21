<?php

/* Réglages Livewire propres à MY EVC (les autres valeurs gardent leur défaut). */
return [
    'layout' => 'layouts.myevc',
    'pagination_theme' => 'bootstrap',
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:512000'],   // 500 Mo (vidéos de la Web TV) ; chaque écran applique sa propre limite
        'max_upload_time' => 10,
    ],
];
