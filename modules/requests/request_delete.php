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

$userRole = getUserRole();
$pdo = getDB();

// Получаем ID заявки
$request_id = $_GET['id'] ?? 0;

if (!$request_id) {
    header("Location: requests_list.php");
    exit;
}

// Получаем данные заявки для подтверждения
try {
    $sql = "SELECT object_name FROM security_requests WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception("Заявка не найдена");
    }
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}

// Обработка удаления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "DELETE FROM security_requests WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$request_id]);
        
        $_SESSION['success_message'] = "Заявка #{$request_id} успешно удалена";
        header("Location: requests_list.php");
        exit;
        
    } catch (Exception $e) {
        $error = "Ошибка при удалении: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление заявки #<?php echo $request_id; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>🗑️ Удаление заявки</h2>
                <a href="request_view.php?id=<?php echo $request_id; ?>" class="btn btn-secondary">← Назад</a>
            </div>
            <div class="card-body">
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="alert alert-warning">
                    <h4>⚠️ Внимание!</h4>
                    <p>Вы собираетесь удалить заявку <strong>#<?php echo $request_id; ?></strong>:</p>
                    <p><strong>"<?php echo htmlspecialchars($request['object_name']); ?>"</strong></p>
                    <p class="mb-0"><strong>Это действие необратимо!</strong> Все данные заявки будут безвозвратно удалены.</p>
                </div>

                <form method="POST" action="">
                    <div class="form-group">
                        <label class="form-label">Для подтверждения введите номер заявки:</label>
                        <input type="text" name="confirm_id" class="form-control" 
                               placeholder="Введите: <?php echo $request_id; ?>" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Точно удалить? Это действие нельзя отменить!')">
                            🗑️ Да, удалить заявку
                        </button>
                        <a href="request_view.php?id=<?php echo $request_id; ?>" class="btn btn-secondary">❌ Отмена</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>