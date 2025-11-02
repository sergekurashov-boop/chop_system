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

require_once __DIR__ . '/includes/request_functions.php';
$forms_file = __DIR__ . '/includes/request_forms.php';
if (file_exists($forms_file)) {
    require_once $forms_file;
} else {
    die("❌ Файл форм не найден: " . $forms_file);
}

$userRole = getUserRole();
$pdo = getDB();

$error = '';
$success = '';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $client_id = $_POST['client_id'] ?? '';
        $object_type_id = $_POST['object_type_id'] ?? '';
        $object_name = trim($_POST['object_name'] ?? '');
        $object_address = trim($_POST['object_address'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $notes = trim($_POST['notes'] ?? '');

        // Валидация
        if (empty($client_id) || empty($object_type_id) || empty($object_name) || empty($start_date)) {
            throw new Exception("Все обязательные поля должны быть заполнены");
        }

        // Создаем заявку
        $sql = "INSERT INTO security_requests (client_id, object_type_id, object_name, object_address, start_date, end_date, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $client_id, 
            $object_type_id, 
            $object_name, 
            $object_address, 
            $start_date, 
            $end_date ?: null, 
            $notes,
            $_SESSION['user_id']
        ]);

        $request_id = $pdo->lastInsertId();
        $success = "Заявка #{$request_id} успешно создана!";

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
    <title>Создание заявки на охрану</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .required::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📝 Создание новой заявки</h2>
                <a href="requests.php" class="btn btn-secondary">← Назад к списку</a>
            </div>
            <div class="card-body">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <div style="margin-top: 10px;">
                            <a href="requests.php" class="btn btn-primary">Вернуться к списку</a>
                            <a href="request_create.php" class="btn btn-secondary">Создать еще заявку</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label required">Клиент</label>
                        <?php echo renderClientSelect(); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Тип объекта</label>
                        <?php echo renderObjectTypeSelect(); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Название объекта</label>
                        <input type="text" name="object_name" class="form-control" required 
                               placeholder="Например: Офисный центр 'Бизнес-Плаза'">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Адрес объекта</label>
                        <input type="text" name="object_address" class="form-control" 
                               placeholder="г. Москва, ул. Примерная, д. 123">
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Дата начала охраны</label>
                        <input type="date" name="start_date" class="form-control" required 
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Дата окончания охраны</label>
                        <input type="date" name="end_date" class="form-control">
                        <small class="text-muted">Оставьте пустым для бессрочной охраны</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Примечания</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Дополнительная информация об объекте..."></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Создать заявку</button>
                        <a href="requests.php" class="btn btn-secondary">Отмена</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>