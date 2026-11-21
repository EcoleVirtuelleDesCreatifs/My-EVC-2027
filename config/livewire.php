<?php

/* Réglages Livewire propres à MY EVC (les autres valeurs gardent leur défaut). */
return [
    'layout' => 'layouts.myevc',
    'pagination_theme' => 'bootstrap',
    'temporary_file_upload' => [
        'rules' => ['required', 'file', 'max:51200'],   // 50 Mo (dépôts de projets, ressources)
        'max_upload_time' => 10,
    ],
];
