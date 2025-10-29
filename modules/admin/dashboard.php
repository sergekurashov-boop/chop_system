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
    'users_by_role' => []
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

    // 2. Статистика смен
    if (in_array('shifts', $allTables)) {
        // Просто считаем все смены (без фильтра по дате)
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM shifts");
        $stats['month_shifts'] = $stmt->fetchColumn();
    }

    // 3. Статистика инструктажей
    if (in_array('instructions', $allTables)) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM instructions");
        $stats['active_instructions'] = $stmt->fetchColumn();
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
                    <h1>Панель управления</h1>
                    <div class="text-muted"><?= date('d.m.Y H:i') ?></div>
                </div>

                <?php displayMessages(); ?>

                <!-- Карточки статистики -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">👥</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['total_users'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Пользователей</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">📊</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= $stats['month_shifts'] ?></h3>
                                        <p class="stat-label text-muted mb-0">Смен</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-3">
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
                    
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="stat-card card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="stat-icon me-3">📁</div>
                                    <div class="stat-info">
                                        <h3 class="stat-number"><?= count($allTables ?? []) ?></h3>
                                        <p class="stat-label text-muted mb-0">Таблиц в БД</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
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
                    
                    <div class="col-lg-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span>Быстрые действия</span>
                            </div>
                            <div class="card-body">
                                <div class="quick-actions">
                                    <a href="users.php" class="btn btn-outline-primary mb-2 w-100">👥 Управление пользователями</a>
                                    <a href="../senior/shifts.php" class="btn btn-outline-success mb-2 w-100">📅 Управление сменами</a>
                                    <a href="../medic/exams.php" class="btn btn-outline-warning mb-2 w-100">🏥 Медосмотры</a>
                                    <a href="backup.php" class="btn btn-outline-info mb-2 w-100">💾 Резервное копирование</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Отладочная информация -->
                <div class="card">
                    <div class="card-header">
                        <span>Информация о системе</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Таблицы в БД:</strong> <?= implode(', ', $allTables ?? ['не удалось получить']) ?></p>
                        <p><strong>Всего таблиц:</strong> <?= count($allTables ?? []) ?></p>
                        <p><strong>Версия PHP:</strong> <?= phpversion() ?></p>
                        <p><strong>Сервер:</strong> <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Неизвестно' ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Подключаем футер -->
    <?php include '../../includes/footer.php'; ?>

    <style>
    .stat-card { transition: transform 0.2s; border-left: 4px solid var(--accent-gray); }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { font-size: 2rem; opacity: 0.8; }
    .stat-number { font-size: 1.8rem; font-weight: bold; margin-bottom: 0.2rem; color: var(--dark-gray); }
    .stat-label { font-size: 0.9rem; }
    .role-item { transition: background-color 0.2s; }
    .role-item:hover { background-color: var(--light-gray); }
    .quick-actions .btn { text-align: left; padding: 0.75rem 1rem; }
    </style>
</body>
</html>

<?php
function getRoleDisplayName($role) {
    $roles = ['admin' => 'Администратор', 'senior' => 'Руководитель', 'medic' => 'Медик', 'guard' => 'Охранник', 'reports' => 'Отчеты'];
    return $roles[$role] ?? $role;
}