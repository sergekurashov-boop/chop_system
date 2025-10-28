<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

if (!isset($_GET['id'])) {
    die('ID смены не указан');
}

$shift_id = intval($_GET['id']);
$pdo = getDB();

// Получаем информацию о смене
$stmt = $pdo->prepare("SELECT * FROM shifts WHERE id = ?");
$stmt->execute([$shift_id]);
$shift = $stmt->fetch();

if (!$shift) {
    die('Смена не найдена');
}

// Получаем доступных сотрудников
$stmt = $pdo->query("SELECT * FROM employees WHERE is_active = 1 ORDER BY full_name");
$employees = $stmt->fetchAll();

// Получаем текущие назначения
$stmt = $pdo->prepare("
    SELECT sa.*, e.full_name, e.position 
    FROM shift_assignments sa 
    JOIN employees e ON sa.employee_id = e.id 
    WHERE sa.shift_id = ?
");
$stmt->execute([$shift_id]);
$assignments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Назначения на смену #<?php echo $shift['id']; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Назначения на смену #<?php echo $shift['id']; ?></h2>
                <a href="shifts.php" class="btn btn-secondary">← Назад к списку</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">Информация о смене</div>
                            <div class="card-body">
                                <p><strong>Место:</strong> <?php echo htmlspecialchars($shift['location']); ?></p>
                                <p><strong>Начало:</strong> <?php echo date('d.m.Y H:i', strtotime($shift['start_datetime'])); ?></p>
                                <p><strong>Требуется охранников:</strong> <?php echo $shift['required_guards_count']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Текущие назначения</h3>
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
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $assignment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($assignment['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($assignment['position']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $assignment['medical_status'] === 'passed' ? 'badge-success' : ($assignment['medical_status'] === 'pending' ? 'badge-warning' : 'badge-danger'); ?>">
                                                    <?php echo getMedicalStatuses()[$assignment['medical_status']]; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($assignment['route_familiarized']): ?>
                                                    <span class="badge badge-success">✅</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">❌</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($assignment['briefing_completed']): ?>
                                                    <span class="badge badge-success">✅</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">❌</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="#" class="btn btn-danger btn-sm">Удалить</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>На эту смену еще не назначены охранники.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Добавить назначение</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Сотрудник</label>
                                        <select name="employee_id" class="form-control" required>
                                            <option value="">Выберите сотрудника</option>
                                            <?php foreach ($employees as $employee): ?>
                                                <option value="<?php echo $employee['id']; ?>">
                                                    <?php echo htmlspecialchars($employee['full_name']); ?> - <?php echo htmlspecialchars($employee['position']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Добавить назначение</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>