<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Neshan Maps (https://platform.neshan.org/docs/)
    |--------------------------------------------------------------------------
    |
    | map_api_key: JavaScript / Leaflet SDK key shown in the browser (restrict by domain in Neshan panel).
    | service_api_key: Web service key used only on the server (Reverse Geocoding v5). Keep secret.
    |
    */
    'map_api_key' => env('NESHAN_MAP_API_KEY', ''),
    'service_api_key' => env('NESHAN_SERVICE_API_KEY', ''),
];
