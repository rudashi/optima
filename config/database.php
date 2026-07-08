<?php

return [

    'optima' => [
        'driver' => 'sqlsrv',
        'host' => env('MS_HOST', 'mssql'),
        'port' => env('MS_PORT', '1433'),
        'database' => env('MS_DATABASE', 'optima_test'),
        'read' => [
            'username' => env('MS_USERNAME', 'sa'),
            'password' => env('MS_PASSWORD', 'Optima!2026'),
        ],
        'write' => [
            'username' => env('MS_SUDO_USERNAME', 'sa'),
            'password' => env('MS_SUDO_PASSWORD', 'Optima!2026'),
        ],
        'unix_socket' => env('MS_SOCKET', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,
        'engine' => null,
        'trust_server_certificate' => true,
    ],

];
