<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/request_functions.php'; // ПОДКЛЮЧАЕМ ФУНКЦИИ

if (!isLoggedIn()) {
    header("Location: ../../landing.php");
    exit;
}

$userRole = getUserRole();
$pdo = getDB();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $client_id = $_POST['client_id'] ?? '';
        $object_type_id = $_POST['object_type_id'] ?? '';
        $object_name = trim($_POST['object_name'] ?? '');
        $start_date = $_POST['start_date'] ?? date('Y-m-d'); // ДОБАВИЛИ ДАТУ
        
        if (empty($client_id) || empty($object_type_id) || empty($object_name)) {
            throw new Exception("Заполните обязательные поля");
        }

        $sql = "INSERT INTO security_requests (client_id, object_type_id, object_name, start_date, created_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$client_id, $object_type_id, $object_name, $start_date, $_SESSION['user_id']]);

        $request_id = $pdo->lastInsertId();
        $success = "Заявка #{$request_id} создана!";

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заявки</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📝 Создание заявки</h2>
                <a href="requests.php" class="btn btn-secondary">← Назад</a>
            </div>
            <div class="card-body">
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <div style="margin-top: 10px;">
                            <a href="requests_list.php" class="btn btn-primary">К списку</a>
                            <a href="request_create.php" class="btn btn-secondary">Еще заявку</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Клиент</label>
                        <?php echo renderClientSelect(); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Тип объекта</label>
                        <?php echo renderObjectTypeSelect(); ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Название объекта</label>
                        <input type="text" name="object_name" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Создать</button>
                    <a href="requests_list.php" class="btn btn-secondary">Отмена</a>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
<script src="../../assets/js/script.js"></script>
<script src="../../assets/js/sidebar.js"></script>
</body>
</html>
