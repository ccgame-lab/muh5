<?php

declare(strict_types=1);

function redirect_to(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
