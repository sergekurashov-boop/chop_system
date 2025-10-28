<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('medic') && !hasAccess('admin')) {
    die('Доступ запрещен');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинские карты сотрудников</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>👥 Медицинские карты сотрудников</h2>
                <a href="dashboard.php" class="btn btn-secondary">← Назад</a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Модуль в разработке</strong><br>
                    Здесь будут медицинские карты и история осмотров сотрудников
                </div>
                
                <!-- Будут: поиск, фильтры, медкарты -->
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>