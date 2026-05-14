<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NID API Configuration
    |--------------------------------------------------------------------------
    |
    | Set these in your .env file:
    |
    |   NID_API_BASE=https://your-nid-api.com/api/search
    |   NID_API_KEY=your_api_key_here
    |   NID_API_TIMEOUT=15
    |
    */

    'nid_api' => [
        'base_url' => env('NID_API_BASE'),
        'key'      => env('NID_API_KEY'),
        'timeout'  => env('NID_API_TIMEOUT', 15),
    ],

];
