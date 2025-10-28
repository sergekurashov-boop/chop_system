<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();

// Если ID смены не указан, показываем список смен для выбора
if (!isset($_GET['id'])) {
    // Получаем список смен
    $stmt = $pdo->query("
        SELECT s.*, COUNT(sa.id) as assigned_count 
        FROM shifts s 
        LEFT JOIN shift_assignments sa ON s.id = sa.shift_id 
        GROUP BY s.id 
        ORDER BY s.start_datetime DESC
    ");
    $shifts = $stmt->fetchAll();
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Выбор смены для назначений</title>
        <link rel="stylesheet" href="../../assets/css/style.css">
    </head>
    <body>
        <?php include '../../includes/header.php'; ?>
        
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Выбор смены для назначений</h2>
                    <a href="shifts.php" class="btn btn-secondary">← Назад к управлению сменами</a>
                </div>
                <div class="card-body">
                    <?php if (count($shifts) > 0): ?>
                        <div class="row">
                            <?php foreach ($shifts as $shift): ?>
                                <div class="col-4">
                                    <div class="card" style="margin-bottom: 1rem;">
                                        <div class="card-header">
                                            <strong>Смена #<?php echo $shift['id']; ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Место:</strong> <?php echo htmlspecialchars($shift['location']); ?></p>
                                            <p><strong>Начало:</strong> <?php echo date('d.m.Y H:i', strtotime($shift['start_datetime'])); ?></p>
                                            <p><strong>Тип:</strong> <?php echo $shift['shift_type'] === '12_hours' ? '12 часов' : '24 часа'; ?></p>
                                            <p><strong>Назначено:</strong> 
                                                <span class="badge <?php echo ($shift['assigned_count'] >= $shift['required_guards_count']) ? 'badge-success' : 'badge-warning'; ?>">
                                                    <?php echo $shift['assigned_count']; ?>/<?php echo $shift['required_guards_count']; ?>
                                                </span>
                                            </p>
                                            <a href="shift_assign.php?id=<?php echo $shift['id']; ?>" class="btn btn-primary btn-sm">
                                                Управлять назначениями
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Нет созданных смен.</p>
                        <a href="shifts.php" class="btn btn-primary">Создать первую смену</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}

// Если ID смены указан, показываем назначения для конкретной смены
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

// Обработка добавления назначения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_id'])) {
    $employee_id = intval($_POST['employee_id']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO shift_assignments (shift_id, employee_id, assigned_by) VALUES (?, ?, ?)");
        $stmt->execute([$shift_id, $employee_id, $_SESSION['user_id']]);
        $_SESSION['success_message'] = 'Сотрудник успешно назначен на смену';
        header("Location: shift_assign.php?id=$shift_id");
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка при назначении: " . $e->getMessage();
    }
}

// Обработка удаления назначения
if (isset($_GET['delete'])) {
    $assignment_id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM shift_assignments WHERE id = ?");
    $stmt->execute([$assignment_id]);
    $_SESSION['success_message'] = 'Назначение удалено';
    header("Location: shift_assign.php?id=$shift_id");
    exit;
}
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
                <a href="shift_assign.php" class="btn btn-secondary">← Выбрать другую смену</a>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">Информация о смене</div>
                            <div class="card-body">
                                <p><strong>Место:</strong> <?php echo htmlspecialchars($shift['location']); ?></p>
                                <p><strong>Начало:</strong> <?php echo date('d.m.Y H:i', strtotime($shift['start_datetime'])); ?></p>
                                <p><strong>Окончание:</strong> <?php echo date('d.m.Y H:i', strtotime($shift['end_datetime'])); ?></p>
                                <p><strong>Требуется охранников:</strong> <?php echo $shift['required_guards_count']; ?></p>
                                <p><strong>Тип смены:</strong> <?php echo $shift['shift_type'] === '12_hours' ? '12 часов' : '24 часа'; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Текущие назначения (<?php echo count($assignments); ?>/<?php echo $shift['required_guards_count']; ?>)</h3>
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
                                                    <?php 
                                                    if ($assignment['medical_status'] === 'passed') echo 'Пройден';
                                                    elseif ($assignment['medical_status'] === 'failed') echo 'Не пройден';
                                                    else echo 'Ожидает';
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
                                                <a href="shift_assign.php?id=<?php echo $shift_id; ?>&delete=<?php echo $assignment['id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Удалить назначение?')">Удалить</a>
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