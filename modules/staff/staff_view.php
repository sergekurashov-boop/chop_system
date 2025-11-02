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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр сотрудника #<?php echo $employee_id; ?></title>
    <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>/css/style.css">
</head>
<body>

<?php include '../../includes/header.php'; ?>
<?php include '../../includes/sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>👁️ Просмотр сотрудника</h2>
                <div>
                    <a href="staff_edit.php?id=<?php echo $employee_id; ?>" class="btn btn-warning">✏️ Редактировать</a>
                    <a href="staff_list.php" class="btn btn-secondary">← Назад к списку</a>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Основная информация -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>📋 Основная информация</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">ID:</th>
                                <td><strong>#<?php echo $employee['id']; ?></strong></td>
                            </tr>
                            <tr>
                                <th>ФИО:</th>
                                <td><strong><?php echo htmlspecialchars($employee['full_name']); ?></strong></td>
                            </tr>
                            <tr>
                                <th>Должность:</th>
                                <td><?php echo htmlspecialchars($employee['position']); ?></td>
                            </tr>
                            <tr>
                                <th>Статус:</th>
                                <td>
                                    <?php if ($employee['is_active']): ?>
                                        <span class="badge bg-success">Активен</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Неактивен</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h4>📞 Контакты</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Телефон:</th>
                                <td>
                                    <?php if (!empty($employee['phone'])): ?>
                                        <?php echo htmlspecialchars($employee['phone']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Не указан</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>
                                    <?php if (!empty($employee['email'])): ?>
                                        <?php echo htmlspecialchars($employee['email']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Не указан</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Медосмотр:</th>
                                <td>
                                    <?php if (!empty($employee['medical_exam_expiry'])): ?>
                                        <?php echo date('d.m.Y', strtotime($employee['medical_exam_expiry'])); ?>
                                        <?php if (strtotime($employee['medical_exam_expiry']) < time()): ?>
                                            <br><span class="badge bg-danger">Просрочен</span>
                                        <?php else: ?>
                                            <br><span class="badge bg-success">Действителен</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">Не указан</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Примечания -->
                <?php if (!empty($employee['notes'])): ?>
                <div class="row">
                    <div class="col-12">
                        <h4>📝 Примечания</h4>
                        <div class="card">
                            <div class="card-body">
                                <?php echo nl2br(htmlspecialchars($employee['notes'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Действия -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-dark">
                                <strong>⚡ Быстрые действия</strong>
                            </div>
                            <div class="card-body">
                                <a href="staff_edit.php?id=<?php echo $employee_id; ?>" class="btn btn-warning">✏️ Редактировать</a>
                                <a href="staff_delete.php?id=<?php echo $employee_id; ?>" class="btn btn-danger" 
                                   onclick="return confirm('Удалить сотрудника <?php echo htmlspecialchars($employee['full_name']); ?>?')">
                                   🗑️ Удалить
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
<script src="<?php echo ASSETS_URL; ?>/js/script.js"></script>
</body>
</html>