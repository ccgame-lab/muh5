<?php

declare(strict_types=1);

require_login();

$user = current_user();

echo 'PLAY PAGE - Welcome, ' . e($user['display_name']);