<?php
$currentUserRole = $_SESSION['user_role'] ?? 'guest';
?>
<header class="header">
    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo BASE_URL; ?>/index.php" style="color: white; text-decoration: none;">
                ЧОП ____________
            </a>
			<a href="https://www.deepseek.com" target="_blank" style="color: #FFFF00; font-weight: bold;">
                <small>Технологическая платформа DeepSeek</small>            </a>
        </div>
         <ul class="nav-menu">
            <li><a href="<?php echo BASE_URL; ?>/index.php">Главная</a></li>
            
            <?php if (in_array($currentUserRole, ['admin', 'senior', 'guard'])): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">📋 Смены</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>/modules/senior/shifts.php">Управление сменами</a></li>
					<li><a href="<?php echo BASE_URL; ?>/modules/senior/instructions.php">Инструктажи</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/modules/senior/shift_journal.php">Журнал учета</a></li>
                    <?php if (in_array($currentUserRole, ['admin', 'senior'])): ?>
                    <li><a href="<?php echo BASE_URL; ?>/modules/senior/shift_assign.php">Назначения</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Медосмотры - подменю -->
<?php if (in_array($currentUserRole, ['admin', 'medic'])): ?>
<li class="dropdown">
    <a href="#" class="dropdown-toggle">🏥 Медицина</a>
    <ul class="dropdown-menu">
        <li><a href="<?php echo BASE_URL; ?>/modules/medic/dashboard.php">Главная</a></li>
        <li><a href="<?php echo BASE_URL; ?>/modules/medic/exams.php">Медосмотры</a></li>
        <li><a href="<?php echo BASE_URL; ?>/modules/medic/employees.php">Сотрудники</a></li>
    </ul>
</li>
<?php endif; ?>
            <!-- Отчеты - подменю -->
<?php if (in_array($currentUserRole, ['admin', 'reports'])): ?>
<li class="dropdown">
    <a href="#" class="dropdown-toggle">📊 Отчеты</a>
    <ul class="dropdown-menu">
        <li><a href="<?php echo BASE_URL; ?>/modules/reports/dashboard.php">Все отчеты</a></li>
        <li><a href="<?php echo BASE_URL; ?>/modules/reports/shift_report.php">За смену</a></li>
        <li><a href="<?php echo BASE_URL; ?>/modules/reports/monthly_report.php">За месяц</a></li>
    </ul>
</li>
<?php endif; ?>
            
            <?php if ($currentUserRole === 'admin'): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">⚙️ Администрирование</a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>/modules/admin/users.php">Пользователи</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/modules/admin/backup.php">Бэкапы</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/modules/settings/system.php">Настройки</a></li>
                </ul>
            </li>
            <?php endif; ?>
			<!-- Демо-доступ (только если не в демо-режиме) -->
<?php if (!isset($_SESSION['demo_mode'])): ?>
<li><a href="<?php echo BASE_URL; ?>/demo.php" style="color: #ff6b6b; font-weight: bold;">🚀 Демо-доступ</a></li>
<?php endif; ?>
            
           
            <li><a href="<?php echo BASE_URL; ?>/logout.php">Выход (<?php echo htmlspecialchars($_SESSION['user_full_name']); ?>)</a></li>
        </ul>
    </nav>
</header>