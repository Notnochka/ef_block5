<?php

header('Content-Type: application/json');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/users') {
    $dbHost = getenv('DB_HOST');
    if ($dbHost !== false && $dbHost !== '') {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $dbHost,
            getenv('DB_PORT') ?: '3306',
            getenv('DB_DATABASE') ?: 'app_db'
        );
        $pdo = new PDO(
            $dsn,
            getenv('DB_USERNAME') ?: 'user',
            getenv('DB_PASSWORD') ?: 'password'
        );
    } else {
        $sqlitePath = dirname(__DIR__) . '/database/users.db';
        $pdo = new PDO('sqlite:' . $sqlitePath);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT id, name FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['users' => $users]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Not Found']);
}
