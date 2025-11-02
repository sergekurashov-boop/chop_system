<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ПРОВЕРКА ПУТЕЙ
$config_path = __DIR__ . '/../../includes/config.php';
if (!file_exists($config_path)) {
    die("❌ config.php не найден по пути: " . $config_path);
}
require_once $config_path;

$auth_path = __DIR__ . '/../../includes/auth.php';
if (!file_exists($auth_path)) {
    die("❌ auth.php не найден по пути: " . $auth_path);
}
require_once $auth_path;

require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/request_functions.php';

if (!isLoggedIn()) {
    header("Location: ../../landing.php");
    exit;
}

$userRole = getUserRole();
$pdo = getDB();
$requests = getSecurityRequests();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на охрану</title>
   <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/sidebar.css">
</head>
<body>

<?php 
$header_path = __DIR__ . '/../../includes/header.php';
if (!file_exists($header_path)) {
    die("❌ header.php не найден: " . $header_path);
}
include $header_path; 

$sidebar_path = __DIR__ . '/../../includes/sidebar.php';
if (!file_exists($sidebar_path)) {
    die("❌ sidebar.php не найден: " . $sidebar_path);
}
include $sidebar_path; 
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📋 Заявки на охрану</h2>
                <a href="request_create.php" class="btn btn-primary">+ Новая заявка</a>
            </div>
            <div class="card-body">
                <?php if (count($requests) > 0): ?>
                    <p>Найдено заявок: <?php echo count($requests); ?></p>
                    <!-- Тут будет таблица -->
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>Заявок пока нет</p>
                        <a href="request_create.php" class="btn btn-primary">Создать первую заявку</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$footer_path = __DIR__ . '/../../includes/footer.php';
if (file_exists($footer_path)) {
    include $footer_path;
}
?>
<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/sidebar.js"></script>
</body>
</html>