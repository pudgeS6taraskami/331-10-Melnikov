<?php
session_start();
require_once 'db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: register_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Получаем избранные места пользователя
$stmt = $db->prepare('SELECT * FROM favorites WHERE user_id = ?');
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = "HeartBeat - Избранное";
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
        .favorites-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 30px;
        }

        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .favorite-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .favorite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .favorite-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .favorite-content {
            padding: 20px;
        }

        .favorite-title {
            color: #ff6b6b;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .favorite-description {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .favorite-type {
            display: inline-block;
            padding: 5px 10px;
            background: #ff6b6b;
            color: white;
            border-radius: 15px;
            font-size: 12px;
        }

        .empty-favorites {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .empty-favorites p {
            color: #666;
            margin-bottom: 20px;
        }

        .explore-button {
            display: inline-block;
            padding: 12px 25px;
            background: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .explore-button:hover {
            background: #ff5252;
            transform: translateY(-2px);
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
            <h2 class="gradient-text">Избранное</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Ваши сохраненные места для свиданий</h4>
        </div>
    </div>

    <div class="favorites-container">
        <?php if (empty($favorites)): ?>
            <div class="empty-favorites">
                <h3>У вас пока нет избранных мест</h3>
                <p>Начните исследовать карту и сохраняйте понравившиеся места для будущих свиданий!</p>
                <a href="map.php" class="explore-button">Исследовать карту</a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($favorites as $favorite): ?>
                    <div class="favorite-card">
                        <div class="favorite-content">
                            <h3 class="favorite-title"><?php echo htmlspecialchars($favorite['title']); ?></h3>
                            <p class="favorite-description"><?php echo htmlspecialchars($favorite['description']); ?></p>
                            <span class="favorite-type"><?php echo htmlspecialchars($favorite['type']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 