<?php
$currentUserRole = $_SESSION['user_role'] ?? 'guest';
?>
<header class="header" style="margin-left: 150px; transition: margin-left 0.3s ease;">
    <nav class="navbar">
        <div class="logo" style="margin-left: auto;">
            <a href="<?php echo BASE_URL; ?>/index.php" style="color: white; text-decoration: none;">
                Система управления частным охранным предприятием
            </a>
        </div>
		<ul class="nav-menu" style="margin-left: auto;">
            <li><a href="<?php echo BASE_URL; ?>/logout.php">Выход (<?php echo htmlspecialchars($_SESSION['user_full_name']); ?>)</a></li>
            <li><a href="<?php echo BASE_URL; ?>/logout.php">🚪 Выход</a></li>
        </ul>
    </nav>
</header>