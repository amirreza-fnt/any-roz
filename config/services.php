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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    | ApiEco proxy for Cedar reverse geocoding (see https://apieco.ir/api/cedarmap-reverse-geocoding/).
    | URL may contain {point} as "lat,lng" or separate {lat} and {lng} placeholders.
    */
    'apieco' => [
        'key' => env('APIECO_KEY'),
        'map_ir_key' => env('APIECO_MAP_IR_KEY'),
        'reverse_geocode_url' => env(
            'APIECO_CEDAR_REVERSE_URL',
            'https://api.apieco.ir/map-ir/v1/geocode/cedarmaps.streets/{point}.json'
        ),
    ],

];
