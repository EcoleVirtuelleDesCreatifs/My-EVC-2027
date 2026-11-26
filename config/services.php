<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'registre' => [
        'url' => env('EVC_REGISTRE_URL'),
        'token' => env('EVC_REGISTRE_TOKEN'),
        'timeout' => 10,
    ],

    /* Mesure d'audience du site public : chargée uniquement après consentement. */
    'analytics' => [
        'ga4' => preg_match('/^G-[A-Z0-9]{4,20}$/', (string) env('GA4_MEASUREMENT_ID')) ? env('GA4_MEASUREMENT_ID') : null,
        'meta_pixel' => preg_match('/^\d{5,20}$/', (string) env('META_PIXEL_ID')) ? env('META_PIXEL_ID') : null,
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
