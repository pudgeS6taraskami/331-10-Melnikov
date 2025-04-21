<?php

session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'register') {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password === $confirm_password) {
                if (strlen($password) >= 6) {
                    try {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
                        $stmt->execute([$username, $hashed_password]);
                        $_SESSION['user_id'] = $db->lastInsertId();
                        header('Location: profile.php');
                        exit();
                    } catch(PDOException $e) {
                        $error = 'Это имя пользователя уже занято';
                    }
                } else {
                    $error = 'Пароль должен содержать не менее 6 символов';
                }
            } else {
                $error = 'Пароли не совпадают';
            }
        } elseif ($_POST['action'] === 'login') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $stmt = $db->prepare('SELECT id, password FROM users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: profile.php');
                exit();
            } else {
                $error = 'Неверное имя пользователя или пароль';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartBeat - Регистрация и вход</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
    <style>
        .container {
            max-width: 400px;
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

        .switch-form {
            text-align: center;
            margin-top: 20px;
        }

        .switch-form button {
            background: none;
            border: none;
            color: #ff6b6b;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .switch-form button:hover {
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
            <a class="decortiontext" href="register_login.php">Авторизация</a>
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
            <h2 class="gradient-text">Регистрация и вход</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Присоединяйтесь к нашему сообществу</h4>
        </div>
    </div>

    <div class="container">
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="" style="display: none;">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label for="reg_username">Имя пользователя</label>
                <input type="text" id="reg_username" name="username" required>
            </div>
            <div class="form-group">
                <label for="reg_password">Пароль</label>
                <input type="password" id="reg_password" name="password" required>
            </div>
            <div class="form-group">
                <label for="reg_confirm_password">Подтвердите пароль</label>
                <input type="password" id="reg_confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="submit-button">Зарегистрироваться</button>
        </form>

        <form id="loginForm" method="POST" action="">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="login_username">Имя пользователя</label>
                <input type="text" id="login_username" name="username" required>
            </div>
            <div class="form-group">
                <label for="login_password">Пароль</label>
                <input type="password" id="login_password" name="password" required>
            </div>
            <button type="submit" class="submit-button">Войти</button>
        </form>

        <div class="switch-form">
            <button onclick="switchForm()" id="switchButton">Нет аккаунта? Зарегистрироваться</button>
        </div>
    </div>

    <script>
        function switchForm() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const switchButton = document.getElementById('switchButton');

            if (loginForm.style.display === 'none') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                switchButton.textContent = 'Нет аккаунта? Зарегистрироваться';
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                switchButton.textContent = 'Уже есть аккаунт? Войти';
            }
        }
    </script>
</body>
</html>
