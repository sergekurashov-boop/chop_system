<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/request_functions.php';

if (!isLoggedIn()) {
    header("Location: ../../landing.php");
    exit;
}

$userRole = getUserRole();
$pdo = getDB();

// Получаем ID заявки из URL
$request_id = $_GET['id'] ?? 0;

if (!$request_id) {
    header("Location: requests_list.php");
    exit;
}

// Получаем данные заявки для редактирования
try {
    $sql = "SELECT * FROM security_requests WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception("Заявка не найдена");
    }
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}

$error = '';
$success = '';

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $object_name = trim($_POST['object_name'] ?? '');
        $object_address = trim($_POST['object_address'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $notes = trim($_POST['notes'] ?? '');

        if (empty($object_name) || empty($start_date)) {
            throw new Exception("Заполните обязательные поля");
        }

        // Обновляем заявку
        $sql = "UPDATE security_requests 
                SET object_name = ?, object_address = ?, start_date = ?, end_date = ?, status = ?, notes = ?, updated_at = NOW() 
                WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $object_name, 
            $object_address, 
            $start_date, 
            $end_date ?: null, 
            $status, 
            $notes,
            $request_id
        ]);

        $success = "Заявка #{$request_id} успешно обновлена!";
        
        // Обновляем данные заявки после редактирования
        $sql = "SELECT * FROM security_requests WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <title>Редактирование заявки #<?php echo $request_id; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/sidebar.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>✏️ Редактирование заявки #<?php echo $request_id; ?></h2>
                <div>
                    <a href="request_view.php?id=<?php echo $request_id; ?>" class="btn btn-info">👁️ Просмотр</a>
                    <a href="requests_list.php" class="btn btn-secondary">← Назад к списку</a>
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
                            <a href="request_view.php?id=<?php echo $request_id; ?>" class="btn btn-primary">Перейти к просмотру</a>
                            <a href="requests_list.php" class="btn btn-secondary">Вернуться к списку</a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!$success): ?>
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Название объекта</label>
                                <input type="text" name="object_name" class="form-control" required 
                                       value="<?php echo htmlspecialchars($request['object_name']); ?>"
                                       placeholder="Например: Офисный центр 'Бизнес-Плаза'">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Адрес объекта</label>
                                <input type="text" name="object_address" class="form-control" 
                                       value="<?php echo htmlspecialchars($request['object_address'] ?? ''); ?>"
                                       placeholder="г. Москва, ул. Примерная, д. 123">
                            </div>

                            <div class="form-group">
                                <label class="form-label required">Статус заявки</label>
                                <select name="status" class="form-control" required>
                                    <option value="active" <?php echo $request['status'] == 'active' ? 'selected' : ''; ?>>Активна</option>
                                    <option value="completed" <?php echo $request['status'] == 'completed' ? 'selected' : ''; ?>>Завершена</option>
                                    <option value="cancelled" <?php echo $request['status'] == 'cancelled' ? 'selected' : ''; ?>>Отменена</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Дата начала охраны</label>
                                <input type="date" name="start_date" class="form-control" required 
                                       value="<?php echo $request['start_date']; ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Дата окончания охраны</label>
                                <input type="date" name="end_date" class="form-control"
                                       value="<?php echo $request['end_date'] ?? ''; ?>">
                                <small class="text-muted">Оставьте пустым для бессрочной охраны</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Примечания</label>
                        <textarea name="notes" class="form-control" rows="4" 
                                  placeholder="Дополнительная информация об объекте..."><?php echo htmlspecialchars($request['notes'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">💾 Сохранить изменения</button>
                        <a href="request_view.php?id=<?php echo $request_id; ?>" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>
                <?php endif; ?>

                <div class="mt-4 p-3 bg-light rounded">
                    <small class="text-muted">
                        <strong>Информация:</strong> Заявка создана <?php echo date('d.m.Y в H:i', strtotime($request['created_at'])); ?>
                        <?php if ($request['updated_at'] != $request['created_at']): ?>
                            <br>Последнее изменение: <?php echo date('d.m.Y в H:i', strtotime($request['updated_at'])); ?>
                        <?php endif; ?>
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/sidebar.js"></script>

</body>
</html>