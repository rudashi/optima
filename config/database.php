<?php

return [

    'optima' => [
        'driver' => 'sqlsrv',
        'host' => env('MS_HOST', '127.0.0.1'),
        'port' => env('MS_PORT', '3306'),
        'database' => env('MS_DATABASE', 'forge'),
        'username' => env('MS_USERNAME', 'forge'),
        'password' => env('MS_PASSWORD', ''),
        'unix_socket' => env('MS_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
        'trust_server_certificate' => true,
    ],

];
