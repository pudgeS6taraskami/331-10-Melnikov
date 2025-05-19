<?php
try {
    // Убедимся, что папка data существует
    if (!file_exists('data')) {
        mkdir('data', 0777, true);
    }

    // Путь к файлу базы данных
    $db_path = __DIR__ . '/data/users.db';
    
    // Создаем подключение к базе данных
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создаем таблицу пользователей, если она не существует
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Создаем таблицу избранных мест, если она не существует
    $db->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        type TEXT,
        image_url TEXT,
        latitude REAL,
        longitude REAL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Устанавливаем права на файл базы данных
    chmod($db_path, 0666);
} catch(PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    die();
}
?> 