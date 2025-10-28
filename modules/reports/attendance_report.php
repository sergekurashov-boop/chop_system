<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модуль в разработке</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <div class="card">
            <div class="card-header">Модуль в разработке</div>
            <div class="card-body">
                <p>Данный функционал находится в стадии разработки.</p>
                <a href="../dashboard.php" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
</body>
</html>