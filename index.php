<?php

require_once 'includes/config.php';
require_once 'includes/auth.php';

// Редирект неавторизованных пользователей на титульную страницу
if (!isLoggedIn()) {
    header("Location: landing.php");
    exit;
}

$userRole = getUserRole();

// Получаем реальную статистику из базы данных
$pdo = getDB();

// Текущие активные смены
$stmt = $pdo->query("SELECT COUNT(*) as count FROM shifts WHERE start_datetime <= NOW() AND end_datetime >= NOW()");
$active_shifts = $stmt->fetch()['count'];

// Всего сотрудников
$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE is_active = 1");
$total_employees = $stmt->fetch()['count'];

// Просроченные медосмотры
$stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE medical_exam_expiry < CURDATE()");
$expired_medical = $stmt->fetch()['count'];

// Задачи (инструктажи не пройдены)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM shift_assignments WHERE briefing_completed = 0 AND shift_id IN (SELECT id FROM shifts WHERE end_datetime >= NOW())");
$pending_tasks = $stmt->fetch()['count'];
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
<!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
       
    <?php 
	include 'includes/header.php';
	include 'includes/sidebar.php'; ?>
     
    <!-- Основной контент -->
    <div class="main-content">
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
                                    <p style="font-size: 2rem; color: var(--accent-gray);"><?php echo $active_shifts; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Сотрудники</h3>
                                    <p style="font-size: 2rem; color: var(--success-gray);"><?php echo $total_employees; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Медосмотры</h3>
                                    <p style="font-size: 2rem; color: var(--warning-gray);"><?php echo $expired_medical; ?></p>
                                    <small>просрочено</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card">
                                <div class="card-body">
                                    <h3>Задачи</h3>
                                    <p style="font-size: 2rem; color: var(--danger-gray);"><?php echo $pending_tasks; ?></p>
                                    <small>ожидают инструктажа</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Быстрое меню по ролям -->
                    <div class="row" style="margin-top: 2rem;">
                        <?php if ($userRole === 'admin' || $userRole === 'senior'): ?>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">📅 Управление сменами</div>
                                <div class="card-body">
                                    <a href="modules/senior/shifts.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Создать смену</a>
                                    <a href="modules/senior/shift_assign.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Назначения</a>
                                    <a href="modules/senior/shift_journal.php" class="btn btn-primary" style="display: block;">Журнал смен</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($userRole === 'admin' || $userRole === 'medic'): ?>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">🏥 Медицинский отдел</div>
                                <div class="card-body">
                                    <a href="modules/medic/dashboard.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Главная</a>
                                    <a href="modules/medic/exams.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Медосмотры</a>
                                    <a href="modules/medic/employees.php" class="btn btn-primary" style="display: block;">Сотрудники</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">📊 Отчеты</div>
                                <div class="card-body">
                                    <a href="modules/reports/dashboard.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Все отчеты</a>
                                    <a href="modules/reports/shift_report.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">За смену</a>
                                    <a href="modules/reports/monthly_report.php" class="btn btn-primary" style="display: block;">За месяц</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Дополнительные модули для админа -->
                    <?php if ($userRole === 'admin'): ?>
                    <div class="row" style="margin-top: 2rem;">
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">⚙️ Администрирование</div>
                                <div class="card-body">
                                    <a href="modules/admin/users.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Пользователи</a>
                                    <a href="modules/admin/backup.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Бэкапы</a>
                                    <a href="modules/settings/system.php" class="btn btn-primary" style="display: block;">Настройки</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">📚 Инструктажи</div>
                                <div class="card-body">
                                    <a href="modules/senior/instructions.php" class="btn btn-primary" style="margin-bottom: 0.5rem; display: block;">Управление</a>
                                    <a href="modules/senior/instruction_conduct.php" class="btn btn-primary" style="display: block;">Проведение</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script src="assets/js/sidebar.js"></script>
    <!-- Footer -->
    <footer style="background: var(--dark-gray); color: white; padding: 2rem 0; text-align: center;">
        <div class="logo">
            <a href="https://www.deepseek.com" target="_blank" style="color: #FFFF00; font-weight: bold;">
                <small>Технологическая платформа DeepSeek</small>
            </a>
        </div>
        <div class="container">
            <p>🛡️ CHOP Manager - Система управления частным охранным предприятием</p>
            <p>© 2025 Все права защищены</p>
        </div>
    </footer>
</body>
</html>