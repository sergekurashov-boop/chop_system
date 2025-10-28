<?php
// Включим вывод ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен. Ваша роль: ' . ($_SESSION['user_role'] ?? 'не определена'));
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('ID смены не указан');
}

$shift_id = intval($_GET['id']);
$pdo = getDB();

// Получаем информацию о смене
try {
    $stmt = $pdo->prepare("SELECT * FROM shifts WHERE id = ?");
    $stmt->execute([$shift_id]);
    $shift = $stmt->fetch();
} catch (PDOException $e) {
    die('Ошибка базы данных: ' . $e->getMessage());
}

if (!$shift) {
    die('Смена не найдена');
}

// Получаем назначения на смену
try {
    $stmt = $pdo->prepare("
        SELECT sa.*, e.full_name, e.position 
        FROM shift_assignments sa 
        JOIN employees e ON sa.employee_id = e.id 
        WHERE sa.shift_id = ?
    ");
    $stmt->execute([$shift_id]);
    $assignments = $stmt->fetchAll();
} catch (PDOException $e) {
    die('Ошибка при получении назначений: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр смены #<?php echo $shift['id']; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Смена #<?php echo $shift['id']; ?></h2>
                <a href="shifts.php" class="btn btn-secondary">← Назад к списку</a>
            </div>
            <div class="card-body">
                <!-- Информация о смене -->
                <div class="row">
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">Основная информация</div>
                            <div class="card-body">
                                <p><strong>Тип смены:</strong> 
                                    <?php echo $shift['shift_type'] === '12_hours' ? '12 часов' : '24 часа'; ?>
                                </p>
                                <p><strong>Начало:</strong> 
                                    <?php echo date('d.m.Y H:i', strtotime($shift['start_datetime'])); ?>
                                </p>
                                <p><strong>Окончание:</strong> 
                                    <?php echo date('d.m.Y H:i', strtotime($shift['end_datetime'])); ?>
                                </p>
                                <p><strong>Место:</strong> 
                                    <?php echo htmlspecialchars($shift['location']); ?>
                                </p>
                                <p><strong>Требуется охранников:</strong> 
                                    <?php echo $shift['required_guards_count']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-8">
                        <div class="card">
                            <div class="card-header">Описание маршрута и обязанностей</div>
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($shift['route_description'] ?: 'Описание не указано')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Назначения на смену -->
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Назначенные охранники</h3>
                    </div>
                    <div class="card-body">
                        <?php if (count($assignments) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Сотрудник</th>
                                        <th>Должность</th>
                                        <th>Медосмотр</th>
                                        <th>Маршрут</th>
                                        <th>Инструктаж</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($assignment['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['position']); ?></td>
                                            <td>
                                                <?php 
                                                $med_status = $assignment['medical_status'];
                                                $badge_class = 'badge-secondary';
                                                if ($med_status === 'passed') $badge_class = 'badge-success';
                                                if ($med_status === 'failed') $badge_class = 'badge-danger';
                                                if ($med_status === 'pending') $badge_class = 'badge-warning';
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php 
                                                    $status_text = 'Неизвестно';
                                                    if ($med_status === 'passed') $status_text = 'Пройден';
                                                    if ($med_status === 'failed') $status_text = 'Не пройден';
                                                    if ($med_status === 'pending') $status_text = 'Ожидает';
                                                    echo $status_text;
                                                    ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($assignment['route_familiarized']): ?>
                                                    <span class="badge badge-success">✅ Ознакомлен</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">❌ Не ознакомлен</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($assignment['briefing_completed']): ?>
                                                    <span class="badge badge-success">✅ Пройден</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">❌ Не пройден</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $all_ready = $assignment['medical_status'] === 'passed' 
                                                    && $assignment['route_familiarized'] 
                                                    && $assignment['briefing_completed'];
                                                ?>
                                                <span class="badge <?php echo $all_ready ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo $all_ready ? 'Готов к работе' : 'Требует внимания'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>На эту смену еще не назначены охранники.</p>
                        <?php endif; ?>
                        
                        <div class="no-print" style="margin-top: 1rem;">
                            <a href="shift_assign.php?id=<?php echo $shift['id']; ?>" class="btn btn-primary">Управление назначениями</a>
                            <button onclick="window.print()" class="btn btn-secondary">Печать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>