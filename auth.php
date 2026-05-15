<?php

declare(strict_types=1);

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: /?p=login');
        exit;
    }
}

function login_test_user(): void
{
    $_SESSION['user'] = [
        'id' => 1,
        'username' => 'test',
        'display_name' => 'Test User',
    ];
}

function logout_user(): void
{
    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}
