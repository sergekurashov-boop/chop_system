<?php
// modules/video/dashboard.php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

// Разрешаем доступ админам, старшим и диспетчерам
if (!hasAccess('admin') && !hasAccess('senior') && !hasAccess('dispatcher')) {
    die('
        <div style="padding: 20px; text-align: center;">
            <h2>⚠️ Доступ запрещен</h2>
            <p>Недостаточно прав для доступа к модулю видеонаблюдения</p>
            <a href="/chop_system/index.php">На главную</a>
        </div>
    ');
}

$pageTitle = "Модуль видеонаблюдения";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Подключаем стили видеомодуля -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/modules/video/assets/css/video.css">
    <style>
        /* Дополнительные стили для видео */
        .video-container {
            width: 100%;
            background: #000;
            border-radius: 8px;
            overflow: hidden;
        }
        
        #videoPlayer {
            width: 100%;
            min-height: 400px;
            background: #000;
        }
        
        .video-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        .screenshot-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="video-module-container">
            <div class="module-header">
                <h1>🎥 Модуль видеонаблюдения <span class="new-badge">NEW</span></h1>
                <p class="module-subtitle">Мгновенный доступ к камерам в реальном времени</p>
            </div>

            <!-- Статус системы -->
            <div class="system-status">
                <div class="status-item">
                    <span class="status-label">Статус системы:</span>
                    <span class="status-value online">● ONLINE</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Камеры:</span>
                    <span class="status-value">4/4</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Время работы:</span>
                    <span class="status-value" id="uptime">0д 0ч 0м</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Последнее обновление:</span>
                    <span class="status-value" id="lastUpdate">--:--:--</span>
                </div>
            </div>

            <div class="search-box">
                <input type="text" id="cameraSearch" placeholder="🔍 Найти камеру..." class="search-input">
            </div>

            <!-- Быстрые действия -->
            <div class="quick-actions">
                <button class="action-btn" onclick="showAllCameras()">📺 Все камеры</button>
                <button class="action-btn" onclick="showOnlyOnline()">🟢 Только онлайн</button>
                <button class="action-btn" onclick="refreshAll()">🔄 Обновить все</button>
                <button class="action-btn" onclick="emergencyAlert()">🚨 Тревога</button>
            </div>

            <!-- Подсказка горячих клавиш -->
            <div class="hotkeys-hint">
                <small>Горячие клавиши: Ctrl+1-4 - быстрый доступ к камерам | Space - пауза | Esc - закрыть</small>
            </div>

            <!-- Сетка камер -->
            <div class="cameras-grid" id="camerasGrid">
                <!-- Группа: Офисное здание -->
                <div class="location-group">
                    <h3 class="group-title">🏢 Офисное здание</h3>
                    <div class="group-cameras">
                        <div class="camera-card" onclick="openCamera('kpp', 'КПП Главный', 'Центральный вход')">
                            <div class="camera-preview">📹</div>
                            <div class="camera-info">
                                <div class="camera-name">КПП Главный</div>
                                <div class="camera-location">Центральный вход</div>
                                <div class="camera-status status-online">● ONLINE</div>
                            </div>
                        </div>
                        
                        <div class="camera-card" onclick="openCamera('hall', 'Холл 2 этаж', 'Основной холл')">
                            <div class="camera-preview">📹</div>
                            <div class="camera-info">
                                <div class="camera-name">Холл 2 этаж</div>
                                <div class="camera-location">Основной холл</div>
                                <div class="camera-status status-online">● ONLINE</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Группа: Складской комплекс -->
                <div class="location-group">
                    <h3 class="group-title">🏭 Складской комплекс</h3>
                    <div class="group-cameras">
                        <div class="camera-card" onclick="openCamera('parking', 'Парковка', 'Южная парковка')">
                            <div class="camera-preview">📹</div>
                            <div class="camera-info">
                                <div class="camera-name">Парковка</div>
                                <div class="camera-location">Южная парковка</div>
                                <div class="camera-status status-offline">● OFFLINE</div>
                            </div>
                        </div>
                        
                        <div class="camera-card" onclick="openCamera('warehouse', 'Склад №1', 'Основной склад')">
                            <div class="camera-preview">📹</div>
                            <div class="camera-info">
                                <div class="camera-name">Склад №1</div>
                                <div class="camera-location">Основной склад</div>
                                <div class="camera-status status-online">● ONLINE</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для просмотра видео -->
    <div id="videoModal" class="video-modal">
        <div class="modal-content">
            <span class="close" onclick="closeVideo()">&times;</span>
            <h2 id="modalTitle">Просмотр камеры</h2>
            
            <div class="video-container">
                <div id="videoPlayer">
                    <!-- Анимированное видео будет здесь -->
                </div>
                <div class="video-info">
                    <span id="connectionStatus">● Ожидание...</span>
                    <span id="cameraInfo">Камера не выбрана</span>
                    <button onclick="takeScreenshot()" class="screenshot-btn">📸 Снимок</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ЭКСТРЕННЫЙ ТЕСТ -->
    <div style="position: fixed; bottom: 10px; right: 10px; z-index: 10000;">
        <button onclick="emergencyTest()" style="background: red; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer;">
            🔴 ЭКСТРЕННЫЙ ТЕСТ
        </button>
    </div>

    <!-- Основной скрипт системы -->
    <script src="../../assets/js/script.js"></script>
    
    <script>
    // Глобальные переменные для анимации
    let currentAnimationId = null;
    let currentStopAnimation = null;

    // Функции быстрых действий
    function showAllCameras() {
        const cards = document.querySelectorAll('.camera-card');
        cards.forEach(card => card.style.display = 'block');
        console.log('✅ Показаны все камеры');
    }

    function showOnlyOnline() {
        const cards = document.querySelectorAll('.camera-card');
        cards.forEach(card => {
            const status = card.querySelector('.camera-status').textContent;
            card.style.display = status.includes('ONLINE') ? 'block' : 'none';
        });
        console.log('✅ Показаны только онлайн камеры');
    }

    function refreshAll() {
        location.reload();
    }

    function emergencyAlert() {
        alert('🚨 ТРЕВОГА! Уведомление отправлено старшему смены!');
        console.log('🚨 Тревога активирована');
    }

    function takeScreenshot() {
        alert('📸 Снимок сохранен в галерею');
        console.log('📸 Скриншот сделан');
    }

    // Открытие камеры
    function openCamera(cameraId, cameraName, location) {
        console.log('🎥 Открываем камеру:', cameraId);
        
        const modal = document.getElementById('videoModal');
        const video = document.getElementById('videoPlayer');
        
        if (modal && video) {
            // Останавливаем предыдущую анимацию
            if (currentStopAnimation) {
                currentStopAnimation();
            }
            
            // Обновляем информацию
            document.getElementById('modalTitle').textContent = cameraName;
            document.getElementById('cameraInfo').textContent = location;
            document.getElementById('connectionStatus').textContent = '● LIVE';
            document.getElementById('connectionStatus').style.color = '#28a745';
            
            // Запускаем анимированное видео для выбранной камеры
            currentStopAnimation = startAnimatedVideo(video, cameraId, cameraName);
            
            // Показываем модалку
            modal.style.display = 'block';
        }
    }

    // Анимированный тестовый режим
    function emergencyTest() {
        console.log('=== АНИМИРОВАННЫЙ ТЕСТ ===');
        openCamera('kpp', 'КПП Главный - ТЕСТ', 'Центральный вход');
    }

    // Создание анимированного видео
    function startAnimatedVideo(videoContainer, cameraId = 'kpp', cameraName = 'Тестовая камера') {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 800;
        canvas.height = 450;
        canvas.style.display = 'block';
        
        // Очищаем контейнер и добавляем canvas
        videoContainer.innerHTML = '';
        videoContainer.appendChild(canvas);
        videoContainer.style.padding = '0';
        videoContainer.style.background = '#000';
        videoContainer.style.display = 'flex';
        videoContainer.style.justifyContent = 'center';
        videoContainer.style.alignItems = 'center';
        
        let animationId;
        let time = 0;
        let cars = [];
        let people = [];
        
        // Инициализируем объекты в зависимости от камеры
        function initObjects() {
            if (cameraId === 'kpp') {
                // КПП - машины и охрана
                cars = [
                    { x: -100, y: 350, speed: 2, color: '#ff4444', direction: 1 },
                    { x: 900, y: 320, speed: 1.8, color: '#4444ff', direction: -1 }
                ];
                people = [
                    { x: 200, y: 300, speed: 0.8, direction: 1, type: 'guard' },
                    { x: 600, y: 280, speed: 1.2, direction: -1, type: 'visitor' }
                ];
            } else if (cameraId === 'hall') {
                // Холл - только люди
                cars = [];
                people = [
                    { x: 100, y: 300, speed: 0.7, direction: 1, type: 'employee' },
                    { x: 400, y: 320, speed: 0.9, direction: -1, type: 'employee' },
                    { x: 700, y: 280, speed: 0.5, direction: 1, type: 'visitor' }
                ];
            } else if (cameraId === 'parking') {
                // Парковка - много машин
                cars = [
                    { x: -100, y: 350, speed: 1.5, color: '#ff4444', direction: 1 },
                    { x: 300, y: 320, speed: 0, color: '#888888', direction: 1 }, // Припаркованная
                    { x: 900, y: 340, speed: 1.8, color: '#44ff44', direction: -1 }
                ];
                people = [
                    { x: 250, y: 300, speed: 0.6, direction: 1, type: 'driver' }
                ];
            } else {
                // Склад - погрузчики и рабочие
                cars = [
                    { x: 200, y: 350, speed: 0.8, color: '#ffaa00', direction: 1, type: 'forklift' },
                    { x: 600, y: 330, speed: 0.9, color: '#ffaa00', direction: -1, type: 'forklift' }
                ];
                people = [
                    { x: 100, y: 300, speed: 0.7, direction: 1, type: 'worker' },
                    { x: 500, y: 280, speed: 0.5, direction: -1, type: 'worker' }
                ];
            }
        }
        
        function drawFrame() {
            time += 0.016; // ~60 FPS
            
            // Очищаем canvas
            ctx.fillStyle = '#1a2a1a';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Рисуем фон в зависимости от камеры
            drawBackground();
            
            // Обновляем и рисуем объекты
            updateAndDrawObjects();
            
            // Эффекты камеры наблюдения
            drawCameraEffects();
            
            // Запускаем следующий кадр
            animationId = requestAnimationFrame(drawFrame);
        }
        
        function drawBackground() {
            if (cameraId === 'kpp') {
                drawKPPBackground();
            } else if (cameraId === 'hall') {
                drawHallBackground();
            } else if (cameraId === 'parking') {
                drawParkingBackground();
            } else {
                drawWarehouseBackground();
            }
        }
        
        function drawKPPBackground() {
            // Небо
            ctx.fillStyle = '#2c5282';
            ctx.fillRect(0, 0, canvas.width, 200);
            
            // Дорога
            ctx.fillStyle = '#4a5568';
            ctx.fillRect(0, 350, canvas.width, 100);
            
            // Разметка
            ctx.strokeStyle = 'yellow';
            ctx.lineWidth = 3;
            ctx.setLineDash([20, 20]);
            ctx.beginPath();
            ctx.moveTo(0, 400);
            ctx.lineTo(canvas.width, 400);
            ctx.stroke();
            ctx.setLineDash([]);
            
            // Будка охраны
            ctx.fillStyle = '#8b4513';
            ctx.fillRect(350, 250, 100, 100);
            ctx.fillStyle = '#87ceeb';
            ctx.fillRect(370, 270, 30, 30); // Окно
        }
        
        function drawHallBackground() {
            // Стены
            ctx.fillStyle = '#f8f9fa';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Пол
            ctx.fillStyle = '#e9ecef';
            ctx.fillRect(0, 300, canvas.width, 150);
            
            // Колонны
            ctx.fillStyle = '#dee2e6';
            ctx.fillRect(200, 150, 30, 150);
            ctx.fillRect(600, 150, 30, 150);
            
            // Освещение
            ctx.fillStyle = 'rgba(255, 255, 200, 0.4)';
            ctx.beginPath();
            ctx.arc(400, 100, 40, 0, Math.PI * 2);
            ctx.fill();
        }
        
        function drawParkingBackground() {
            // Асфальт
            ctx.fillStyle = '#4a5568';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Парковочные места
            ctx.strokeStyle = 'white';
            ctx.lineWidth = 2;
            for (let i = 0; i < 6; i++) {
                ctx.strokeRect(100 + i * 120, 200, 80, 120);
            }
        }
        
        function drawWarehouseBackground() {
            // Складское помещение
            ctx.fillStyle = '#2d3748';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            // Стеллажи
            ctx.fillStyle = '#4a5568';
            ctx.fillRect(100, 150, 50, 200);
            ctx.fillRect(300, 150, 50, 200);
            ctx.fillRect(500, 150, 50, 200);
            ctx.fillRect(700, 150, 50, 200);
            
            // Освещение
            ctx.fillStyle = 'rgba(255, 255, 150, 0.3)';
            ctx.fillRect(0, 0, canvas.width, 20);
        }
        
        function updateAndDrawObjects() {
            // Машины
            cars.forEach(car => {
                if (car.speed > 0) {
                    car.x += car.speed * car.direction;
                    
                    // Если уехала за границу - возвращаем
                    if (car.direction > 0 && car.x > canvas.width + 100) {
                        car.x = -100;
                    } else if (car.direction < 0 && car.x < -100) {
                        car.x = canvas.width + 100;
                    }
                }
                
                if (car.type === 'forklift') {
                    drawForklift(car);
                } else {
                    drawCar(car);
                }
            });
            
            // Люди
            people.forEach(person => {
                person.x += person.speed * person.direction;
                
                // Если ушел за границу - возвращаем
                if (person.direction > 0 && person.x > canvas.width + 50) {
                    person.x = -50;
                } else if (person.direction < 0 && person.x < -50) {
                    person.x = canvas.width + 50;
                }
                
                drawPerson(person);
            });
        }
        
        function drawCar(car) {
            // Кузов
            ctx.fillStyle = car.color;
            ctx.fillRect(car.x, car.y, 80, 30);
            
            // Кабина
            ctx.fillRect(car.x + 50, car.y - 20, 30, 20);
            
            // Колеса
            ctx.fillStyle = '#2d3748';
            ctx.fillRect(car.x + 10, car.y + 25, 15, 10);
            ctx.fillRect(car.x + 55, car.y + 25, 15, 10);
            
            // Фары (мигают)
            ctx.fillStyle = Math.sin(time * 10) > 0 ? '#ffff00' : '#ffaa00';
            if (car.direction > 0) {
                ctx.fillRect(car.x + 75, car.y + 10, 5, 5);
            } else {
                ctx.fillRect(car.x, car.y + 10, 5, 5);
            }
        }
        
        function drawForklift(forklift) {
            // Основная часть
            ctx.fillStyle = forklift.color;
            ctx.fillRect(forklift.x, forklift.y - 15, 60, 25);
            
            // Кабина
            ctx.fillStyle = '#3498db';
            ctx.fillRect(forklift.x + 40, forklift.y - 30, 20, 15);
            
            // Вилы
            ctx.fillStyle = '#95a5a6';
            ctx.fillRect(forklift.x - 20, forklift.y - 5, 20, 5);
            ctx.fillRect(forklift.x - 20, forklift.y - 15, 20, 5);
            
            // Колеса
            ctx.fillStyle = '#2c3e50';
            ctx.fillRect(forklift.x + 10, forklift.y + 5, 15, 8);
            ctx.fillRect(forklift.x + 35, forklift.y + 5, 15, 8);
        }
        
        function drawPerson(person) {
            const walkOffset = Math.sin(time * 10) * 3;
            
            if (person.type === 'guard') {
                // Охранник в форме
                ctx.fillStyle = '#2c3e50';
                ctx.fillRect(person.x, person.y - 40 + walkOffset, 15, 40);
                
                // Желтые полосы
                ctx.fillStyle = '#f1c40f';
                ctx.fillRect(person.x, person.y - 30 + walkOffset, 15, 3);
                ctx.fillRect(person.x, person.y - 15 + walkOffset, 15, 3);
            } else if (person.type === 'worker') {
                // Рабочий
                ctx.fillStyle = '#e74c3c';
                ctx.fillRect(person.x, person.y - 40 + walkOffset, 12, 40);
            } else {
                // Посетитель/сотрудник
                ctx.fillStyle = person.type === 'employee' ? '#3498db' : '#48bb78';
                ctx.fillRect(person.x, person.y - 40 + walkOffset, 12, 40);
            }
            
            // Голова
            ctx.fillStyle = '#ffdbac';
            ctx.fillRect(person.x - 3, person.y - 50 + walkOffset, 18, 10);
        }
        
        function drawCameraEffects() {
            // Таймстамп
            const now = new Date();
            ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
            ctx.fillRect(canvas.width - 200, 10, 190, 25);
            ctx.fillStyle = '#00ff00';
            ctx.font = '12px monospace';
            ctx.fillText(now.toLocaleTimeString('ru-RU'), canvas.width - 190, 25);
            
            // Название камеры
            ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
            ctx.fillRect(10, 10, 200, 25);
            ctx.fillStyle = '#00ff00';
            ctx.font = '12px monospace';
            ctx.fillText(cameraName, 20, 25);
            
            // Сетка камеры
            ctx.strokeStyle = 'rgba(0, 255, 0, 0.1)';
            ctx.lineWidth = 1;
            
            // Вертикальные линии
            for (let x = 0; x < canvas.width; x += 50) {
                ctx.beginPath();
                ctx.moveTo(x, 0);
                ctx.lineTo(x, canvas.height);
                ctx.stroke();
            }
            
            // Горизонтальные линии
            for (let y = 0; y < canvas.height; y += 50) {
                ctx.beginPath();
                ctx.moveTo(0, y);
                ctx.lineTo(canvas.width, y);
                ctx.stroke();
            }
            
            // Шум
            for (let i = 0; i < 10; i++) {
                const x = Math.random() * canvas.width;
                const y = Math.random() * canvas.height;
                const size = Math.random() * 2;
                ctx.fillStyle = `rgba(255, 255, 255, ${Math.random() * 0.05})`;
                ctx.fillRect(x, y, size, size);
            }
            
            // Мигающий статус
            ctx.fillStyle = Math.sin(time * 5) > 0 ? '#00ff00' : '#004400';
            ctx.beginPath();
            ctx.arc(30, 30, 4, 0, Math.PI * 2);
            ctx.fill();
        }
        
        // Запускаем анимацию
        initObjects();
        drawFrame();
        
        // Функция для остановки анимации
        return function stopAnimation() {
            if (animationId) {
                cancelAnimationFrame(animationId);
            }
        };
    }

    // Обработчик закрытия модального окна
    function closeVideo() {
        const modal = document.getElementById('videoModal');
        const video = document.getElementById('videoPlayer');
        
        if (modal) {
            modal.style.display = 'none';
        }
        
        // Останавливаем анимацию
        if (currentStopAnimation) {
            currentStopAnimation();
            currentStopAnimation = null;
        }
        
        // Очищаем видео контейнер
        if (video) {
            video.innerHTML = '<div style="color: #0f0; padding: 20px; text-align: center;">Готов к работе</div>';
        }
    }

    // Обработчик закрытия по клику вне окна
    window.onclick = function(event) {
        const modal = document.getElementById('videoModal');
        if (event.target === modal) {
            closeVideo();
        }
    }

    // Обработчик Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeVideo();
        }
    });

    // Обновление статуса системы
    function updateSystemStatus() {
        const now = new Date();
        const lastUpdateElement = document.getElementById('lastUpdate');
        const uptimeElement = document.getElementById('uptime');
        
        if (lastUpdateElement) {
            lastUpdateElement.textContent = now.toLocaleTimeString();
        }
        
        if (uptimeElement) {
            const startTime = new Date(now.getTime() - 2 * 60 * 60 * 1000);
            const diff = now - startTime;
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            uptimeElement.textContent = `${days}д ${hours}ч ${minutes}м`;
        }
    }

    // Поиск камер
    function setupSearch() {
        const searchInput = document.getElementById('cameraSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.camera-card');
                
                cards.forEach(card => {
                    const name = card.querySelector('.camera-name').textContent.toLowerCase();
                    const location = card.querySelector('.camera-location').textContent.toLowerCase();
                    
                    card.style.display = (name.includes(searchTerm) || location.includes(searchTerm)) ? 'block' : 'none';
                });
            });
        }
    }

    // Инициализация при загрузке
    document.addEventListener('DOMContentLoaded', function() {
        updateSystemStatus();
        setInterval(updateSystemStatus, 60000);
        setupSearch();
        console.log('🎥 Модуль видеонаблюдения инициализирован');
    });
    </script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>