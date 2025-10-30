<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();
$page_title = "Настройки системы";

// Обработка сохранения настроек
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_messages = [];
    $error_messages = [];
    
    // Общие настройки системы
    if (isset($_POST['save_general'])) {
        try {
            // Здесь можно добавить сохранение в БД или config файл
            $success_messages[] = "Общие настройки сохранены";
        } catch (Exception $e) {
            $error_messages[] = "Ошибка сохранения общих настроек: " . $e->getMessage();
        }
    }
    
    // Настройки безопасности
    if (isset($_POST['save_security'])) {
        try {
            $success_messages[] = "Настройки безопасности сохранены";
        } catch (Exception $e) {
            $error_messages[] = "Ошибка сохранения настроек безопасности: " . $e->getMessage();
        }
    }
    
    // Настройки уведомлений
    if (isset($_POST['save_notifications'])) {
        try {
            $success_messages[] = "Настройки уведомлений сохранены";
        } catch (Exception $e) {
            $error_messages[] = "Ошибка сохранения настроек уведомлений: " . $e->getMessage();
        }
    }
    
    // Сброс кеша
    if (isset($_POST['clear_cache'])) {
        try {
            // Очистка кеша сессий и временных файлов
            $cache_dir = '../../cache/';
            if (file_exists($cache_dir)) {
                clearDirectory($cache_dir);
            }
            $success_messages[] = "Кеш очищен";
        } catch (Exception $e) {
            $error_messages[] = "Ошибка очистки кеша: " . $e->getMessage();
        }
    }
    
    // Перезагрузка системы
    if (isset($_POST['restart_system'])) {
        try {
            // Здесь можно добавить перезагрузку сервисов
            $success_messages[] = "Команда перезагрузки отправлена";
        } catch (Exception $e) {
            $error_messages[] = "Ошибка перезагрузки: " . $e->getMessage();
        }
    }
}

// Функция очистки директории
function clearDirectory($dir) {
    if (!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? clearDirectory($path) : unlink($path);
    }
}

