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

// Получаем данные заявки
try {
    $sql = "SELECT sr.*, c.name as client_name, c.phone as client_phone, 
                   c.email as client_email, ot.name as object_type_name
            FROM security_requests sr 
            LEFT JOIN clients c ON sr.client_id = c.id 
            LEFT JOIN object_types ot ON sr.object_type_id = ot.id 
            WHERE sr.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        throw new Exception("Заявка не найдена");
    }
    
} catch (Exception $e) {
    die("Ошибка: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр заявки #<?php echo $request_id; ?></title>
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
                <h2>👁️ Просмотр заявки #<?php echo $request_id; ?></h2>
                <div>
                    <a href="request_edit.php?id=<?php echo $request_id; ?>" class="btn btn-warning">✏️ Редактировать</a>
					  <a href="request_delete.php?id=<?php echo $request['id']; ?>" class="btn btn-outline-danger" 
           title="Удалить заявку" onclick="return confirm('Удалить заявку #<?php echo $request['id']; ?>?')">
            🗑️
        </a>
                    <a href="requests_list.php" class="btn btn-secondary">← Назад к списку</a>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Основная информация -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>📋 Основная информация</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Номер заявки:</th>
                                <td><strong>#<?php echo $request['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>Объект охраны:</th>
                                <td><strong><?php echo htmlspecialchars($request['object_name']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Тип объекта:</th>
                                <td><?php echo htmlspecialchars($request['object_type_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Адрес объекта:</th>
                                <td><?php echo !empty($request['object_address']) ? htmlspecialchars($request['object_address']) : '<span class="text-muted">Не указан</span>'; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h4>📅 Даты и статус</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Дата начала:</th>
                                <td><?php echo date('d.m.Y', strtotime($request['start_date'])); ?></td>
                            </tr>
                            <tr>
                                <th>Дата окончания:</th>
                                <td>
                                    <?php if ($request['end_date']): ?>
                                        <?php echo date('d.m.Y', strtotime($request['end_date'])); ?>
                                    <?php else: ?>
                                        <span class="text-success">Бессрочно</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Статус:</th>
                                <td>
                                    <?php
                                    $status_badge = [
                                        'active' => ['success', 'Активна'],
                                        'completed' => ['secondary', 'Завершена'], 
                                        'cancelled' => ['danger', 'Отменена']
                                    ];
                                    $status = $status_badge[$request['status']] ?? ['secondary', 'Неизвестен'];
                                    ?>
                                    <span class="badge bg-<?php echo $status[0]; ?>"><?php echo $status[1]; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Дата создания:</th>
                                <td><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Информация о клиенте -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h4>👤 Информация о клиенте</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Клиент:</strong><br>
                                        <?php echo htmlspecialchars($request['client_name'] ?? 'Не указан'); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Телефон:</strong><br>
                                        <?php echo !empty($request['client_phone']) ? htmlspecialchars($request['client_phone']) : '<span class="text-muted">Не указан</span>'; ?>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Email:</strong><br>
                                        <?php echo !empty($request['client_email']) ? htmlspecialchars($request['client_email']) : '<span class="text-muted">Не указан</span>'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Примечания -->
                <?php if (!empty($request['notes'])): ?>
                <div class="row">
                    <div class="col-12">
                        <h4>📝 Примечания</h4>
                        <div class="card">
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($request['notes'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
<script src="<?php echo ASSETS_URL; ?>/js/sidebar.js"></script>

</body>
</html>