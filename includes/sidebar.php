<?php
$currentUserRole = $_SESSION['user_role'] ?? 'guest';
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Сайдбар -->
<aside class="sidebar">
    <div class="logo">
        <div>
           <center> <img src="/chop_system/742.jpg" width="" height="" alt="Логотип ЧОП"></center>
		   <button id="sidebarToggle" class="sidebar-toggle" title="Свернуть меню">&lArr;Свернуть<br>&rArr;Развернуть</button>
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
                    Дашборд
                </a>
                <a href="/chop_system/modules/medic/exams.php" class="nav-link <?php echo $currentPage == 'exams.php' ? 'active' : ''; ?>">
                    Медосмотры
                </a>
                <a href="/chop_system/modules/medic/employees.php" class="nav-link <?php echo $currentPage == 'employees.php' ? 'active' : ''; ?>">
                    Сотрудники
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
                <!-- ДОБАВЛЕНА ССЫЛКА НА ДАШБОРД -->
                <a href="/chop_system/modules/admin/dashboard.php" class="nav-link <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                    📊 Дашборд
                </a>
                <a href="/chop_system/modules/admin/users.php" class="nav-link <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
                    Пользователи
                </a>
                <a href="/chop_system/modules/admin/backup.php" class="nav-link <?php echo $currentPage == 'backup.php' ? 'active' : ''; ?>">
                    Бэкапы
                </a>
                <a href="/chop_system/modules/settings/system.php" class="nav-link <?php echo $currentPage == 'system.php' ? 'active' : ''; ?>">
                    Настройки
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