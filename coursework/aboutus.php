<?php
$page_title = "HeartBeat - О нас";
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
</head>
<body>
    <div class="display_inn">
        <div class="Logo" onclick="location.href='index.php';" style="cursor:pointer;">
            <h1>HEARTBEAT</h1>
        </div>
        <nav class="nav">
        <nav class="nav">
            <a class="decortiontext" href="map.php">Карта</a>
            <a class="decortiontext" href="methods.php">Свидания</a>
            <a class="decortiontext" href="aboutus.php">О нас</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="decortiontext" href="profile.php">Профиль (<?php echo $favorite_count; ?>)</a>
            <?php else: ?>
                <a class="decortiontext" href="register_login.php">Авторизация</a>
            <?php endif; ?>
        </nav>
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
            <h2 class="gradient-text">О нас</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Наша миссия - вдохновлять на романтику</h4>
        </div>
    </div>

    <div class="about-content">
        <div class="about-text">
            <p>Добро пожаловать на HeartBeat - ваш персональный гид по романтическим свиданиям! Мы создали этот проект, чтобы помочь вам сделать каждое свидание особенным и незабываемым.</p>
            <p>Наша команда состоит из романтиков и экспертов по организации свиданий, которые собрали лучшие идеи и места для проведения времени с вашей второй половинкой.</p>
            <p>Мы верим, что каждое свидание должно быть уникальным и запоминающимся. Поэтому мы предлагаем разнообразные варианты - от классических романтических ужинов до необычных приключений.</p>
            <p>Исследуйте наш сайт, находите вдохновение и создавайте свои собственные истории любви!</p>
        </div>
    </div>
</body>
</html> 