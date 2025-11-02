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

$employees = getAllEmployees();
$stats = getEmployeesCount();
$expired = checkExpiredDocuments();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сотрудники</title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style_pro.css">
<link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/sidebar_pro.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>👥 Сотрудники</h2>
                <a href="staff_add.php" class="btn btn-primary">➕ Добавить сотрудника</a>
            </div>
            <div class="card-body">
                
               <!-- Статистика -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h4><?php echo $stats['total']; ?></h4>
                <p>Всего сотрудников</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h4><?php echo $stats['active']; ?></h4>
                <p>Активных</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h4><?php echo $expired['expired_medical']; ?></h4>
                <p>Просрочены медосмотры</p>
            </div>
        </div>
    </div>
</div>
<!-- Список сотрудников -->
<?php if (count($employees) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ФИО</th>
                    <th>Должность</th>
                    <th>Телефон</th>
                    <th>Медосмотр</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><strong>#<?php echo $employee['id']; ?></strong></td>
                    <td>
                        <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($employee['position']); ?></td>
                    <td>
                        <?php if (!empty($employee['phone'])): ?>
                            <?php echo htmlspecialchars($employee['phone']); ?>
                        <?php else: ?>
                            <span class="text-muted">Не указан</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($employee['medical_exam_expiry'])): ?>
                            <?php echo date('d.m.Y', strtotime($employee['medical_exam_expiry'])); ?>
                            <?php if (strtotime($employee['medical_exam_expiry']) < time()): ?>
                                <br><small class="text-danger">Просрочен</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Не указан</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($employee['is_active']): ?>
                            <span class="badge bg-success">Активен</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Неактивен</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="staff_view.php?id=<?php echo $employee['id']; ?>" class="btn btn-outline-info" title="Просмотр">
                                👁️
                            </a>
                            <a href="staff_edit.php?id=<?php echo $employee['id']; ?>" class="btn btn-outline-warning" title="Редактировать">
                                ✏️
                            </a>
                            <a href="staff_delete.php?id=<?php echo $employee['id']; ?>" class="btn btn-outline-secondary" 
   title="Переместить в архив" 
   onclick="return confirm('Переместить <?php echo htmlspecialchars($employee['full_name']); ?> в архив?')">
   📦
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
        <p>Сотрудников пока нет</p>
        <a href="staff_add.php" class="btn btn-primary">Добавить первого сотрудника</a>
    </div>
<?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
</body>
</html>