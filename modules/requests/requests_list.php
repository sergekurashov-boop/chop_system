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

require_once __DIR__ . '/request_functions.php';

$userRole = getUserRole();
$pdo = getDB();
$requests = getSecurityRequests();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на охрану</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/sidebar.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📋 Заявки на охрану</h2>
                <a href="request_create.php" class="btn btn-primary">+ Новая заявка</a>
            </div>
            <div class="card-body">
                <?php if (count($requests) > 0): ?>
                    <p>Найдено заявок: <?php echo count($requests); ?></p>
                    <!-- В таблице заявок замени простую таблицу на эту: -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Объект охраны</th>
                <th>Клиент</th>
                <th>Тип объекта</th>
                <th>Дата начала</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
            <tr>
                <td><strong>#<?php echo $request['id']; ?></strong></td>
                <td>
                    <div class="fw-bold"><?php echo htmlspecialchars($request['object_name']); ?></div>
                    <?php if (!empty($request['object_address'])): ?>
                    <small class="text-muted"><?php echo htmlspecialchars($request['object_address']); ?></small>
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($request['client_name'] ?? 'Не указан'); ?></td>
                <td><?php echo htmlspecialchars($request['object_type_name']); ?></td>
                <td>
                    <?php echo date('d.m.Y', strtotime($request['start_date'])); ?>
                    <?php if ($request['end_date']): ?>
                    <br><small>до <?php echo date('d.m.Y', strtotime($request['end_date'])); ?></small>
                    <?php else: ?>
                    <br><small class="text-success">бессрочно</small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php
                    $status_badge = [
                        'active' => 'success',
                        'completed' => 'secondary', 
                        'cancelled' => 'danger'
                    ];
                    $status_text = [
                        'active' => 'Активна',
                        'completed' => 'Завершена',
                        'cancelled' => 'Отменена'
                    ];
                    ?>
                    <span class="badge bg-<?php echo $status_badge[$request['status']] ?? 'secondary'; ?>">
                        <?php echo $status_text[$request['status']] ?? $request['status']; ?>
                    </span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="request_view.php?id=<?php echo $request['id']; ?>" class="btn btn-outline-info" title="Просмотр">
                            👁️
                        </a>
                        <a href="request_edit.php?id=<?php echo $request['id']; ?>" class="btn btn-outline-warning" title="Редактировать">
                            ✏️
                        </a>
						  <a href="request_delete.php?id=<?php echo $request['id']; ?>" class="btn btn-outline-danger" 
           title="Удалить заявку" onclick="return confirm('Удалить заявку #<?php echo $request['id']; ?>?')">
            🗑️
        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <p>Заявок пока нет</p>
                        <a href="request_create.php" class="btn btn-primary">Создать первую заявку</a>
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