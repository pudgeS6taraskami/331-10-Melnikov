<?php
session_start();
require_once 'db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: register_login.php');
    exit();
}

// Получаем информацию о пользователе из базы данных
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare('SELECT username, created_at FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title = "HeartBeat - Личный кабинет";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .info-card h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
        }

        .profile-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .action-button {
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            background: #ff6b6b;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .logout-button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-button:hover {
            background: #ff5252;
        }
    </style>
</head>
<body>
    <div class="display_inn">
        <div class="Logo" onclick="location.href='index.php';" style="cursor:pointer;">
            <h1>HEARTBEAT</h1>
        </div>
        <nav class="nav">
            <a class="decortiontext" href="methods.php">Свидания</a>
            <a class="decortiontext" href="map.php">Карта</a>
            <a class="decortiontext" href="aboutus.php">О нас</a>
            <a class="decortiontext" href="profile.php">Профиль</a>
            <form action="logout.php" method="post" style="display: inline;">
                <button type="submit" class="logout-button">Выйти</button>
            </form>
        </nav>
    </div>

    <div class="creative-header">
        <div class="floating-hearts">
            <div class="heart heart-1"></div>
            <div class="heart heart-2"></div>
            <div class="heart heart-3"></div>
            <div class="heart heart-4"></div>
            <div class="heart heart-5"></div>
            <div class="heart heart-6"></div>
            <div class="heart heart-7"></div>
            <div class="heart heart-8"></div>
            <div class="heart heart-9"></div>
            <div class="heart heart-10"></div>
            <div class="heart heart-11"></div>
            <div class="heart heart-12"></div>
        </div>
        <div class="title-container">
            <h2 class="gradient-text">Личный кабинет</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Добро пожаловать, <?php echo htmlspecialchars($user['username']); ?>!</h4>
        </div>
    </div>

    <div class="profile-container">
        <div class="profile-info">
            <div class="info-card">
                <h3>Основная информация</h3>
                <p><strong>Имя пользователя:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
            </div>
            <div class="info-card">
                <h3>Статистика</h3>
                <p><strong>Сохранённые места</strong></p>
            </div>
        </div>

        <div class="profile-actions">
            <button class="action-button" onclick="location.href='edit_profile.php'">Редактировать профиль</button>
            <button class="action-button" onclick="location.href='favorites.php'">Избранное</button>
        </div>
    </div>
</body>
</html> 