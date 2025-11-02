<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/staff_functions.php';

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

// Получаем данные сотрудника
try {
    $employee = getEmployeeById($employee_id);
    
    if (!$employee) {
        throw new Exception("Сотрудник не найден");
    }
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}

$error = '';
$success = '';

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = trim($_POST['full_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $medical_exam_expiry = $_POST['medical_exam_expiry'] ?? '';
        $is_active = $_POST['is_active'] ?? 1;
        $notes = trim($_POST['notes'] ?? '');

        if (empty($full_name) || empty($position)) {
            throw new Exception("Заполните обязательные поля");
        }

        // Обновляем сотрудника
        $sql = "UPDATE employees 
                SET full_name = ?, position = ?, phone = ?, email = ?, 
                    medical_exam_expiry = ?, is_active = ?, notes = ? 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, 
            $position, 
            $phone, 
            $email, 
            $medical_exam_expiry ?: null, 
            $is_active, 
            $notes,
            $employee_id
        ]);

        $success = "Сотрудник #{$employee_id} успешно обновлен!";
        
        // Обновляем данные
        $employee = getEmployeeById($employee_id);

    } catch (Exception $e) {
        $error = "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование сотрудника #<?php echo $employee_id; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>✏️ Редактирование сотрудника</h2>
                <div>
                    <a href="staff_view.php?id=<?php echo $employee_id; ?>" class="btn btn-info">👁️ Просмотр</a>
                    <a href="staff_list.php" class="btn btn-secondary">← Назад к списку</a>
                </div>
            </div>
            <div class="card-body">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <div style="margin-top: 10px;">
                            <a href="staff_view.php?id=<?php echo $employee_id; ?>" class="btn btn-primary">Перейти к просмотру</a>
                            <a href="staff_list.php" class="btn btn-secondary">Вернуться к списку</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">ФИО сотрудника</label>
                                <input type="text" name="full_name" class="form-control" required 
                                       value="<?php echo htmlspecialchars($employee['full_name']); ?>"
                                       placeholder="Иванов Иван Иванович">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Должность</label>
                                <select name="position" class="form-control" required>
                                    <option value="">-- Выберите должность --</option>
                                    <option value="Начальник охраны" <?php echo $employee['position'] == 'Начальник охраны' ? 'selected' : ''; ?>>Начальник охраны</option>
                                    <option value="Старший охранник" <?php echo $employee['position'] == 'Старший охранник' ? 'selected' : ''; ?>>Старший охранник</option>
                                    <option value="Охранник" <?php echo $employee['position'] == 'Охранник' ? 'selected' : ''; ?>>Охранник</option>
                                    <option value="Патрульный" <?php echo $employee['position'] == 'Патрульный' ? 'selected' : ''; ?>>Патрульный</option>
                                    <option value="Диспетчер" <?php echo $employee['position'] == 'Диспетчер' ? 'selected' : ''; ?>>Диспетчер</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Телефон</label>
                                <input type="tel" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>"
                                       placeholder="+7-999-123-45-67">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>"
                                       placeholder="ivanov@example.com">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Срок действия медосмотра</label>
                                <input type="date" name="medical_exam_expiry" class="form-control"
                                       value="<?php echo $employee['medical_exam_expiry'] ?? ''; ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Статус</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" <?php echo $employee['is_active'] ? 'selected' : ''; ?>>Активен</option>
                                    <option value="0" <?php echo !$employee['is_active'] ? 'selected' : ''; ?>>Неактивен</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Примечания</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Дополнительная информация о сотруднике..."><?php echo htmlspecialchars($employee['notes'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
                        <a href="staff_view.php?id=<?php echo $employee_id; ?>" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
</body>
</html>