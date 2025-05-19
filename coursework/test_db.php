<?php
try {
    $db = new SQLite3('users.db');
    echo "Подключение к базе данных успешно!<br>";
    
    // Проверяем существование таблицы
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
    if ($result->fetchArray()) {
        echo "Таблица 'users' существует!<br>";
        
        // Показываем содержимое таблицы
        $result = $db->query("SELECT * FROM users");
        echo "Содержимое таблицы users:<br>";
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            print_r($row);
            echo "<br>";
        }
    } else {
        echo "Таблица 'users' не существует!<br>";
    }
} catch (Exception $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
}
?> 