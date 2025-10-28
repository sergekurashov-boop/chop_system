<?php
require_once 'includes/config.php';

// Автоматический вход для демо-режима
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['user_full_name'] = 'Демо Администратор';
$_SESSION['user_role'] = 'admin';
$_SESSION['demo_mode'] = true;

// Логируем демо-вход
error_log("Demo access: " . date('Y-m-d H:i:s'));

header("Location: index.php");
exit;
?>