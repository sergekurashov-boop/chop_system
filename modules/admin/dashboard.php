<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

// Проверка прав доступа
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit();
}

// Получаем подключение к БД через синглтон
$pdo = getDB();

// Получаем статистику
$stats = [
    'total_users' => 0,
    'month_shifts' => 0, 
    'active_instructions' => 0,
    'users_by_role' => [],
    'active_today_shifts' => 0,
    'today_incidents' => 0,
    'expired_medical' => 0,
    'expired_licenses' => 0
];

try {
    // Получаем список всех таблиц
    $stmt = $pdo->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // 1. Статистика пользователей
    if (in_array('users', $allTables)) {
        // Количество АКТИВНЫХ пользователей по ролям (используем is_active = 1)
        $stmt = $pdo->query("
            SELECT role, COUNT(*) as count 
            FROM users 
            WHERE is_active = 1 
            GROUP BY role
        ");
        $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stats['total_users'] = array_sum(array_column($stats['users_by_role'], 'count'));
    }

    // 2. Статистика смен - АДАПТИРУЕМ ПОД ВАШУ СТРУКТУРУ
    if (in_array('shifts', $allTables)) {
        // Сначала посмотрим структуру таблицы shifts
        $stmt = $pdo->query("DESCRIBE shifts");
        $shiftColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Просто считаем все смены (без фильтра по дате)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM shifts");
        $stats['month_shifts'] = $stmt->fetchColumn();
        
        // Активные смены сегодня - используем существующие поля
        if (in_array('date', $shiftColumns)) {
            // Если есть поле date
            $stmt = $pdo->query("
                SELECT COUNT(*) as count FROM shifts 
                WHERE date = CURDATE() 
                AND status = 'active'
            ");
        } elseif (in_array('created_at', $shiftColumns)) {
            // Если есть created_at, используем его
            $stmt = $pdo->query("
                SELECT COUNT(*) as count FROM shifts 
                WHERE DATE(created_at) = CURDATE()
            ");
        } else {
            // Если нет подходящих полей, просто показываем общее количество
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM shifts");
        }
        $stats['active_today_shifts'] = $stmt->fetchColumn();
    }

    // 3. Статистика инструктажей
    if (in_array('instructions', $allTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM instructions");
        $stats['active_instructions'] = $stmt->fetchColumn();
    }

    // 4. Инциденты за сегодня - проверяем существование таблицы и полей
    if (in_array('incidents', $allTables)) {
        $stmt = $pdo->query("DESCRIBE incidents");
        $incidentColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('created_at', $incidentColumns)) {
            $stmt = $pdo->query("
                SELECT COUNT(*) as count FROM incidents 
                WHERE DATE(created_at) = CURDATE()
            ");
            $stats['today_incidents'] = $stmt->fetchColumn();
        } else {
            // Если нет поля created_at, показываем общее количество
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM incidents");
            $stats['today_incidents'] = $stmt->fetchColumn();
        }
    }

    // 5. Просроченные медосмотры (если есть таблица medical_records)
    if (in_array('medical_records', $allTables)) {
        $stmt = $pdo->query("DESCRIBE medical_records");
        $medicalColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('expiry_date', $medicalColumns)) {
            $stmt = $pdo->query("
                SELECT COUNT(*) as count FROM medical_records 
                WHERE expiry_date < CURDATE()
            ");
            $stats['expired_medical'] = $stmt->fetchColumn();
        }
    }

    // 6. Просроченные лицензии (если есть таблица user_licenses)
    if (in_array('user_licenses', $allTables)) {
        $stmt = $pdo->query("DESCRIBE user_licenses");
        $licenseColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('expiry_date', $licenseColumns)) {
            $stmt = $pdo->query("
                SELECT COUNT(*) as count FROM user_licenses 
                WHERE expiry_date < CURDATE()
            ");
            $stats['expired_licenses'] = $stmt->fetchColumn();
        }
    }

} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    addErrorMessage("Ошибка загрузки статистики: " . $e->getMessage());
}

$page_title = "Админ-панель - Дашборд";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <!-- Подключаем хедер -->
    <?php include '../../includes/header.php'; ?>
    
    <!-- Основная структура -->
    <div class="main-wrapper">
        <!-- Сайдбар -->
        <?php include '../../includes/sidebar.php'; ?>
        
        <!-- Основной контент -->
        <main class="main-content">
            <div class="dashboard">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Панель управления ЧОП</h1>
                    <div class="text-muted"><?= date('d.m.Y H:i') ?></div>
                </div>

                <?php displayMessages(); ?>

                <!-- Карточки статистики -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">👥</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['total_users'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Сотрудников</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">🔄</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['active_today_shifts'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Смен сегодня</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">⚠️</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['today_incidents'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Инцидентов</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">📋</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['active_instructions'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Инструктажей</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">🏥</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number <?= $stats['expired_medical'] > 0 ? 'text-danger' : '' ?>">
                                            <?= $stats['expired_medical'] ?>
                                        </h3>
                                        <p class="stat-label text-muted mb-0">Просрочен медосмотр</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-2 col-md-4 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">📄</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number <?= $stats['expired_licenses'] > 0 ? 'text-danger' : '' ?>">
                                            <?= $stats['expired_licenses'] ?>
                                        </h3>
                                        <p class="stat-label text-muted mb-0">Просрочены лицензии</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <!-- Распределение по ролям -->
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Распределение по ролям</span>
                                <a href="users.php" class="btn btn-sm btn-outline-primary">Управление</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($stats['users_by_role'])): ?>
                                    <div class="role-distribution">
                                        <?php foreach ($stats['users_by_role'] as $role): ?>
                                            <div class="role-item d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                                <span class="role-name"><?= getRoleDisplayName($role['role']) ?></span>
                                                <span class="badge bg-primary"><?= $role['count'] ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted text-center">
                                        <?= $stats['total_users'] > 0 ? "{$stats['total_users']} пользователей (без распределения по ролям)" : "Нет данных о пользователях" ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Требуют внимания -->
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Требуют внимания</span>
                                <?php $attention_count = ($stats['expired_medical'] ?? 0) + ($stats['expired_licenses'] ?? 0); ?>
                                <span class="badge bg-danger"><?= $attention_count ?></span>
                            </div>
                            <div class="card-body">
                                <?php if (($stats['expired_medical'] ?? 0) > 0): ?>
                                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                                    <span>Просроченные медосмотры</span>
                                    <span class="badge bg-danger"><?= $stats['expired_medical'] ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (($stats['expired_licenses'] ?? 0) > 0): ?>
                                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                                    <span>Просроченные лицензии</span>
                                    <span class="badge bg-danger"><?= $stats['expired_licenses'] ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (($stats['today_incidents'] ?? 0) > 0): ?>
                                <div class="alert alert-info d-flex justify-content-between align-items-center mb-3">
                                    <span>Инцидентов за сегодня</span>
                                    <span class="badge bg-info"><?= $stats['today_incidents'] ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($attention_count === 0 && ($stats['today_incidents'] ?? 0) === 0): ?>
                                    <div class="text-center text-muted py-3">
                                        <div class="mb-2">✅</div>
                                        <p>Все системы работают нормально</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Быстрые действия -->
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <span>Быстрые действия</span>
                            </div>
                            <div class="quick-actions">
    <a href="users.php" class="btn btn-outline-primary mb-2 w-100">👥 Управление пользователями</a>
    <a href="../senior/shifts.php" class="btn btn-outline-success mb-2 w-100">📅 Управление сменами</a>
    <a href="../medic/exams.php" class="btn btn-outline-warning mb-2 w-100">🏥 Медосмотры</a>
    <a href="backup.php" class="btn btn-outline-info mb-2 w-100">💾 Резервное копирование</a>
    <a href="settings.php" class="btn btn-outline-secondary mb-2 w-100">⚙️ Настройки системы</a>
</div>
                            </div>
                        </div>
                    </div>

                    <!-- Последние активности -->
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Последние действия</span>
                            </div>
                            <div class="card-body">
                                <div class="activity-list">
                                    <div class="activity-item text-muted mb-3">
                                        ℹ️ Модуль активности в разработке
                                    </div>
                                    <div class="activity-item mb-2">
                                        <div>➕ Новый пользователь добавлен</div>
                                        <small class="text-muted d-block">Сегодня в 14:30</small>
                                    </div>
                                    <div class="activity-item mb-2">
                                        <div>🛡️ Смена завершена на объекте "БЦ Северный"</div>
                                        <small class="text-muted d-block">Сегодня в 12:15</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Подключаем футер -->
    <?php include '../../includes/footer.php'; ?>

    <style>
    .stat-card { 
        transition: transform 0.2s; 
        border-left: 4px solid var(--accent-gray); 
    }
    .stat-card:hover { 
        transform: translateY(-2px); 
    }
    .stat-icon { 
        font-size: 2rem; 
        opacity: 0.8; 
    }
    .stat-number { 
        font-size: 1.8rem; 
        font-weight: bold; 
        margin-bottom: 0.2rem; 
        color: var(--dark-gray); 
    }
    .stat-label { 
        font-size: 0.9rem; 
    }
    .role-item { 
        transition: background-color 0.2s; 
    }
    .role-item:hover { 
        background-color: var(--light-gray); 
    }
    .quick-actions .btn { 
        text-align: left; 
        padding: 0.75rem 1rem; 
    }
    .activity-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--light-gray);
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    </style>
	<script src="../../assets/js/script.js"></script>
</body>
</html>

<?php
function getRoleDisplayName($role) {
    $roles = [
        'admin' => 'Администратор', 
        'senior' => 'Руководитель', 
        'medic' => 'Медик', 
        'guard' => 'Охранник', 
        'reports' => 'Отчеты'
    ];
    return $roles[$role] ?? $role;
}
?>