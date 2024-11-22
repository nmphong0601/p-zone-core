<?php
return [
    'core'             => '1.0',
    'core-sub-version' => '1.0.5',
    'homepage'         => 'https://p-zone.nmp-tech.com',
    'name'             => 'P-Zone',
    'github'           => 'https://github.com/p-zone/p-zone',
    'facebook'         => 'https://www.facebook.com/PZone.Ecommerce',
    'auth'             => 'NMP',
    'email'            => 'nmphong0601.business@gmail.com',
    'api_link'         => env('PZ_API_LINK', 'https://api.p-zone.nmp-tech.com/v3'),
    'ecommerce_mode'   => env('PZ_ECOMMERCE_MODE', 1),
    'search_mode'      => env('PZ_SEARCH_MODE', 'PRODUCT'), //PRODUCT,NEWS,CMS
];
