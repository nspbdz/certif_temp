<?php

$isDevel = env('APP_ENV') == 'local';
$micrositeAKCDN = 'https://akcdn.detik.net.id/microsite/';
$env = $isDevel ? 'devel' : 'live';

return [
    'data_email' => array("info@detikmail.com", "info@cnbcindonesia-mail.com","info@cnnindonesia-mail.com"),
    'email_type' => ['certificate', 'ticket'],
    'font_type' => [
        'montserrat-bold' => 'Montserrat Bold',
        'montserrat-black' => 'Montserrat Black',
        'gotham-bold' => 'Gotham Bold',
        'gotham-black' => 'Gotham Black',
        'poppins' => 'Poppins',
        'dm-sans' => 'DM Sans',
        'dm-serif-display' => 'DM Serif Display',
        'urbanist' => 'Urbanist',
        'sniglet' => 'Sniglet',
        'chelsea-market' => 'Chelsea Market',
        'cinzel' => 'Cinzel',
        'raleway' => 'Raleway',
        'ibm-flex-mono' => 'IBM Flex Mono',
        'proxima' => 'Proxima'
    ],
    'text_position' => ['top','middle','bottom'],
    'line_height' => [
        'top' => '45px',
        'middle' => '105px',
        'bottom' => '140px'
    ],
    'status' => [
        'pending' => 'pending',
        'sending' => 'sending',
        'failed' => 'failed',
        'success' => 'success'
    ],
    'env' => $env,
    'ip' => [
        'cdn' => 'http://127.0.0.1/project/',
        'akcdn' => 'http://127.0.0.1/',
        'devel' => '192.168.1.158',
        'proxy' => '203.190.243.181:3128'
    ],
    'akcdn' => [
        'visual' => 'https://akcdn.detik.net.id/visual/',
        'microsite' => $micrositeAKCDN,
        'full_url' => $micrositeAKCDN . $env . "/" . config('app.name') . "/" ,
    ],
    'recipient_email_limit' => 100,
    'certif_temp_path' => 'public/temp_certif/'
];
