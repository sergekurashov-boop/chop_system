<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$userRole = getUserRole();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления сменами ЧОП</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                Панель управления - Добро пожаловать, <?php echo $_SESSION['user_full_name']; ?>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Статистика -->
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>Текущие смены</h3>
                                <p style="font-size: 2rem; color: var(--accent-blue);">12</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>Сотрудники</h3>
                                <p style="font-size: 2rem; color: var(--success-green);">45</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>Медосмотры</h3>
                                <p style="font-size: 2rem; color: var(--warning-orange);">8</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="card">
                            <div class="card-body">
                                <h3>Задачи</h3>
                                <p style="font-size: 2rem; color: var(--danger-red);">3</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Быстрое меню по ролям -->
                <div class="row" style="margin-top: 2rem;">
                    <?php if ($userRole === 'admin' || $userRole === 'senior'): ?>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Управление сменами</div>
                            <div class="card-body">
                                <a href="modules/senior/shifts.php" class="btn btn-primary">Создать смену</a>
                                <a href="modules/senior/assignments.php" class="btn btn-primary">Назначения</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($userRole === 'admin' || $userRole === 'medic'): ?>
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Медицинский отдел</div>
                            <div class="card-body">
                                <a href="modules/medic/exams.php" class="btn btn-primary">Медосмотры</a>
                                <a href="modules/medic/records.php" class="btn btn-primary">Записи</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Отчеты</div>
                            <div class="card-body">
                                <a href="modules/reports/daily.php" class="btn btn-primary">Ежедневные</a>
                                <a href="modules/reports/monthly.php" class="btn btn-primary">Месячные</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>