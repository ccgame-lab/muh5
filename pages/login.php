<?php

declare(strict_types=1);

global $config;

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        redirect_to('/?error=' . urlencode('Vui lòng nhập đầy đủ thông tin.'));
    }

    try {
        $pdo = new PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $config['db_s1']['host'],
                $config['db_s1']['port'],
                $config['db_s1']['database']
            ),
            $config['db_s1']['username'],
            $config['db_s1']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $stmt = $pdo->prepare('SELECT account, passwd FROM globaluser WHERE account = ? LIMIT 1');
        $stmt->execute([$username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['passwd'] !== md5($password)) {
            redirect_to('/?error=' . urlencode('Tài khoản hoặc mật khẩu không đúng.'));
        }

        $_SESSION['user'] = [
            'id'           => 0,
            'username'     => $row['account'],
            'display_name' => $row['account'],
        ];

        redirect_to('/?p=servers');

    } catch (PDOException $e) {
        error_log('Login DB error: ' . $e->getMessage());
        redirect_to('/?error=' . urlencode('Lỗi hệ thống, vui lòng thử lại.'));
    }
}

// GET: already logged in?
if (is_logged_in()) {
    redirect_to('/?p=servers');
}

// No form at /?p=login — redirect to home which has the form
redirect_to('/');
