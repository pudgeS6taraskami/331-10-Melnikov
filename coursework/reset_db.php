<?php
require_once 'db.php';

try {
    // Удаляем существующие таблицы
    $db->exec('DROP TABLE IF EXISTS favorites');
    $db->exec('DROP TABLE IF EXISTS users');

    // Создаем таблицы заново
    $db->exec('CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $db->exec('CREATE TABLE favorites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        type TEXT,
        visited INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )');

    echo "База данных успешно пересоздана!";
} catch(PDOException $e) {
    die('Ошибка при пересоздании базы данных: ' . $e->getMessage());
}
?> 