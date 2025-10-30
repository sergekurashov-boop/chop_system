<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$page_title = "Обслуживание системы";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../../includes/header.php'; 
    include '../../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>🛠️ Обслуживание системы</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        ⚠️ Внимание! Эти операции могут повлиять на работу системы.
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>🧹 Очистка системы</h4>
                                </div>
                                <div class="card-body">
                                    <p>Очистка временных файлов и кеша</p>
                                    <button class="btn btn-warning" onclick="confirmAction('Очистить кеш системы?')">
                                        🗑️ Очистить кеш
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>📊 Оптимизация БД</h4>
                                </div>
                                <div class="card-body">
                                    <p>Оптимизация таблиц базы данных</p>
                                    <button class="btn btn-info" onclick="confirmAction('Оптимизировать базу данных?')">
                                        🔄 Оптимизировать БД
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>🔍 Проверка целостности</h4>
                                </div>
                                <div class="card-body">
                                    <p>Проверка целостности данных системы</p>
                                    <button class="btn btn-primary" onclick="confirmAction('Выполнить проверку целостности?')">
                                        🔍 Проверить целостность
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>📋 Системная информация</h4>
                                </div>
                                <div class="card-body">
                                    <p><strong>Версия PHP:</strong> <?= phpversion() ?></p>
                                    <p><strong>Память:</strong> <?= round(memory_get_usage(true) / 1024 / 1024, 2) ?> MB</p>
                                    <p><strong>Время работы:</strong> <?= round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2) ?> сек</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../assets/js/script.js"></script>
    <script>
    function confirmAction(message) {
        if (confirm(message)) {
            alert('🚧 Функция в разработке');
        }
    }
    </script>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>