<?php
// Включим вывод всех ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключаем необходимые файлы
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Проверяем аутентификацию
checkAuth();

// Проверяем права доступа
if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен. Ваша роль: ' . ($_SESSION['user_role'] ?? 'не определена'));
}

// Подключаемся к базе данных
$pdo = getDB();

// Обработка создания новой смены
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_shift'])) {
    $shift_type = $_POST['shift_type'];
    $start_datetime = $_POST['start_datetime'];
    $location = $_POST['location'];
    $route_description = $_POST['route_description'];
    $required_guards_count = (int)$_POST['required_guards_count'];
    
    // Вычисляем время окончания смены
    $hours = ($shift_type === '24_hours') ? 24 : 12;
    $end_datetime = date('Y-m-d H:i:s', strtotime($start_datetime . " +{$hours} hours"));
    
    try {
        $stmt = $pdo->prepare("INSERT INTO shifts (shift_type, start_datetime, end_datetime, location, route_description, required_guards_count, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$shift_type, $start_datetime, $end_datetime, $location, $route_description, $required_guards_count, $_SESSION['user_id']]);
        
        $_SESSION['success_message'] = 'Смена успешно создана!';
        header('Location: shifts.php');
        exit;
    } catch (PDOException $e) {
        $error = "Ошибка при создании смены: " . $e->getMessage();
    }
}

// Получаем список смен
try {
    $stmt = $pdo->query("
        SELECT s.*, u.full_name as created_by_name, 
               COUNT(sa.id) as assigned_count
        FROM shifts s 
        LEFT JOIN users u ON s.created_by = u.id
        LEFT JOIN shift_assignments sa ON s.id = sa.shift_id
        GROUP BY s.id 
        ORDER BY s.start_datetime DESC
    ");
    $shifts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Ошибка при получении списка смен: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление сменами</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Добавляем кнопку мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <!-- ДОБАВЛЯЕМ ОБЕРТКУ MAIN-CONTENT -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Управление сменами</h2>
                </div>
                <div class="card-body">
                    <?php 
                    // Выводим сообщения об успехе/ошибках
                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                        unset($_SESSION['success_message']);
                    }
                    if (isset($error)) {
                        echo '<div class="alert alert-error">' . $error . '</div>';
                    }
                    ?>
                    
                    <!-- Форма создания смены -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3>Создать новую смену</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Тип смены *</label>
                                            <select name="shift_type" class="form-control" required>
                                                <option value="12_hours">12 часов</option>
                                                <option value="24_hours">24 часа</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Дата и время начала *</label>
                                            <input type="datetime-local" name="start_datetime" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Количество охранников *</label>
                                            <input type="number" name="required_guards_count" class="form-control" min="1" max="10" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label class="form-label">Место работы *</label>
                                            <input type="text" name="location" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Описание маршрута и обязанностей</label>
                                    <textarea name="route_description" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <button type="submit" name="create_shift" class="btn btn-primary">Создать смену</button>
                            </form>
                        </div>
                    </div>

                    <!-- Список смен -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Текущие смены</h3>
                        </div>
                        <div class="card-body">
                            <?php if (count($shifts) > 0): ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Тип</th>
                                            <th>Начало</th>
                                            <th>Окончание</th>
                                            <th>Место</th>
                                            <th>Охранники</th>
                                            <th>Создана</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($shifts as $shift): ?>
                                            <tr>
                                                <td><?php echo $shift['id']; ?></td>
                                                <td><?php echo ($shift['shift_type'] === '12_hours') ? '12 часов' : '24 часа'; ?></td>
                                                <td><?php echo date('d.m.Y H:i', strtotime($shift['start_datetime'])); ?></td>
                                                <td><?php echo date('d.m.Y H:i', strtotime($shift['end_datetime'])); ?></td>
                                                <td><?php echo htmlspecialchars($shift['location']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo ($shift['assigned_count'] >= $shift['required_guards_count']) ? 'badge-success' : 'badge-warning'; ?>">
                                                        <?php echo $shift['assigned_count']; ?>/<?php echo $shift['required_guards_count']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo htmlspecialchars($shift['created_by_name']); ?></td>
                                                <td>
                                                    <a href="shift_view.php?id=<?php echo $shift['id']; ?>" class="btn btn-primary">Просмотр</a>
                                                    <a href="shift_assign.php?id=<?php echo $shift['id']; ?>" class="btn btn-success">Назначения</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>Нет созданных смен.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- Закрываем main-content -->
	    <?php include '../../includes/footer.php'; ?>
    
  
    <!-- ПРАВИЛЬНЫЙ ПУТЬ к JavaScript -->
	<script src="assets/js/instructions.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>
</html>