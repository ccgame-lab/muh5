<?php

declare(strict_types=1);

global $config;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    redirect_to('/');
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');

if (!preg_match('/^[a-zA-Z0-9_]{4,32}$/', $username)) {
    redirect_to('/?error=' . urlencode('Tài khoản chỉ cho phép a-zA-Z0-9_ từ 4-32 ký tự.'));
}
if (strlen($password) < 6) {
    redirect_to('/?error=' . urlencode('Mật khẩu tối thiểu 6 ký tự.'));
}

try {
    $sdk_conf = $config['sdk_db'] ?? null;
    if (!$sdk_conf) {
        $sdk_conf = $config['db_s1'];
        $sdk_conf['database'] = 'muh5_sdk';
        $sdk_conf['charset'] = 'utf8mb4';
    }

    $sdk_pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $sdk_conf['host'], $sdk_conf['port'], $sdk_conf['database'], $sdk_conf['charset'] ?? 'utf8mb4'),
        $sdk_conf['username'],
        $sdk_conf['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $game_pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $config['db_s1']['host'], $config['db_s1']['port'], $config['db_s1']['database']),
        $config['db_s1']['username'],
        $config['db_s1']['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check if username exists in sdk_db
    $stmt = $sdk_pdo->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        redirect_to('/?error=' . urlencode('Tài khoản đã tồn tại trong SDK.'));
    }

    // Check if username exists in db_s1
    $stmt = $game_pdo->prepare('SELECT account FROM globaluser WHERE account = ? LIMIT 1');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        redirect_to('/?error=' . urlencode('Tài khoản đã tồn tại trong Game.'));
    }

    $sdk_pdo->beginTransaction();

    try {
        // Insert SDK
        $stmt = $sdk_pdo->prepare('INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)');
        $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT),
            $email === '' ? null : $email
        ]);

        // Insert Game
        $stmt = $game_pdo->prepare('INSERT INTO globaluser (account, passwd) VALUES (?, ?)');
        $stmt->execute([
            $username,
            md5($password)
        ]);

        $sdk_pdo->commit();

        $_SESSION['user'] = [
            'id'           => 0, // In standard implementation this would be the db ID
            'username'     => $username,
            'display_name' => $username,
        ];

        redirect_to('/?p=servers');

    } catch (Exception $e) {
        $sdk_pdo->rollBack();
        throw $e;
    }

} catch (PDOException $e) {
    error_log('Register DB error: ' . $e->getMessage());
    redirect_to('/?error=' . urlencode('Lỗi hệ thống khi đăng ký.'));
}
