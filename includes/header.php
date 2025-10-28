<?php
$currentUserRole = $_SESSION['user_role'] ?? 'guest';

// Определяем базовый URL
$base_url = '/chop_system';
?>
<header class="header">
    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo $base_url; ?>/index.php" style="color: white; text-decoration: none;">
                ЧОП Система
            </a>
        </div>
        <ul class="nav-menu">
            <li><a href="<?php echo $base_url; ?>/index.php">Главная</a></li>
            
            <?php if (in_array($currentUserRole, ['admin', 'senior', 'guard'])): ?>
            <li><a href="<?php echo $base_url; ?>/modules/senior/shifts.php">Смены</a></li>
            <?php endif; ?>
            
            <?php if (in_array($currentUserRole, ['admin', 'medic'])): ?>
            <li><a href="<?php echo $base_url; ?>/modules/medic/exams.php">Медосмотры</a></li>
            <?php endif; ?>
            
            <?php if (in_array($currentUserRole, ['admin', 'reports'])): ?>
            <li><a href="<?php echo $base_url; ?>/modules/reports/daily.php">Отчеты</a></li>
            <?php endif; ?>
            
            <?php if ($currentUserRole === 'admin'): ?>
<li><a href="../admin/users.php">Пользователи</a></li>
<li><a href="../admin/backup.php">Бэкапы</a></li> <!-- НОВАЯ ССЫЛКА -->
<li><a href="../settings/system.php">Настройки</a></li>
<?php endif; ?>
            
            <li><a href="<?php echo $base_url; ?>/logout.php">Выход (<?php echo htmlspecialchars($_SESSION['user_full_name']); ?>)</a></li>
        </ul>
    </nav>
</header>