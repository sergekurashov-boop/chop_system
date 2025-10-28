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
    <title>Медицинские осмотры</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📋 Медицинские осмотры</h2>
                <a href="dashboard.php" class="btn btn-secondary">← Назад</a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Модуль в разработке</strong><br>
                    Здесь будет управление медицинскими осмотрами сотрудников
                </div>
                
                <div class="row">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Запланированные осмотры</div>
                            <div class="card-body">
                                <p>Список запланированных медосмотров</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">История осмотров</div>
                            <div class="card-body">
                                <p>Архив проведенных осмотров</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Новый осмотр</div>
                            <div class="card-body">
                                <button class="btn btn-primary">➕ Начать осмотр</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html><?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
checkAuth();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинские осмотры</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <div class="card">
            <div class="card-header">Медицинские осмотры</div>
            <div class="card-body">
                <p>Модуль в разработке</p>
                <a href="../index.php" class="btn btn-primary">На главную</a>
            </div>
        </div>
    </div>
</body>
</html>