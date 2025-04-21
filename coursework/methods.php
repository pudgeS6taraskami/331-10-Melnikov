<?php
session_start();
require_once 'db.php';

$page_title = "HeartBeat - Варианты свиданий";

// Получаем количество избранных мест для текущего пользователя
$favorite_count = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $favorite_count = $stmt->fetchColumn();
}

// Обработка добавления в избранное
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_favorite') {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не авторизован']);
        exit;
    }

    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $user_id = $_SESSION['user_id'];

    try {
        // Проверяем, не добавлено ли уже это место в избранное
        $stmt = $db->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ? AND title = ?');
        $stmt->execute([$user_id, $title]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Это место уже в избранном']);
            exit;
        }

        $stmt = $db->prepare('INSERT INTO favorites (user_id, title, description, type) VALUES (?, ?, ?, ?)');
        $stmt->execute([$user_id, $title, $description, $type]);
        
        // Получаем обновленное количество избранных
        $stmt = $db->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $new_count = $stmt->fetchColumn();
        
        echo json_encode(['success' => true, 'count' => $new_count]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении в избранное']);
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HeartBeat - Варианты свиданий</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
    <script src="slider.js" defer></script>
</head>
<body>
    <div class="display_inn">
        <div class="Logo" onclick="location.href='index.php';" style="cursor:pointer;">
            <h1>HeartBeat</h1>
        </div>
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
            <h2 class="gradient-text">Варианты свиданий</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Выберите идеальное свидание для вас и вашей половинки</h4>
        </div>
    </div>

    <style>
        .favorite-heart-button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>

    <section class="slider">
        <div class="slider-container">
            <div class="slider-item active">
                <img src="brr.jpg" alt="Свидания на природе" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания на природе</h2>
                    <p>Наслаждайтесь романтическим ужином на природе, в уединенном месте, специально подобранном для вас. Мягкие пледы, вкусные закуски и прекрасные виды — все это создаст атмосферу уюта и близости</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания на природе', 'Наслаждайтесь романтическим ужином на природе, в уединенном месте, специально подобранном для вас. Мягкие пледы, вкусные закуски и прекрасные виды — все это создаст атмосферу уюта и близости', 'Природа')"></button>
                </div>
            </div>
            <div class="slider-item">
                <img src="home.png" alt="Свидания дома" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания дома</h2>
                    <p>Свидания дома могут быть уютными, романтичными и полными теплых моментов</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания дома', 'Свидания дома могут быть уютными, романтичными и полными теплых моментов', 'Дома')"></button>
                </div>
            </div>
            <div class="slider-item">
                <img src="street.jpg" alt="Свидания на улице" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания, прогулки по улице</h2>
                    <p>Иногда просто прогулка по улице может стать замечательным свиданием</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания, прогулки по улице', 'Иногда просто прогулка по улице может стать замечательным свиданием', 'Прогулка')"></button>
                </div>
            </div>
            <div class="slider-item">
                <img src="sky.jpg" alt="Свидания под красивым небом" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания под красивым небом</h2>
                    <p>Бывает, что просто посмотреть на небо может быть замечательным и незабываемым свиданием</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания под красивым небом', 'Бывает, что просто посмотреть на небо может быть замечательным и незабываемым свиданием', 'Небо')"></button>
                </div>
            </div>
            <div class="slider-item">
                <img src="far_sky.jpg" alt="Свидания далеко от дома" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания далеко от дома</h2>
                    <p>Поехать со своим любимым человеком на далёкие расстояния может быть замечательным и незабываемым свиданием</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания далеко от дома', 'Поехать со своим любимым человеком на далёкие расстояния может быть замечательным и незабываемым свиданием', 'Путешествие')"></button>
                </div>
            </div>
            <div class="slider-item">
                <img src="winter_street.jpg" alt="Свидания зимой" class="slider-img">
                <div class="slider-content">
                    <h2>Свидания зимой</h2>
                    <p>Времена года не имеют значения, главное, что вы вместе. Зимние свидания могут быть замечательными</p>
                    <button class="favorite-heart-button" onclick="addToFavorites('Свидания зимой', 'Времена года не имеют значения, главное, что вы вместе. Зимние свидания могут быть замечательными', 'Зима')"></button>
                </div>
            </div>
        </div>
        <div class="slider-buttons">
            <button class="slider-button" onclick="showPreviousSlide()">←</button>
            <button class="slider-button" onclick="showNextSlide()">→</button>
        </div>
    </section>

    <script>
        var parent = document.getElementById("modalParent"),
            section = document.querySelector("section");

        function openPopup() {
            parent.style.display = "block";
            section.style.filter = "none";
        }

        function closePopup() {
            parent.style.display = "none";
            section.style.filter = "none";
        }

        parent.addEventListener("click", function(e) {
            if (e.target.className == "modal-parent") {
                closePopup();
            }
        });

        function addToFavorites(title, description, type) {
            const button = event.currentTarget;
            if (button.disabled) return;

            const data = {
                action: 'add_favorite',
                title: title,
                description: description,
                type: type
            };

            fetch('methods.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.add('added');
                    button.disabled = true;
                    // Обновляем счетчик в навигации
                    const profileLink = document.querySelector('a[href="profile.php"]');
                    if (profileLink) {
                        profileLink.textContent = `Профиль (${data.count})`;
                    }
                } else {
                    if (data.message === 'Пользователь не авторизован') {
                        window.location.href = 'register_login.php';
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка при добавлении в избранное');
            });
        }
    </script>
</body>
</html> 