<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

// Разрешаем доступ всем авторизованным для демо
$pageTitle = "Демонстрация модуля видеонаблюдения";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/video.css">
    <style>
        .demo-section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: 20px 0;
            border-left: 5px solid #2E8B57;
        }
        
        .integration-examples {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .integration-card {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .integration-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .tech-specs {
            background: #e8f4fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .code-example {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 15px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <?php 
    include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <div class="main-content">
        <div class="container">
            <!-- Заголовок демо -->
            <div class="demo-section">
                <h1>🎥 Демонстрация модуля видеонаблюдения</h1>
                <p class="module-subtitle">Технологии интеграции и возможности системы</p>
            </div>

            <!-- Основные возможности -->
            <div class="demo-section">
                <h2>🚀 Ключевые возможности</h2>
                <div class="integration-examples">
                    <div class="integration-card">
                        <h3>📡 Поддержка протоколов</h3>
                        <ul>
                            <li>RTSP (Real Time Streaming Protocol)</li>
                            <li>ONVIF для автоматического обнаружения</li>
                            <li>HTTP/MJPEG для веб-камер</li>
                            <li>HLS для браузерного воспроизведения</li>
                        </ul>
                    </div>
                    
                    <div class="integration-card">
                        <h3>🔧 Интеграции</h3>
                        <ul>
                            <li>REST API для внешних систем</li>
                            <li>WebSocket для реального времени</li>
                            <li>Базы данных (MySQL/PostgreSQL)</li>
                            <li>Файловые системы для архива</li>
                        </ul>
                    </div>
                    
                    <div class="integration-card">
                        <h3>🛡️ Безопасность</h3>
                        <ul>
                            <li>HTTPS для защищенной передачи</li>
                            <li>Аутентификация камер</li>
                            <li>Шифрование видеопотоков</li>
                            <li>Разграничение прав доступа</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Примеры интеграции -->
            <div class="demo-section">
                <h2>🔌 Примеры интеграции с системами</h2>
                
                <div class="integration-card">
                    <h3>1. Интеграция с СКУД (контроль доступа)</h3>
                    <p>При срабатывании события в СКУД автоматически показываем соответствующую камеру:</p>
                    <div class="code-example">
// Пример вебхука от СКУД<br>
POST /api/integration/skud-event<br>
{<br>
  "event_type": "door_access",<br>
  "door_id": "entrance_1", <br>
  "card_number": "A1B2C3",<br>
  "timestamp": "2024-01-15T10:30:00Z"<br>
}
                    </div>
                </div>

                <div class="integration-card">
                    <h3>2. Интеграция с тревожной сигнализацией</h3>
                    <p>При тревоге автоматически записываем видео и уведомляем оператора:</p>
                    <div class="code-example">
// Обработчик тревожного события<br>
function handleAlarmEvent(alarmData) {<br>
  // Включаем запись на камерах зоны<br>
  startRecording(alarmData.zone_cameras);<br>
  <br>
  // Показываем уведомление оператору<br>
  showOperatorAlert(alarmData);<br>
  <br>
  // Сохраняем событие в журнал<br>
  logSecurityEvent(alarmData);<br>
}
                    </div>
                </div>

                <div class="integration-card">
                    <h3>3. REST API для мобильных приложений</h3>
                    <p>Предоставляем API для мобильного доступа к камерам:</p>
                    <div class="code-example">
// Получение списка камер<br>
GET /api/v1/cameras<br>
Authorization: Bearer {token}<br>
<br>
// Получение live-потока<br>
GET /api/v1/cameras/{id}/stream<br>
<br>
// Просмотр архива<br>
GET /api/v1/cameras/{id}/archive?start=2024-01-15T10:00:00Z&end=2024-01-15T11:00:00Z
                    </div>
                </div>
            </div>

            <!-- Технические спецификации -->
            <div class="tech-specs">
                <h2>🔧 Технические требования</h2>
                <div class="integration-examples">
                    <div class="integration-card">
                        <h3>Серверная часть</h3>
                        <ul>
                            <li>PHP 7.4+ / Node.js</li>
                            <li>MySQL 5.7+ / PostgreSQL</li>
                            <li>FFmpeg для обработки видео</li>
                            <li>Минимум 4GB RAM</li>
                            <li>SSD для архива видео</li>
                        </ul>
                    </div>
                    
                    <div class="integration-card">
                        <h3>Поддерживаемые камеры</h3>
                        <ul>
                            <li>Hikvision, Dahua, Axis</li>
                            <li>Any ONVIF-совместимые</li>
                            <li>IP-камеры с RTSP</li>
                            <li>Веб-камеры USB</li>
                        </ul>
                    </div>
                    
                    <div class="integration-card">
                        <h3>Клиентская часть</h3>
                        <ul>
                            <li>Modern browsers (Chrome, Firefox, Safari)</li>
                            <li>Поддержка WebRTC / HLS</li>
                            <li>Адаптивный дизайн</li>
                            <li>PWA для мобильных устройств</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Демо интерфейса -->
            <div class="demo-section">
                <h2>🎯 Демонстрация интерфейса</h2>
                <p>Ниже представлен рабочий прототип интерфейса оператора:</p>
                
                <div style="text-align: center; margin: 20px 0;">
                    <a href="dashboard.php" class="btn btn-primary" style="padding: 15px 30px; font-size: 1.1rem;">
                        🚀 Перейти к рабочему интерфейсу
                    </a>
                </div>
                
                <div class="integration-examples">
                    <div class="integration-card">
                        <h3>📊 Статистика в реальном времени</h3>
                        <p>Мониторинг состояния системы, онлайн-камер, нагрузки</p>
                    </div>
                    
                    <div class="integration-card">
                        <h3>🔍 Умный поиск камер</h3>
                        <p>Быстрый поиск по названию, расположению, статусу</p>
                    </div>
                    
                    <div class="integration-card">
                        <h3>⌨️ Горячие клавиши</h3>
                        <p>Быстрый доступ к камерам для оператора</p>
                    </div>
                </div>
            </div>

            <!-- Контакты для интеграции -->
            <div class="demo-section" style="background: #e8f5e8;">
                <h2>📞 Контакты для технической интеграции</h2>
                <p>Готовы к подключению ваших камер и систем! Обращайтесь для:</p>
                <ul>
                    <li>🔧 Технической консультации по интеграции</li>
                    <li>🎥 Подключения существующих систем видеонаблюдения</li>
                    <li>🛠️ Настройки API и вебхуков</li>
                    <li>📊 Демонстрации работы на вашем оборудовании</li>
                </ul>
                
                <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 5px;">
                    <strong>Технический специалист:</strong> [Имя специалиста]<br>
                    <strong>Телефон:</strong> [Номер телефона]<br>
                    <strong>Email:</strong> [Email для связи]<br>
                    <strong>Поддержка:</strong> 24/7 для критических систем
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script src="assets/js/video.js"></script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>