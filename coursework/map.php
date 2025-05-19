<?php
session_start();
require_once 'db.php';

$page_title = "HeartBeat - Карта свиданий";

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
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $user_id = $_SESSION['user_id'];

    try {
        // Проверяем, не добавлено ли уже это место в избранное
        $stmt = $db->prepare('SELECT COUNT(*) FROM favorites WHERE user_id = ? AND title = ?');
        $stmt->execute([$user_id, $title]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Это место уже в избранном']);
            exit;
        }

        $stmt = $db->prepare('INSERT INTO favorites (user_id, title, description, type, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$user_id, $title, $description, $type, $latitude, $longitude]);
        
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
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&family=Rubik+Mono+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Gidole&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container {
            width: 100%;
            height: 600px;
            margin: 20px auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        #map {
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .map-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #ff6b6b;
            border-top: 5px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .loading-text {
            color: #ff6b6b;
            font-size: 18px;
            text-align: center;
            margin-top: 10px;
        }

        .loading-dots {
            display: inline-block;
            animation: dots 1.5s infinite;
        }

        .map-error {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
            text-align: center;
        }
        
        .location-card {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .location-card h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
        }
        
        .location-card p {
            color: #666;
            margin-bottom: 5px;
        }
        
        .favorite-heart-button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 10px;
        }
        .location-type {
            display: inline-block;
            padding: 5px 10px;
            background: #ff6b6b;
            color: white;
            border-radius: 15px;
            font-size: 12px;
            margin-top: 10px;
        }

        .heart-marker {
            width: 40px;
            height: 40px;
            background: #ff6b6b;
            position: relative;
            transform: rotate(45deg);
            animation: pulse 2s infinite;
        }

        .heart-marker::before,
        .heart-marker::after {
            content: '';
            width: 40px;
            height: 40px;
            background: #ff6b6b;
            border-radius: 50%;
            position: absolute;
        }

        .heart-marker::before {
            left: -20px;
        }

        .heart-marker::after {
            top: -20px;
        }

        @keyframes pulse {
            0% {
                transform: rotate(45deg) scale(1);
            }
            50% {
                transform: rotate(45deg) scale(1.1);
            }
            100% {
                transform: rotate(45deg) scale(1);
            }
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes dots {
            0%, 20% { content: "."; }
            40% { content: ".."; }
            60% { content: "..."; }
            80%, 100% { content: ""; }
        }

        .error-button {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .error-button:hover {
            background: #ff5252;
            transform: scale(1.05);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 16px;
            margin-top: 10px;
        }

        .leaflet-popup-content-wrapper {
            border-radius: 10px;
            padding: 0;
        }

        .leaflet-popup-content {
            margin: 0;
        }

        .leaflet-popup-tip {
            background: #ff6b6b;
        }

        .city-selector {
            text-align: center;
            margin: 20px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }

        .city-selector select {
            padding: 10px 20px;
            font-size: 16px;
            border: 2px solid #ff6b6b;
            border-radius: 20px;
            background: white;
            color: #333;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .city-selector select:hover {
            border-color: #ff5252;
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.3);
        }

        .city-selector h3 {
            color: #ff6b6b;
            margin-bottom: 10px;
        }
    </style>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
</head>
<body>
    <div class="display_inn">
        <div class="Logo" onclick="location.href='index.php';" style="cursor:pointer;">
            <h1>HEARTBEAT</h1>
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
            <h2 class="gradient-text">Карта романтических мест</h2>
            <div class="sparkle-container">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
            <h4 class="subtitle">Найдите идеальное место для вашего свидания</h4>
        </div>
    </div>

    <div class="city-selector">
        <h3>Выберите город</h3>
        <select id="citySelect" onchange="changeCity()">
            <option value="spb">Санкт-Петербург</option>
            <option value="msk">Москва</option>
            <option value="ekb">Екатеринбург</option>
            <option value="kzn">Казань</option>
            <option value="nsk">Новосибирск</option>
        </select>
    </div>

    <div class="map-container">
        <div id="map"></div>
        <div class="map-loading" id="loading">
            <div class="loading-spinner"></div>
            <div class="loading-text">
                Загрузка карты<span class="loading-dots"></span><br>
                <small>Это может занять несколько секунд</small>
            </div>
        </div>
        <div class="map-error" id="error" style="display: none;">
            <h3>Ошибка загрузки карты</h3>
            <p>Пожалуйста, проверьте подключение к интернету</p>
            <div class="error-message" id="error-details"></div>
            <button class="error-button" onclick="initMap()">Попробовать снова</button>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        let currentCity = 'spb';

        // Данные о городах и местах
        const citiesData = {
            spb: {
                center: [59.9305477418292, 30.362548172608253],
                zoom: 12,
                locations: [
                    {
                        position: [59.9375, 30.308611],
                        title: "Дворцовая площадь",
                        description: "Исторический центр города, красивые виды на Зимний дворец",
                        type: "Площадь"
                    },
                    {
                        position: [59.934167, 30.305833],
                        title: "Стрелка Васильевского острова",
                        description: "Романтическая набережная с видом на Неву и Петропавловскую крепость",
                        type: "Набережная"
                    },
                    {
                        position: [59.943889, 30.335833],
                        title: "Летний сад",
                        description: "Старейший сад города с фонтанами и скульптурами",
                        type: "Парк"
                    },
                    {
                        position: [59.948889, 30.304722],
                        title: "Марсово поле",
                        description: "Просторный парк с фонтанами и памятниками",
                        type: "Парк"
                    },
                    {
                        position: [59.943056, 30.301944],
                        title: "Михайловский сад",
                        description: "Уютный сад с прудами и мостиками",
                        type: "Парк"
                    },
                    {
                        position: [59.944722, 30.323889],
                        title: "Новая Голландия",
                        description: "Современное пространство с ресторанами и мероприятиями",
                        type: "Пространство"
                    },
                    {
                        position: [59.935833, 30.32],
                        title: "Казанский собор",
                        description: "Величественный собор с колоннадой",
                        type: "Собор"
                    },
                    {
                        position: [59.934722, 30.302778],
                        title: "Эрмитаж",
                        description: "Один из крупнейших музеев мира",
                        type: "Музей"
                    },
                    {
                        position: [59.948889, 30.304722],
                        title: "Русский музей",
                        description: "Крупнейший музей русского искусства",
                        type: "Музей"
                    },
                    {
                        position: [59.955281864966885, 30.367108912766913],
                        title: "Поющие фонтаны",
                        description: "Красивый фонтан с музыкальным сопровождением",
                        type: "Фонтан"
                    }
                ]
            },
            msk: {
                center: [55.7558, 37.6173],
                zoom: 12,
                locations: [
                    {
                        position: [55.7522, 37.6156],
                        title: "Красная площадь",
                        description: "Главная площадь Москвы с историческими памятниками",
                        type: "Площадь"
                    },
                    {
                        position: [55.7495, 37.6239],
                        title: "Парк Горького",
                        description: "Популярный парк с множеством развлечений",
                        type: "Парк"
                    },
                    {
                        position: [55.7522, 37.6156],
                        title: "ВДНХ",
                        description: "Выставка достижений народного хозяйства",
                        type: "Выставка"
                    }
                ]
            },
            ekb: {
                center: [56.8389, 60.6057],
                zoom: 12,
                locations: [
                    {
                        position: [56.8389, 60.6057],
                        title: "Плотинка",
                        description: "Исторический центр города",
                        type: "Площадь"
                    },
                    {
                        position: [56.8389, 60.6057],
                        title: "Храм на Крови",
                        description: "Православный храм-памятник",
                        type: "Храм"
                    }
                ]
            },
            kzn: {
                center: [55.7961, 49.1064],
                zoom: 12,
                locations: [
                    {
                        position: [55.7961, 49.1064],
                        title: "Кремль",
                        description: "Исторический центр Казани",
                        type: "Кремль"
                    },
                    {
                        position: [55.7961, 49.1064],
                        title: "Мечеть Кул-Шариф",
                        description: "Главная мечеть Татарстана",
                        type: "Мечеть"
                    }
                ]
            },
            nsk: {
                center: [55.0302, 82.9204],
                zoom: 12,
                locations: [
                    {
                        position: [55.0302, 82.9204],
                        title: "Площадь Ленина",
                        description: "Центральная площадь города",
                        type: "Площадь"
                    },
                    {
                        position: [55.0302, 82.9204],
                        title: "Театр оперы и балета",
                        description: "Крупнейший театр Сибири",
                        type: "Театр"
                    }
                ]
            }
        };

        // Функция смены города
        function changeCity() {
            const select = document.getElementById('citySelect');
            currentCity = select.value;
            initMap();
        }

        // Инициализация карты
        async function initMap() {
            const loadingElement = document.getElementById('loading');
            const errorElement = document.getElementById('error');
            const errorDetails = document.getElementById('error-details');
            
            try {
                loadingElement.style.display = 'flex';
                errorElement.style.display = 'none';

                // Очистка предыдущих маркеров
                markers.forEach(marker => marker.remove());
                markers = [];

                // Получение данных о текущем городе
                const cityData = citiesData[currentCity];
                
                // Создание карты
                if (map) {
                    map.setView(cityData.center, cityData.zoom);
                } else {
                    map = L.map('map').setView(cityData.center, cityData.zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                }

                // Создание маркеров
                cityData.locations.forEach(location => {
                    const markerDiv = document.createElement("div");
                    markerDiv.className = "heart-marker";
                    
                    const marker = L.marker(location.position, {
                        icon: L.divIcon({
                            className: 'heart-icon',
                            html: markerDiv,
                            iconSize: [40, 40],
                            iconAnchor: [20, 20]
                        })
                    }).addTo(map);

                    const popup = L.popup().setContent(`
                        <div class="location-card">
                            <h3>${location.title}</h3>
                            <p>${location.description}</p>
                            <span class="location-type">${location.type}</span>
                            <button class="favorite-heart-button" onclick="addToFavorites('${location.title}', '${location.description}', '${location.type}', ${location.position[0]}, ${location.position[1]})">Добавить в избранное</button>
                        </div>
                    `);

                    marker.bindPopup(popup);
                    markers.push(marker);
                });

                loadingElement.style.display = 'none';
            } catch (error) {
                console.error('Ошибка при загрузке карты:', error);
                loadingElement.style.display = 'none';
                errorElement.style.display = 'flex';
                errorDetails.textContent = error.message;
            }
        }

        // Инициализация карты при загрузке страницы
        document.addEventListener('DOMContentLoaded', initMap);

        function addToFavorites(title, description, type, latitude, longitude) {
            const button = event.currentTarget;
            if (button.disabled) return;

            const data = {
                action: 'add_favorite',
                title: title,
                description: description,
                type: type,
                latitude: latitude,
                longitude: longitude
            };

            fetch('map.php', {
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