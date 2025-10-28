<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('medic') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();

// Статистика для дашборда
$stmt = $pdo->query("SELECT COUNT(*) as total FROM employees WHERE is_active = 1");
$total_employees = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as expired FROM employees WHERE medical_exam_expiry < CURDATE()");
$expired_medical = $stmt->fetch()['expired'];

$stmt = $pdo->query("SELECT COUNT(*) as upcoming FROM employees WHERE medical_exam_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$upcoming_expiry = $stmt->fetch()['upcoming'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский кабинет - Главная</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>🏥 Медицинский кабинет</h2>
            </div>
            <div class="card-body">
                <!-- Статистика -->
                <div class="row">
                    <div class="col-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3><?php echo $total_employees; ?></h3>
                                <p>Всего сотрудников</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 style="color: #dc3545;"><?php echo $expired_medical; ?></h3>
                                <p>Просроченные медосмотры</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 style="color: #ffc107;"><?php echo $upcoming_expiry; ?></h3>
                                <p>Скоро истекают</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Быстрые действия -->
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Быстрые действия</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <a href="exams.php" class="btn btn-primary btn-block">📋 Медосмотры</a>
                            </div>
                            <div class="col-3">
                                <a href="employees.php" class="btn btn-success btn-block">👥 Сотрудники</a>
                            </div>
                            <div class="col-3">
                                <a href="reports.php" class="btn btn-info btn-block">📊 Отчеты</a>
                            </div>
                            <div class="col-3">
                                <a href="schedule.php" class="btn btn-warning btn-block">📅 График</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>