<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}
?>
<!-- Пока просто заглушка -->
<div class="card">
    <div class="card-header">Панель администратора</div>
    <div class="card-body">
        <p>Административная панель - в разработке</p>
    </div>
</div>