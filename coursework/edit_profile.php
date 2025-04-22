<?php
session_start();
require_once 'db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: register_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Обработка формы изменения пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['current_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Проверяем текущий пароль
        $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                if (strlen($new_password) >= 6) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $db->prepare('UPDATE users SET password = ? WHERE id = ?');
                    $stmt->execute([$hashed_password, $user_id]);
                    $success = 'Пароль успешно изменен!';
                } else {
                    $error = 'Новый пароль должен содержать не менее 6 символов';
                }
            } else {
                $error = 'Новые пароли не совпадают';
            }
        } else {
            $error = 'Неверный текущий пароль';
        }
    }
}

$page_title = "HeartBeat - Редактирование профиля";
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
        .edit-profile-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ff6b6b;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff5252;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
        }

        .error-message {
            color: #ff5252;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(255, 82, 82, 0.1);
            border-radius: 10px;
        }

        .success-message {
            color: #4caf50;
            margin-bottom: 20px;
            padding: 10px;
            background: rgba(76, 175, 80, 0.1);
            border-radius: 10px;
        }

        .submit-button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .submit-button:hover {
            background: #ff5252;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #ff6b6b;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #ff5252;
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
            <h2 class="gradient-text">Редактирование профиля</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Измените данные вашего профиля</h4>
        </div>
    </div>

    <div class="edit-profile-container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Текущий пароль</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">Новый пароль</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Подтвердите новый пароль</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="submit-button">Изменить пароль</button>
        </form>

        <a href="profile.php" class="back-link">← Вернуться в профиль</a>
    </div>
</body>
</html> 