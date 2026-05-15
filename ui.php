<?php

declare(strict_types=1);

function render_header(string $title): void
{
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css"/>
    <style>
        body { background-color: #0f1117; }
        .auth-card { max-width: 420px; width: 100%; }
        .brand-tag { font-size: 0.72rem; letter-spacing: 0.08em; opacity: 0.45; text-transform: uppercase; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <?php
}

function render_footer(): void
{
    ?>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
    <?php
}
