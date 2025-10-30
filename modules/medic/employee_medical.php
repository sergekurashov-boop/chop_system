<?php
// modules/medic/employee_medical.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('medic') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

// Получаем ID сотрудника из URL
$employee_id = intval($_GET['id'] ?? 0);
if (!$employee_id) {
    die('Не указан ID сотрудника');
}

$pdo = getDB();

// Получаем данные сотрудника
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch();

if (!$employee) {
    die('Сотрудник не найден');
}

// Получаем историю медосмотров
$stmt = $pdo->prepare("
    SELECT * FROM medical_exams 
    WHERE employee_id = ? 
    ORDER BY exam_date DESC
");
$stmt->execute([$employee_id]);
$medical_exams = $stmt->fetchAll();

// Получаем лицензии
$stmt = $pdo->prepare("
    SELECT * FROM employee_licenses 
    WHERE employee_id = ? 
    ORDER BY expiry_date DESC
");
$stmt->execute([$employee_id]);
$licenses = $stmt->fetchAll();

$pageTitle = "Медицинская карта: " . $employee['full_name'];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>🏥 Медицинская карта сотрудника</h2>
                    <div class="quick-actions">
                        <a href="medical_cards.php" class="btn btn-primary">← Назад к списку</a>
                        <a href="add_medical_exam.php?employee_id=<?php echo $employee_id; ?>" class="btn btn-success">➕ Новый медосмотр</a>
                        <a href="license_edit.php?employee_id=<?php echo $employee_id; ?>" class="btn btn-warning">📜 Редактировать лицензию</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Информация о сотруднике -->
                    <div class="employee-info" style="padding: 20px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                        <h3><?php echo htmlspecialchars($employee['full_name']); ?></h3>
                        <p><strong>Должность:</strong> <?php echo htmlspecialchars($employee['position']); ?></p>
                        <p><strong>Дата приема:</strong> <?php echo $employee['hire_date'] ? date('d.m.Y', strtotime($employee['hire_date'])) : 'Не указана'; ?></p>
                        <p><strong>Статус:</strong> 
                            <span class="status-badge <?php echo $employee['is_active'] ? 'status-ok' : 'status-danger'; ?>">
                                <?php echo $employee['is_active'] ? 'Активен' : 'Неактивен'; ?>
                            </span>
                        </p>
                    </div>

                    <div class="row">
                        <!-- Медосмотры -->
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3>🩺 История медосмотров</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($medical_exams)): ?>
                                        <p>Медосмотры не найдены</p>
                                    <?php else: ?>
                                        <div class="exams-list">
                                            <?php foreach ($medical_exams as $exam): ?>
                                                <div class="exam-item" style="padding: 15px; border: 1px solid #e0e0e0; border-radius: 5px; margin-bottom: 10px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                                        <div>
                                                            <strong><?php echo date('d.m.Y', strtotime($exam['exam_date'])); ?></strong>
                                                            <br>
                                                            <small><?php 
                                                                $types = [
                                                                    'preliminary' => 'Предварительный',
                                                                    'periodic' => 'Периодический', 
                                                                    'extra' => 'Внеочередной'
                                                                ];
                                                                echo $types[$exam['exam_type']] ?? $exam['exam_type'];
                                                            ?></small>
                                                        </div>
                                                        <span class="status-badge <?php echo $exam['is_passed'] ? 'status-ok' : 'status-danger'; ?>">
                                                            <?php echo $exam['is_passed'] ? 'Пройден' : 'Не пройден'; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <?php if ($exam['clinic_name']): ?>
                                                        <p style="margin: 5px 0 0 0;"><small>Клиника: <?php echo htmlspecialchars($exam['clinic_name']); ?></small></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($exam['conclusion']): ?>
                                                        <p style="margin: 5px 0 0 0;"><small>Заключение: <?php echo htmlspecialchars($exam['conclusion']); ?></small></p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($exam['restrictions']): ?>
                                                        <p style="margin: 5px 0 0 0; color: #dc3545;">
                                                            <small>Ограничения: <?php echo htmlspecialchars($exam['restrictions']); ?></small>
                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <p style="margin: 5px 0 0 0;">
                                                        <small>Следующий осмотр: <strong><?php echo date('d.m.Y', strtotime($exam['next_exam_date'])); ?></strong></small>
                                                    </p>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Лицензии -->
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3>📜 Лицензии и допуски</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($licenses)): ?>
                                        <p>Лицензии не найдены</p>
                                    <?php else: ?>
                                        <div class="licenses-list">
                                            <?php foreach ($licenses as $license): ?>
                                                <div class="license-item" style="padding: 15px; border: 1px solid #e0e0e0; border-radius: 5px; margin-bottom: 10px;">
                                                    <div style="display: flex; justify-content: space-between; align-items: start;">
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($license['license_number']); ?></strong>
                                                            <br>
                                                            <small>Тип: <?php echo htmlspecialchars($license['license_type']); ?></small>
                                                        </div>
                                                        <span class="status-badge <?php echo $license['is_active'] ? 'status-ok' : 'status-danger'; ?>">
                                                            <?php echo $license['is_active'] ? 'Активна' : 'Неактивна'; ?>
                                                        </span>
                                                    </div>
                                                    
                                                    <p style="margin: 5px 0 0 0;">
                                                        <small>Выдана: <?php echo date('d.m.Y', strtotime($license['issue_date'])); ?></small>
                                                    </p>
                                                    
                                                    <p style="margin: 5px 0 0 0;">
                                                        <small>Истекает: 
                                                            <strong style="color: <?php 
                                                                $expiry_color = strtotime($license['expiry_date']) < time() ? '#dc3545' : 
                                                                                (strtotime($license['expiry_date']) < strtotime('+30 days') ? '#ffc107' : '#28a745');
                                                                echo $expiry_color;
                                                            ?>">
                                                                <?php echo date('d.m.Y', strtotime($license['expiry_date'])); ?>
                                                            </strong>
                                                        </small>
                                                    </p>
                                                    
                                                    <?php if ($license['issuing_authority']): ?>
                                                        <p style="margin: 5px 0 0 0;"><small>Орган выдачи: <?php echo htmlspecialchars($license['issuing_authority']); ?></small></p>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>