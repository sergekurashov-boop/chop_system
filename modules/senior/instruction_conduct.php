<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';


checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

if (!isset($_GET['assignment_id']) || !isset($_GET['shift_id'])) {
    die('Не указаны необходимые параметры');
}

$assignment_id = intval($_GET['assignment_id']);
$shift_id = intval($_GET['shift_id']);
$pdo = getDB();

// Получаем информацию о назначении
$stmt = $pdo->prepare("
    SELECT sa.*, e.full_name, e.position, s.location 
    FROM shift_assignments sa 
    JOIN employees e ON sa.employee_id = e.id 
    JOIN shifts s ON sa.shift_id = s.id 
    WHERE sa.id = ?
");
$stmt->execute([$assignment_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    die('Назначение не найдено');
}

// Получаем активные инструкции для проведения
$stmt = $pdo->query("
    SELECT * FROM instructions 
    WHERE is_active = 1 
    ORDER BY category, title
");
$instructions = $stmt->fetchAll();

// Группируем инструкции по категориям
$instructions_by_category = [];
$categories = [
    'primary' => 'Первичный инструктаж',
    'safety' => 'Техника безопасности',
    'route' => 'Знание маршрута', 
    'equipment' => 'Работа с оборудованием'
];

foreach ($instructions as $instruction) {
    $instructions_by_category[$instruction['category']][] = $instruction;
}

// Обработка отметки о прохождении инструктажа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_completion'])) {
    $instruction_id = intval($_POST['instruction_id']);
    
    try {
        // Проверяем, не пройден ли уже этот инструктаж сегодня
        $stmt_check = $pdo->prepare("
            SELECT id FROM instruction_completions 
            WHERE employee_id = ? AND instruction_id = ? AND DATE(completed_at) = CURDATE()
        ");
        $stmt_check->execute([$assignment['employee_id'], $instruction_id]);
        
        if (!$stmt_check->fetch()) {
            // Добавляем запись о прохождении
            $stmt = $pdo->prepare("
                INSERT INTO instruction_completions (instruction_id, employee_id, completed_by, shift_id) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$instruction_id, $assignment['employee_id'], $_SESSION['user_id'], $shift_id]);
        }
        
        $_SESSION['success_message'] = 'Инструктаж отмечен как пройденный';
        
    } catch (PDOException $e) {
        $error = "Ошибка: " . $e->getMessage();
    }
}

// Получаем пройденные инструктажи за сегодня
$stmt_completions = $pdo->prepare("
    SELECT ic.instruction_id, i.title 
    FROM instruction_completions ic 
    JOIN instructions i ON ic.instruction_id = i.id 
    WHERE ic.employee_id = ? AND DATE(ic.completed_at) = CURDATE()
");
$stmt_completions->execute([$assignment['employee_id']]);
$completed_instructions = $stmt_completions->fetchAll(PDO::FETCH_KEY_PAIR);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Проведение инструктажа</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; 
	include '../../includes/header.php';
include '../../includes/sidebar.php';
	?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📖 Проведение инструктажа</h2>
                <a href="shift_journal.php" class="btn btn-secondary">← Назад к журналу</a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Информация о сотруднике -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h3>Сотрудник</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>ФИО:</strong> <?php echo htmlspecialchars($assignment['full_name']); ?></p>
                        <p><strong>Должность:</strong> <?php echo htmlspecialchars($assignment['position']); ?></p>
                        <p><strong>Смена:</strong> #<?php echo $shift_id; ?> (<?php echo htmlspecialchars($assignment['location']); ?>)</p>
                        <p><strong>Пройдено инструктажей сегодня:</strong> 
                            <span class="badge badge-info"><?php echo count($completed_instructions); ?></span>
                        </p>
                    </div>
                </div>

                <!-- Список инструктажей -->
                <?php foreach ($categories as $category_key => $category_name): ?>
                    <?php if (!empty($instructions_by_category[$category_key])): ?>
                        <div class="card" style="margin-bottom: 1.5rem;">
                            <div class="card-header">
                                <h4>📋 <?php echo $category_name; ?></h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($instructions_by_category[$category_key] as $instruction): ?>
                                        <div class="col-6">
                                            <div class="card" style="margin-bottom: 1rem;">
                                                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                                    <strong><?php echo htmlspecialchars($instruction['title']); ?></strong>
                                                    <?php if (isset($completed_instructions[$instruction['id']])): ?>
                                                        <span class="badge badge-success">✅ Пройден</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body">
                                                    <div style="max-height: 100px; overflow: hidden; margin-bottom: 1rem;">
                                                        <?php echo strip_tags(substr($instruction['content'], 0, 150)); ?>...
                                                    </div>
                                                    <div style="display: flex; gap: 0.5rem;">
                                                        <a href="instruction_view.php?id=<?php echo $instruction['id']; ?>" 
                                                           class="btn btn-primary btn-sm" target="_blank">
                                                            👁️ Просмотреть
                                                        </a>
                                                        <?php if (!isset($completed_instructions[$instruction['id']])): ?>
                                                            <form method="POST" style="margin: 0;">
                                                                <input type="hidden" name="instruction_id" value="<?php echo $instruction['id']; ?>">
                                                                <button type="submit" name="mark_completion" class="btn btn-success btn-sm">
                                                                    ✅ Отметить пройденным
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Завершение инструктажа -->
                <div class="card">
                    <div class="card-header">
                        <h4>✅ Завершение инструктажа</h4>
                    </div>
                    <div class="card-body">
                        <p>После проведения всех необходимых инструктажей отметьте общее завершение:</p>
                        <form method="POST" action="shift_journal.php">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment_id; ?>">
                            <button type="submit" name="mark_briefing" class="btn btn-success btn-lg">
                                ✅ Завершить инструктаж сотрудника
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="../../assets/js/script.js"></script>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>