<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior')) {
    die('Доступ запрещен');
}

if (!isset($_GET['id'])) {
    die('ID смены не указан');
}

$shift_id = intval($_GET['id']);
$pdo = getDB();

// Получаем информацию о смене
$stmt = $pdo->prepare("
    SELECT s.*, u.full_name as created_by_name
    FROM shifts s 
    LEFT JOIN users u ON s.created_by = u.id
    WHERE s.id = ?
");
$stmt->execute([$shift_id]);
$shift = $stmt->fetch();

if (!$shift) {
    die('Смена не найдена');
}

// Получаем назначения на смену
$stmt = $pdo->prepare("
    SELECT sa.*, e.full_name, e.position
    FROM shift_assignments sa
    JOIN employees e ON sa.employee_id = e.id
    WHERE sa.shift_id = ?
    ORDER BY e.full_name
");
$stmt->execute([$shift_id]);
$assignments = $stmt->fetchAll();
?>