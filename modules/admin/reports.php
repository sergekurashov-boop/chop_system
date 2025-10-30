<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();
$page_title = "Отчеты системы";

// Параметры отчетов
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');
$report_type = $_GET['report_type'] ?? 'shifts';

// Статистика для отчетов
$stats = [
    'total_shifts' => 0,
    'total_users' => 0,
    'active_objects' => 0,
    'total_incidents' => 0
];

try {
    // Общая статистика
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM shifts WHERE date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $stats['total_shifts'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $stats['total_users'] = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM objects WHERE status = 'active'");
    $stats['active_objects'] = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM incidents WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$date_from . ' 00:00:00', $date_to . ' 23:59:59']);
    $stats['total_incidents'] = $stmt->fetchColumn();

} catch (Exception $e) {
    error_log("Reports error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
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
                    <h2>📊 Отчеты системы</h2>
                </div>
                <div class="card-body">
                    <!-- Фильтры отчетов -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3>🔍 Параметры отчета</h3>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Тип отчета</label>
                                            <select name="report_type" class="form-control">
                                                <option value="shifts" <?= $report_type == 'shifts' ? 'selected' : '' ?>>Смены</option>
                                                <option value="users" <?= $report_type == 'users' ? 'selected' : '' ?>>Пользователи</option>
                                                <option value="objects" <?= $report_type == 'objects' ? 'selected' : '' ?>>Объекты</option>
                                                <option value="incidents" <?= $report_type == 'incidents' ? 'selected' : '' ?>>Инциденты</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Дата с</label>
                                            <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Дата по</label>
                                            <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary w-100">📈 Сформировать</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Статистика -->
                    <div class="row mb-4">
                        <div class="col-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <div class="stat-number"><?= $stats['total_shifts'] ?></div>
                                    <div class="stat-label">Смен</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <div class="stat-number"><?= $stats['total_users'] ?></div>
                                    <div class="stat-label">Пользователей</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <div class="stat-number"><?= $stats['active_objects'] ?></div>
                                    <div class="stat-label">Объектов</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="stat-card card text-center">
                                <div class="card-body">
                                    <div class="stat-number"><?= $stats['total_incidents'] ?></div>
                                    <div class="stat-label">Инцидентов</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Результаты отчета -->
                    <div class="card">
                        <div class="card-header">
                            <h3>📋 Результаты отчета</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($report_type == 'shifts'): ?>
                                <h4>Отчет по сменам за период <?= date('d.m.Y', strtotime($date_from)) ?> - <?= date('d.m.Y', strtotime($date_to)) ?></h4>
                                <p>Здесь будет детальная информация о сменах...</p>
                            <?php elseif ($report_type == 'users'): ?>
                                <h4>Отчет по пользователям</h4>
                                <p>Здесь будет статистика по пользователям...</p>
                            <?php elseif ($report_type == 'objects'): ?>
                                <h4>Отчет по объектам</h4>
                                <p>Здесь будет информация по объектам охраны...</p>
                            <?php elseif ($report_type == 'incidents'): ?>
                                <h4>Отчет по инцидентам</h4>
                                <p>Здесь будет статистика инцидентов...</p>
                            <?php endif; ?>
                            
                            <div class="alert alert-info">
                                ℹ️ Модуль отчетов находится в разработке. В будущем здесь будет детальная аналитика.
                            </div>
                        </div>
                    </div>

                    <!-- Экспорт -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3>📤 Экспорт данных</h3>
                        </div>
                        <div class="card-body">
                            <div class="btn-group">
                                <button class="btn btn-success">📄 Excel</button>
                                <button class="btn btn-primary">📊 PDF</button>
                                <button class="btn btn-secondary">📋 CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <style>
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    .stat-label {
        font-size: 0.9rem;
        color: var(--text-muted);
    }
    </style>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>