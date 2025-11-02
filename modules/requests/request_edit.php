<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
if (!isLoggedIn()) { header("Location: ../../landing.php"); exit; }
$userRole = getUserRole();
?>
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><title>Редактирование заявки</title><link rel="stylesheet" href="../../assets/css/style.css"></head>
<body>
<?php include '../../includes/header.php'; include '../../includes/sidebar.php'; ?>
<div class="main-content"><div class="container"><div class="card"><div class="card-header"><h2>✏️ Редактирование заявки</h2><a href="requests.php" class="btn btn-secondary">← Назад</a></div><div class="card-body"><p>Форма редактирования заявки будет здесь.</p></div></div></div></div>
</body>
</html>