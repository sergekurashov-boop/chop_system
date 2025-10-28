<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <div class="card">
            <div class="card-header">Управление пользователями</div>
            <div class="card-body">
                <p>Модуль в разработке</p>
                <a href="../../index.php" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
</body>
</html>