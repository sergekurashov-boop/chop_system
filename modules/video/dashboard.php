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
				<!-- Быстрые действия -->
<div class="quick-actions">
    <button class="action-btn" onclick="videoModule.showAllCameras()">📺 Все камеры</button>
    <button class="action-btn" onclick="videoModule.showOnlyOnline()">🟢 Только онлайн</button>
    <button class="action-btn" onclick="videoModule.refreshAll()">🔄 Обновить все</button>
    <button class="action-btn" onclick="videoModule.emergencyAlert()">🚨 Тревога</button>
</div>
            </div>
			<!-- После быстрых действий -->
<div class="hotkeys-hint">
    <small>Горячие клавиши: Ctrl+1-4 - быстрый доступ к камерам | Space - пауза | Esc - закрыть</small>
</div>

            <div class="cameras-grid" id="camerasGrid">
    <!-- Группа: Офисное здание -->
    <div class="location-group">
        <h3 class="group-title">🏢 Офисное здание</h3>
        <div class="group-cameras">
            <div class="camera-card" onclick="videoModule.showCamera('kpp', 'КПП Главный', 'Центральный вход')">
                <div class="camera-preview">📹</div>
                <div class="camera-info">
                    <div class="camera-name">КПП Главный</div>
                    <div class="camera-location">Центральный вход</div>
                    <div class="camera-status status-online">● ONLINE</div>
                </div>
            </div>
            
            <div class="camera-card" onclick="videoModule.showCamera('hall', 'Холл 2 этаж', 'Основной холл')">
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
            <div class="camera-card" onclick="videoModule.showCamera('parking', 'Парковка', 'Южная парковка')">
                <div class="camera-preview">📹</div>
                <div class="camera-info">
                    <div class="camera-name">Парковка</div>
                    <div class="camera-location">Южная парковка</div>
                    <div class="camera-status status-offline">● OFFLINE</div>
                </div>
            </div>
            
            <div class="camera-card" onclick="videoModule.showCamera('warehouse', 'Склад №1', 'Основной склад')">
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

    <!-- Модальное окно для просмотра видео -->
	<div class="video-container">
    <video id="videoPlayer" controls autoplay>
        Ваш браузер не поддерживает видео тег.
    </video>
    <div class="video-info">
        <span id="connectionStatus">● Подключаемся...</span>
        <span id="cameraInfo"></span>
        <button onclick="videoModule.takeScreenshot()" class="screenshot-btn">📸 Снимок</button>
    </div>
</div>
    <div id="videoModal" class="video-modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Просмотр камеры</h2>
            <div class="video-container">
                <video id="videoPlayer" controls autoplay>
                    Ваш браузер не поддерживает видео тег.
                </video>
                <div class="video-info">
                    <span id="connectionStatus">● Подключаемся...</span>
                    <span id="cameraInfo"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ПОДКЛЮЧАЕМ СКРИПТЫ В КОНЦЕ ТЕЛА -->
    <!-- Основной скрипт системы -->
    <script src="../../assets/js/script.js"></script>
    
    <!-- Скрипты видеомодуля -->
    <script src="<?php echo BASE_URL; ?>/modules/video/assets/js/video-generator.js"></script>
    <script src="<?php echo BASE_URL; ?>/modules/video/assets/js/video.js"></script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>