<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/response.php';

$page = $_GET['p'] ?? 'home';

$routes = [
    'home' => 'home.php',
    'play' => 'play.php',
    'servers' => 'servers.php',
    'register' => 'register.php',
    'login' => 'login.php',
    'logout' => 'logout.php',
    'login_bt' => 'login_bt.php',
    'login_bt.json' => 'login_bt.php',
];

$file = $routes[$page] ?? null;

if (!$file) {
    http_response_code(404);
    exit('404');
}

require __DIR__ . '/pages/' . $file;