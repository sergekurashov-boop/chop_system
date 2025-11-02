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

$userRole = getUserRole();
$pdo = getDB();

// Получаем заявки
$requests = getSecurityRequests();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявки на охрану</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .requests-table th,
        .requests-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .requests-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .status-active { color: #28a745; font-weight: bold; }
        .status-completed { color: #007bff; }
        .status-cancelled { color: #dc3545; }
        .amount { font-weight: bold; color: #2c3e50; }
        .btn-sm { padding: 5px 10px; font-size: 0.875rem; }
    </style>
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2>📋 Заявки на охрану</h2>
                <a href="request_create.php" class="btn btn-primary">+ Новая заявка</a>
            </div>
            <div class="card-body">
                <?php if (count($requests) > 0): ?>
                    <div class="table-responsive">
                        <table class="requests-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Клиент</th>
                                    <th>Объект</th>
                                    <th>Тип объекта</th>
                                    <th>Дата начала</th>
                                    <th>Дата окончания</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request): ?>
                                <?php 
                                    $total_amount = calculateRequestTotal($request['id']);
                                    $paid_amount = getRequestPaymentsTotal($request['id']);
                                    $debt = $total_amount - $paid_amount;
                                ?>
                                <tr>
                                    <td>#<?php echo $request['id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['client_name'] ?? 'Не указан'); ?></td>
                                    <td><?php echo htmlspecialchars($request['object_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['object_type_name']); ?></td>
                                    <td><?php echo date('d.m.Y', strtotime($request['start_date'])); ?></td>
                                    <td><?php echo $request['end_date'] ? date('d.m.Y', strtotime($request['end_date'])) : 'Бессрочно'; ?></td>
                                    <td class="amount">
                                        <?php echo number_format($total_amount, 2, ',', ' '); ?> ₽
                                        <?php if ($debt > 0): ?>
                                            <br><small style="color: #dc3545;">Долг: <?php echo number_format($debt, 2, ',', ' '); ?> ₽</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-<?php echo $request['status']; ?>">
                                            <?php 
                                            $statuses = [
                                                'active' => 'Активна',
                                                'completed' => 'Завершена', 
                                                'cancelled' => 'Отменена'
                                            ];
                                            echo $statuses[$request['status']] ?? $request['status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="request_view.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-info">👁️</a>
                                        <a href="request_edit.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-warning">✏️</a>
                                        <a href="payments.php?request_id=<?php echo $request['id']; ?>" class="btn btn-sm btn-success">💰</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                        <strong>Статистика:</strong> 
                        Всего заявок: <?php echo count($requests); ?> | 
                        Активных: <?php echo count(array_filter($requests, fn($r) => $r['status'] === 'active')); ?> |
                        Общая сумма: <?php echo number_format(array_sum(array_map(fn($r) => calculateRequestTotal($r['id']), $requests)), 2, ',', ' '); ?> ₽
                    </div>
                    
                <?php else: ?>
                    <div class="alert alert-info">
                        <h4>Заявок пока нет</h4>
                        <p>У вас нет созданных заявок на охрану. Создайте первую заявку!</p>
                        <a href="request_create.php" class="btn btn-primary">Создать первую заявку</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>