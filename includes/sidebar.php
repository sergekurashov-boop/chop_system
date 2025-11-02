<?php
$currentUserRole = $_SESSION['user_role'] ?? 'guest';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Сайдбар -->
<aside class="sidebar">
    <div class="logo">
        <div class="sidebar-header">
		
            <div class="logo-container">
               <center> <h3>НАВИГАЦИЯ</h3></center>
            </div>
			
            <button id="sidebarToggle" class="sidebar-toggle" title="Свернуть/развернуть сайдбар">
                <span class="toggle-icon">←</span>
                <span class="toggle-text">Свернуть</span>
            </button>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <!-- Главная -->
        <div class="nav-item">
            <a href="/chop_system/index.php" class="nav-link <?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                <span class="nav-icon">🏠</span>
                <span class="nav-text">Главная</span>
            </a>
        </div>
		<!-- Заявки на охрану -->
<?php if (in_array($currentUserRole, ['admin', 'senior', 'manager'])): ?>
<div class="nav-section">
    <div class="nav-header">
        <span class="nav-icon">📋</span>
        <span class="nav-text">Заявки на охрану</span>
        <span class="nav-arrow">▼</span>
    </div>
    <div class="nav-submenu">
        <a href="/chop_system/modules/requests/requests_list.php" class="nav-link <?php echo $currentPage == 'requests_list.php' ? 'active' : ''; ?>">
            📊 Все заявки
        </a>
        <a href="/chop_system/modules/requests/request_create.php" class="nav-link <?php echo $currentPage == 'request_create.php' ? 'active' : ''; ?>">
            ➕ Создать заявку
        </a>
    </div>
</div>
<?php endif; ?>
<!-- Сотрудники -->
<?php if (in_array($currentUserRole, ['admin', 'senior', 'manager'])): ?>
<div class="nav-section">
    <div class="nav-header">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Сотрудники</span>
        <span class="nav-arrow">▼</span>
    </div>
    <div class="nav-submenu">
        <a href="/chop_system/modules/staff/staff_list.php" class="nav-link">📋 Все сотрудники</a>
        <a href="/chop_system/modules/staff/staff_add.php" class="nav-link">➕ Добавить сотрудника</a>
    </div>
</div>
<?php endif; ?>

            <!-- 📹 ВИДЕОНАБЛЮДЕНИЕ -->
        <?php if (in_array($currentUserRole, ['admin', 'senior', 'dispatcher'])): ?>
        <div class="nav-section">
            <div class="nav-header">
                <span class="nav-icon">🎥</span>
                <span class="nav-text">Видеонаблюдение</span>
                <span class="nav-arrow">▼</span>
            </div>
            <div class="nav-submenu">
                <a href="/chop_system/modules/video/dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    📹 Панель управления
                </a>
                <a href="/chop_system/modules/video/demo.php" class="nav-link <?php echo $currentPage == 'demo.php' ? 'active' : ''; ?>">
                    🎥 Демонстрация
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Смены -->
        <?php if (in_array($currentUserRole, ['admin', 'senior', 'guard'])): ?>
        <div class="nav-section">
            <div class="nav-header">
                <span class="nav-icon">📅</span>
                <span class="nav-text">Смены</span>
                <span class="nav-arrow">▼</span>
            </div>
            <div class="nav-submenu">
                <a href="/chop_system/modules/senior/shifts.php" class="nav-link <?php echo $currentPage == 'shifts.php' ? 'active' : ''; ?>">
                    Управление сменами
                </a>
                <a href="/chop_system/modules/senior/shift_journal.php" class="nav-link <?php echo $currentPage == 'shift_journal.php' ? 'active' : ''; ?>">
                    Журнал учета
                </a>
                <a href="/chop_system/modules/senior/shift_assign.php" class="nav-link <?php echo $currentPage == 'shift_assign.php' ? 'active' : ''; ?>">
                    Назначения
                </a>
                <?php if (in_array($currentUserRole, ['admin', 'senior'])): ?>
                <a href="/chop_system/modules/senior/instructions.php" class="nav-link <?php echo $currentPage == 'instructions.php' ? 'active' : ''; ?>">
                    Инструктажи
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Медицина -->
        <?php if (in_array($currentUserRole, ['admin', 'medic'])): ?>
        <div class="nav-section">
            <div class="nav-header">
                <span class="nav-icon">🏥</span>
                <span class="nav-text">Медицина</span>
                <span class="nav-arrow">▼</span>
            </div>
            <div class="nav-submenu">
                <a href="/chop_system/modules/medic/dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Дашборд
                </a>
                <a href="/chop_system/modules/medic/medical_cards.php" class="nav-link <?php echo $currentPage == 'medical_cards.php' ? 'active' : ''; ?>">
                    📋 Медкарты
                </a>
                <a href="/chop_system/modules/medic/exams.php" class="nav-link <?php echo $currentPage == 'exams.php' ? 'active' : ''; ?>">
                    🩺 Медосмотры
                </a>
                <a href="/chop_system/modules/medic/employees.php" class="nav-link <?php echo $currentPage == 'employees.php' ? 'active' : ''; ?>">
                    👥 Сотрудники
                </a>
                <a href="/chop_system/modules/medic/reports.php" class="nav-link <?php echo $currentPage == 'reports.php' ? 'active' : ''; ?>">
                    📈 Отчеты
                </a>
                <a href="/chop_system/modules/medic/schedule.php" class="nav-link <?php echo $currentPage == 'schedule.php' ? 'active' : ''; ?>">
                    📅 График
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Отчеты -->
        <?php if (in_array($currentUserRole, ['admin', 'reports'])): ?>
        <div class="nav-section">
            <div class="nav-header">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Отчеты</span>
                <span class="nav-arrow">▼</span>
            </div>
            <div class="nav-submenu">
                <a href="/chop_system/modules/reports/dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    Все отчеты
                </a>
                <a href="/chop_system/modules/reports/shift_report.php" class="nav-link <?php echo $currentPage == 'shift_report.php' ? 'active' : ''; ?>">
                    За смену
                </a>
                <a href="/chop_system/modules/reports/monthly_report.php" class="nav-link <?php echo $currentPage == 'monthly_report.php' ? 'active' : ''; ?>">
                    За месяц
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Администрирование -->
        <?php if ($currentUserRole === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-header">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Администрирование</span>
                <span class="nav-arrow">▼</span>
            </div>
            <div class="nav-submenu">
                <a href="/chop_system/modules/admin/dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Дашборд
                </a>
                <a href="/chop_system/modules/admin/users.php" class="nav-link <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
                    👥 Пользователи
                </a>
                <a href="/chop_system/modules/admin/backup.php" class="nav-link <?php echo $currentPage == 'backup.php' ? 'active' : ''; ?>">
                    💾 Бэкапы
                </a>
                <a href="/chop_system/modules/admin/settings.php" class="nav-link <?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">
                    ⚙️ Настройки системы
                </a>
                <a href="/chop_system/modules/admin/system/logs.php" class="nav-link <?php echo $currentPage == 'logs.php' ? 'active' : ''; ?>">
                    📋 Логи системы
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Выход -->
        <div class="nav-item">
            <a href="/chop_system/logout.php" class="nav-link" style="color: var(--danger-gray);">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Выход (<?php echo $_SESSION['user_full_name'] ?? 'Пользователь'; ?>)</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Кнопка мобильного меню -->
<div class="mobile-menu-toggle">
    <span>☰</span>
</div>
