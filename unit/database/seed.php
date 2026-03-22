<?php
$pdo = new PDO('sqlite:users.db');
$pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY, name TEXT)');
$pdo->exec("INSERT OR IGNORE INTO users (id, name) VALUES (1, 'John Doe'), (2, 'Jane Smith')");
