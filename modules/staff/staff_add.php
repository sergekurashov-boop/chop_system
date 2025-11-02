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

$error = '';
$success = '';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $full_name = trim($_POST['full_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $medical_exam_expiry = $_POST['medical_exam_expiry'] ?? '';
        $is_active = $_POST['is_active'] ?? 1;
        $notes = trim($_POST['notes'] ?? '');

        // Валидация
        if (empty($full_name) || empty($position)) {
            throw new Exception("Заполните обязательные поля");
        }

        // Создаем сотрудника
        $sql = "INSERT INTO employees (full_name, position, phone, email, medical_exam_expiry, is_active, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $full_name, 
            $position, 
            $phone, 
            $email, 
            $medical_exam_expiry ?: null, 
            $is_active, 
            $notes
        ]);

        $employee_id = $pdo->lastInsertId();
        $success = "Сотрудник #{$employee_id} успешно добавлен!";

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
    <title>Добавление сотрудника</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <style>
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-control { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        .required::after { content: " *"; color: red; }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>👥 Добавление сотрудника</h2>
                <a href="staff_list.php" class="btn btn-secondary">← Назад к списку</a>
            </div>
            <div class="card-body">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <div style="margin-top: 10px;">
                            <a href="staff_list.php" class="btn btn-primary">Вернуться к списку</a>
                            <a href="staff_add.php" class="btn btn-secondary">Добавить еще сотрудника</a>
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
                                       placeholder="Иванов Иван Иванович">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Должность</label>
                                <select name="position" class="form-control" required>
                                    <option value="">-- Выберите должность --</option>
                                    <option value="Начальник охраны">Начальник охраны</option>
                                    <option value="Старший охранник">Старший охранник</option>
                                    <option value="Охранник">Охранник</option>
                                    <option value="Патрульный">Патрульный</option>
                                    <option value="Диспетчер">Диспетчер</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Телефон</label>
                                <input type="tel" name="phone" class="form-control" 
                                       placeholder="+7-999-123-45-67">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="ivanov@example.com">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Срок действия медосмотра</label>
                                <input type="date" name="medical_exam_expiry" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Статус</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" selected>Активен</option>
                                    <option value="0">Неактивен</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Примечания</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Дополнительная информация о сотруднике..."></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">💾 Добавить сотрудника</button>
                        <a href="staff_list.php" class="btn btn-secondary">❌ Отмена</a>
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