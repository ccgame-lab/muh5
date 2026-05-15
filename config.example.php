<?php

declare(strict_types=1);

return [
    'app_name' => 'MUH5',
    'debug' => false,

    'greenjade_id' => [
        'base_url' => 'https://server.greenjade.net',
        'client_id' => '',
        'client_secret' => '',
        'redirect_uri' => 'https://muh5.ccgame.org/callback.php',
    ],

    'game' => [
        'slug' => 'muh5',
        'server_id' => 1,
    ],

    'assets' => [
        'base_url' => '',
    ],

    'server' => [
        'name' => 'S1',
        'id' => 1,
        'ws_host' => 'muh5-ws.ccgame.org',
        'ws_path' => '/s1/',
        'ws_port' => 443,
    ],
    'db_s100' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'actor_s100',
        'username' => 'root_muh5',
        'password' => '',
    ],


    'db_s1' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'actor_s1',
        'username' => 'root_muh5',
        'password' => '',
    ],

    'sdk_db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'muh5_sdk',
        'username' => 'root',
        'password' => 'CHANGE_ME',
        'charset' => 'utf8mb4',
    ],
];
