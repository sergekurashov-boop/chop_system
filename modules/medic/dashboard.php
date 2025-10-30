<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('medic') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();

// Статистика для дашборда
$stmt = $pdo->query("SELECT COUNT(*) as total FROM employees WHERE is_active = 1");
$total_employees = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as expired FROM employees WHERE medical_exam_expiry < CURDATE()");
$expired_medical = $stmt->fetch()['expired'];

$stmt = $pdo->query("SELECT COUNT(*) as upcoming FROM employees WHERE medical_exam_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)");
$upcoming_expiry = $stmt->fetch()['upcoming'];

// Сотрудники с просроченными медосмотрами
$stmt = $pdo->query("
    SELECT e.id, e.full_name, e.position, e.medical_exam_expiry 
    FROM employees e 
    WHERE e.medical_exam_expiry < CURDATE() 
    AND e.is_active = 1 
    ORDER BY e.medical_exam_expiry ASC 
    LIMIT 10
");
$expired_employees = $stmt->fetchAll();

// Ближайшие истечения
$stmt = $pdo->query("
    SELECT e.id, e.full_name, e.position, e.medical_exam_expiry,
           DATEDIFF(e.medical_exam_expiry, CURDATE()) as days_left
    FROM employees e 
    WHERE e.medical_exam_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND e.is_active = 1 
    ORDER BY e.medical_exam_expiry ASC 
    LIMIT 10
");
$upcoming_employees = $stmt->fetchAll();

// Статистика по месяцам
$stmt = $pdo->query("
    SELECT 
        MONTH(medical_exam_expiry) as month,
        COUNT(*) as count
    FROM employees 
    WHERE medical_exam_expiry >= CURDATE() 
    AND medical_exam_expiry <= DATE_ADD(CURDATE(), INTERVAL 6 MONTH)
    AND is_active = 1
    GROUP BY MONTH(medical_exam_expiry)
    ORDER BY month ASC
");
$monthly_stats = $stmt->fetchAll();

$month_names = [
    1 => 'Янв', 2 => 'Фев', 3 => 'Мар', 4 => 'Апр', 
    5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Авг',
    9 => 'Сен', 10 => 'Окт', 11 => 'Ноя', 12 => 'Дек'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Медицинский кабинет - Главная</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        .stat-card.total { border-left-color: #007bff; }
        .stat-card.expired { border-left-color: #dc3545; }
        .stat-card.upcoming { border-left-color: #ffc107; }
        .stat-card.valid { border-left-color: #28a745; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #007bff;
        }
        .action-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .alert-list {
            max-height: 300px;
            overflow-y: auto;
        }
        .alert-item {
            padding: 12px;
            border-left: 4px solid;
            margin-bottom: 10px;
            background: #f8f9fa;
        }
        .alert-item.expired { border-left-color: #dc3545; }
        .alert-item.warning { border-left-color: #ffc107; }
        
        .month-stats {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .month-bar {
            flex: 1;
            text-align: center;
        }
        .bar {
            height: 60px;
            background: #e9ecef;
            border-radius: 5px;
            position: relative;
            overflow: hidden;
        }
        .bar-fill {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: #007bff;
            border-radius: 5px;
        }
        .month-label {
            font-size: 0.8rem;
            margin-top: 5px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>🏥 Медицинский кабинет - Обзор</h2>
                </div>
                <div class="card-body">
                    <!-- Статистика -->
                    <div class="stats-grid">
                        <div class="stat-card total">
                            <div class="stat-number"><?php echo $total_employees; ?></div>
                            <div class="stat-label">Всего сотрудников</div>
                        </div>
                        <div class="stat-card expired">
                            <div class="stat-number" style="color: #dc3545;"><?php echo $expired_medical; ?></div>
                            <div class="stat-label">Просроченные медосмотры</div>
                        </div>
                        <div class="stat-card upcoming">
                            <div class="stat-number" style="color: #ffc107;"><?php echo $upcoming_expiry; ?></div>
                            <div class="stat-label">Истекают в течение 30 дней</div>
                        </div>
                        <div class="stat-card valid">
                            <div class="stat-number" style="color: #28a745;"><?php echo $total_employees - $expired_medical; ?></div>
                            <div class="stat-label">Действующие медосмотры</div>
                        </div>
                    </div>

                    <!-- Быстрые действия -->
                    <div class="quick-actions">
                        <a href="exams.php" class="action-btn">
                            <span class="action-icon">📋</span>
                            <span>Медосмотры</span>
                        </a>
                        <a href="employees.php" class="action-btn">
                            <span class="action-icon">👥</span>
                            <span>Сотрудники</span>
                        </a>
                        <a href="reports.php" class="action-btn">
                            <span class="action-icon">📊</span>
                            <span>Отчеты</span>
                        </a>
                        <a href="schedule.php" class="action-btn">
                            <span class="action-icon">📅</span>
                            <span>График осмотров</span>
                        </a>
                        <a href="add_exam.php" class="action-btn">
                            <span class="action-icon">➕</span>
                            <span>Новый осмотр</span>
                        </a>
                    </div>

                    <div class="row">
                        <!-- Просроченные медосмотры -->
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 style="color: #dc3545;">⚠️ Просроченные медосмотры</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($expired_employees)): ?>
                                        <p style="color: #28a745;">✅ Нет просроченных медосмотров</p>
                                    <?php else: ?>
                                        <div class="alert-list">
                                            <?php foreach ($expired_employees as $employee): ?>
                                                <div class="alert-item expired">
                                                    <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong>
                                                    <br>
                                                    <small><?php echo htmlspecialchars($employee['position']); ?></small>
                                                    <br>
                                                    <span style="color: #dc3545;">
                                                        Просрочен: <?php echo date('d.m.Y', strtotime($employee['medical_exam_expiry'])); ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <a href="employees.php?filter=expired" class="btn btn-outline-danger btn-sm">
                                            Показать всех (<?php echo $expired_medical; ?>)
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Ближайшие истечения -->
                        <div class="col-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 style="color: #ffc107;">📅 Ближайшие истечения</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($upcoming_employees)): ?>
                                        <p style="color: #28a745;">✅ Нет ближайших истечений</p>
                                    <?php else: ?>
                                        <div class="alert-list">
                                            <?php foreach ($upcoming_employees as $employee): ?>
                                                <div class="alert-item warning">
                                                    <strong><?php echo htmlspecialchars($employee['full_name']); ?></strong>
                                                    <br>
                                                    <small><?php echo htmlspecialchars($employee['position']); ?></small>
                                                    <br>
                                                    <span style="color: #ffc107;">
                                                        Истекает: <?php echo date('d.m.Y', strtotime($employee['medical_exam_expiry'])); ?>
                                                        (через <?php echo $employee['days_left']; ?> дн.)
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <a href="employees.php?filter=upcoming" class="btn btn-outline-warning btn-sm">
                                            Показать всех (<?php echo $upcoming_expiry; ?>)
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Статистика по месяцам -->
                    <div class="card" style="margin-top: 2rem;">
                        <div class="card-header">
                            <h3>📈 Истечения по месяцам (6 месяцев)</h3>
                        </div>
                        <div class="card-body">
                            <div class="month-stats">
                                <?php
                                $max_count = 0;
                                foreach ($monthly_stats as $stat) {
                                    if ($stat['count'] > $max_count) {
                                        $max_count = $stat['count'];
                                    }
                                }
                                
                                for ($i = 1; $i <= 6; $i++):
                                    $current_month = date('n');
                                    $month = ($current_month + $i - 2) % 12 + 1;
                                    $year = date('Y') + floor(($current_month + $i - 2) / 12);
                                    
                                    $count = 0;
                                    foreach ($monthly_stats as $stat) {
                                        if ($stat['month'] == $month) {
                                            $count = $stat['count'];
                                            break;
                                        }
                                    }
                                    
                                    $height = $max_count > 0 ? ($count / $max_count * 100) : 0;
                                ?>
                                <div class="month-bar">
                                    <div class="bar">
                                        <div class="bar-fill" style="height: <?php echo $height; ?>%"></div>
                                    </div>
                                    <div class="month-label">
                                        <?php echo $month_names[$month] . ' ' . $year; ?><br>
                                        <small><?php echo $count; ?></small>
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="<?php echo MAIN_JS; ?>"></script>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>