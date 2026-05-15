<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

$page = $_GET['p'] ?? 'home';

$routes = [
    'home' => 'home.php',
    'play' => 'play.php',
];

$file = $routes[$page] ?? null;

if (!$file) {
    http_response_code(404);
    exit('404');
}

require __DIR__ . '/pages/' . $file;