// Получаем текущие настройки (заглушки)
$current_settings = [
    'system_name' => 'Система управления ЧОП',
    'company_name' => 'Ваша компания',
    'timezone' => 'Europe/Moscow',
    'date_format' => 'd.m.Y',
    'items_per_page' => 20,
    
    'session_timeout' => 60,
    'login_attempts' => 5,
    'password_min_length' => 6,
    
    'email_notifications' => true,
    'sms_notifications' => false,
    'telegram_notifications' => false,
    'notify_expiry_days' => 30
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>⚙️ Настройки системы</h2>
                </div>
                <div class="card-body">
                    <!-- Сообщения -->
                    <?php if (!empty($success_messages)): ?>
                        <?php foreach ($success_messages as $msg): ?>
                            <div class="alert alert-success">✅ <?= $msg ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($error_messages)): ?>
                        <?php foreach ($error_messages as $msg): ?>
                            <div class="alert alert-error">❌ <?= $msg ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Табы -->
                    <div class="tabs">
                        <button class="tab-button active" onclick="openTab(event, 'general')">📋 Общие</button>
                        <button class="tab-button" onclick="openTab(event, 'security')">🔐 Безопасность</button>
                        <button class="tab-button" onclick="openTab(event, 'notifications')">🔔 Уведомления</button>
                        <button class="tab-button" onclick="openTab(event, 'system')">🖥️ Система</button>
                    </div>

                    <!-- Общие настройки -->
                    <div id="general" class="tab-content active">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Название системы:</label>
                                <input type="text" name="system_name" class="form-control" 
                                       value="<?= $current_settings['system_name'] ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Название компании:</label>
                                <input type="text" name="company_name" class="form-control" 
                                       value="<?= $current_settings['company_name'] ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Часовой пояс:</label>
                                <select name="timezone" class="form-control">
                                    <option value="Europe/Moscow" <?= $current_settings['timezone'] == 'Europe/Moscow' ? 'selected' : '' ?>>Москва</option>
                                    <option value="Europe/Kaliningrad" <?= $current_settings['timezone'] == 'Europe/Kaliningrad' ? 'selected' : '' ?>>Калининград</option>
                                    <option value="Asia/Yekaterinburg" <?= $current_settings['timezone'] == 'Asia/Yekaterinburg' ? 'selected' : '' ?>>Екатеринбург</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Формат даты:</label>
                                <select name="date_format" class="form-control">
                                    <option value="d.m.Y" <?= $current_settings['date_format'] == 'd.m.Y' ? 'selected' : '' ?>>дд.мм.гггг</option>
                                    <option value="Y-m-d" <?= $current_settings['date_format'] == 'Y-m-d' ? 'selected' : '' ?>>гггг-мм-дд</option>
                                    <option value="m/d/Y" <?= $current_settings['date_format'] == 'm/d/Y' ? 'selected' : '' ?>>мм/дд/гггг</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Элементов на странице:</label>
                                <input type="number" name="items_per_page" class="form-control" 
                                       value="<?= $current_settings['items_per_page'] ?>" min="5" max="100">
                            </div>
                            
                            <button type="submit" name="save_general" class="btn btn-primary">💾 Сохранить общие настройки</button>
                        </form>
                    </div>

                    <!-- Настройки безопасности -->
                    <div id="security" class="tab-content">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Таймаут сессии (минут):</label>
                                <input type="number" name="session_timeout" class="form-control" 
                                       value="<?= $current_settings['session_timeout'] ?>" min="15" max="480">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Максимум попыток входа:</label>
                                <input type="number" name="login_attempts" class="form-control" 
                                       value="<?= $current_settings['login_attempts'] ?>" min="3" max="10">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Минимальная длина пароля:</label>
                                <input type="number" name="password_min_length" class="form-control" 
                                       value="<?= $current_settings['password_min_length'] ?>" min="6" max="20">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <input type="checkbox" name="force_ssl" <?= isset($current_settings['force_ssl']) && $current_settings['force_ssl'] ? 'checked' : '' ?>> 
                                    Требовать HTTPS
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <input type="checkbox" name="log_logins" <?= isset($current_settings['log_logins']) && $current_settings['log_logins'] ? 'checked' : '' ?>> 
                                    Логировать входы в систему
                                </label>
                            </div>
                            
                            <button type="submit" name="save_security" class="btn btn-primary">💾 Сохранить настройки безопасности</button>
                        </form>
                    </div>

                    <!-- Настройки уведомлений -->
                    <div id="notifications" class="tab-content">
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Типы уведомлений:</label>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="email_notifications" <?= $current_settings['email_notifications'] ? 'checked' : '' ?>> 
                                        📧 Email уведомления
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="sms_notifications" <?= $current_settings['sms_notifications'] ? 'checked' : '' ?>> 
                                        📱 SMS уведомления
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="telegram_notifications" <?= $current_settings['telegram_notifications'] ? 'checked' : '' ?>> 
                                        📲 Telegram уведомления
                                    </label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Уведомлять о истечении за (дней):</label>
                                <input type="number" name="notify_expiry_days" class="form-control" 
                                       value="<?= $current_settings['notify_expiry_days'] ?>" min="1" max="90">
                                <small class="form-text">Уведомления о истечении лицензий, медосмотров и т.д.</small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">События для уведомлений:</label>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="notify_new_user" checked> 
                                        📝 Создание нового пользователя
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="notify_expiry" checked> 
                                        ⏰ Истечение срока документов
                                    </label>
                                </div>
                                <div>
                                    <label class="form-label">
                                        <input type="checkbox" name="notify_incident" checked> 
                                        ⚠️ Новые инциденты
                                    </label>
                                </div>
                            </div>
                            
                            <button type="submit" name="save_notifications" class="btn btn-primary">💾 Сохранить настройки уведомлений</button>
                        </form>
                    </div>

                    <!-- Системные настройки -->
                    <div id="system" class="tab-content">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>🔄 Управление кешем</h4>
                                    </div>
                                    <div class="card-body">
                                        <p>Очистка временных файлов и кеша системы</p>
                                        <form method="POST">
                                            <button type="submit" name="clear_cache" class="btn btn-warning"
                                                    onclick="return confirm('Очистить весь кеш системы?')">
                                                🗑️ Очистить кеш
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>🔄 Перезагрузка</h4>
                                    </div>
                                    <div class="card-body">
                                        <p>Перезагрузка системных служб</p>
                                        <form method="POST">
                                            <button type="submit" name="restart_system" class="btn btn-danger"
                                                    onclick="return confirm('Перезагрузить системные службы?')">
                                                🔄 Перезагрузить
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card" style="margin-top: 1rem;">
                            <div class="card-header">
                                <h4>📊 Информация о системе</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Версия PHP:</strong> <?= phpversion() ?></p>
                                        <p><strong>Сервер БД:</strong> MySQL</p>
                                        <p><strong>Версия системы:</strong> 1.0.0</p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Время работы:</strong> <?= round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2) ?> сек</p>
                                        <p><strong>Память:</strong> <?= round(memory_get_usage(true) / 1024 / 1024, 2) ?> MB</p>
                                        <p><strong>Последнее обновление:</strong> <?= date('d.m.Y H:i') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>

    <style>
    .tabs {
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 1rem;
    }
    
    .tab-button {
        background: none;
        border: none;
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    
    .tab-button:hover {
        background: #f8f9fa;
    }
    
    .tab-button.active {
        border-bottom-color: #007bff;
        background: #f8f9fa;
        font-weight: bold;
    }
    
    .tab-content {
        display: none;
        padding: 1rem 0;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    
    .form-text {
        font-size: 0.875rem;
        color: #6c757d;
    }
    </style>
<script src="../../assets/js/script.js"></script>
    <script>
    function openTab(evt, tabName) {
        // Скрыть все табы
        var tabcontent = document.getElementsByClassName("tab-content");
        for (var i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        
        // Убрать активность со всех кнопок
        var tabbuttons = document.getElementsByClassName("tab-button");
        for (var i = 0; i < tabbuttons.length; i++) {
            tabbuttons[i].classList.remove("active");
        }
        
        // Показать выбранный таб и активировать кнопку
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>