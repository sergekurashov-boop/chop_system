<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    header("Location: ../../landing.php");
    exit;
}

$userRole = getUserRole();
$pdo = getDB();

// Получаем ID сотрудника
$employee_id = $_GET['id'] ?? 0;

if (!$employee_id) {
    header("Location: staff_list.php");
    exit;
}

// Вместо DELETE делаем архив:
$sql = "UPDATE employees SET is_active = 0 WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$employee_id]);

$_SESSION['success_message'] = "Сотрудник #{$employee_id} перемещен в архив";
// Возвращаемся к списку
header("Location: staff_list.php");
exit;
?>