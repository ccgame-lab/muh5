<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

global $config;

$assetBase = rtrim($config['assets']['base_url'] ?? '', '/');

if (empty($assetBase)) {
    $resBase = '/resource/';
    $clientBase = '/';
} else {
    $resBase = $assetBase . '/resource/';
    $clientBase = '/';
}

$name = $config['server']['name'] ?? 'S1';
$id = $config['server']['id'] ?? 1;
$ws_host = $config['server']['ws_host'] ?? 'muh5-ws.ccgame.org';
$ws_path = $config['server']['ws_path'] ?? '/s1/';
$ws_port = $config['server']['ws_port'] ?? 443;

$serverStr = "{$name}|{$id}|{$ws_host}{$ws_path}:{$ws_port}";

$data = [
    'serverList' => [$serverStr],
    'resList' => ["主干|{$resBase}"],
    'clientList' => ["主干|{$clientBase}"],
];

echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
