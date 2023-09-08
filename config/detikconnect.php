<?php 

$baseUrl = env('APP_URL', '');

return [
    'backend' => [
        'base'          => $baseUrl,
        'is_staff'      => true,
        'is_mobile_app' => false,
        'urlLogin'      => $baseUrl . env('BACKEND_DC_URL_LOGIN', ''),
        'nextUrl'       => $baseUrl . env('BACKEND_DC_NEXT_URL', ''),
        'client_id'     => env('BACKEND_DC_CLIENT_ID', ''),
        'client_secret' => env('BACKEND_DC_SECRET', ''),
        'redirect_url'  => $baseUrl . env('BACKEND_DC_REDIRECT_URL', ''),
        'is_development'=> (bool) env('BACKEND_DC_IS_DEVELOPMENT', false),
        'is_internal'   => true,
    ],
    'url' => [
        'login' => '/auth',
        'dashboard' => '/dashboard'
    ]
];
