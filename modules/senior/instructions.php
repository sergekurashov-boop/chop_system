<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();

// Обработка создания/редактирования инструкции
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_instruction'])) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $category = $_POST['category'];
        $instruction_id = $_POST['instruction_id'] ?? null;
        
        if ($instruction_id) {
            // Редактирование
            $stmt = $pdo->prepare("UPDATE instructions SET title = ?, content = ?, category = ? WHERE id = ?");
            $stmt->execute([$title, $content, $category, $instruction_id]);
            $_SESSION['success_message'] = 'Инструкция обновлена';
        } else {
            // Создание
            $stmt = $pdo->prepare("INSERT INTO instructions (title, content, category, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $content, $category, $_SESSION['user_id']]);
            $_SESSION['success_message'] = 'Инструкция создана';
        }
        header("Location: instructions.php");
        exit;
    }
    
    if (isset($_POST['toggle_instruction'])) {
        $instruction_id = intval($_POST['instruction_id']);
        $stmt = $pdo->prepare("UPDATE instructions SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$instruction_id]);
        $_SESSION['success_message'] = 'Статус инструкции изменен';
        header("Location: instructions.php");
        exit;
    }
}

// Получаем список инструкций
$stmt = $pdo->query("
    SELECT i.*, u.full_name as author 
    FROM instructions i 
    LEFT JOIN users u ON i.created_by = u.id 
    ORDER BY i.category, i.title
");
$instructions = $stmt->fetchAll();

// Группируем по категориям
$instructions_by_category = [
    'primary' => [],
    'safety' => [],
    'route' => [],
    'equipment' => []
];

foreach ($instructions as $instruction) {
    $instructions_by_category[$instruction['category']][] = $instruction;
}

// Категории инструкций
$categories = [
    'primary' => 'Первичный инструктаж',
    'safety' => 'Техника безопасности',
    'route' => 'Знание маршрута',
    'equipment' => 'Работа с оборудованием'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление инструктажами</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Froala Editor -->
    <link rel="stylesheet" href="../../froala/css/froala_editor.pkgd.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📚 Управление инструктажами</h2>
            </div>
            <div class="card-body">
                <?php displayMessages(); ?>

                <!-- Форма создания инструкции -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h3>➕ Создать новую инструкцию</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="instructionForm">
                            <input type="hidden" name="instruction_id" id="instruction_id">
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Название инструкции *</label>
                                        <input type="text" name="title" id="instruction_title" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Категория *</label>
                                        <select name="category" class="form-control" required>
                                            <option value="">Выберите категорию</option>
                                            <?php foreach ($categories as $key => $name): ?>
                                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Содержание инструкции *</label>
                                <textarea id="instruction_content" name="content" class="form-control" rows="10" required></textarea>
                            </div>
                            
                            <button type="submit" name="save_instruction" class="btn btn-primary">Сохранить инструкцию</button>
                            <button type="button" id="cancelEdit" class="btn btn-secondary" style="display: none;">Отмена</button>
                        </form>
                    </div>
                </div>

                <!-- Список инструкций -->
                <?php foreach ($categories as $category_key => $category_name): ?>
                    <?php if (!empty($instructions_by_category[$category_key])): ?>
                        <div class="card" style="margin-bottom: 2rem;">
                            <div class="card-header">
                                <h3>📋 <?php echo $category_name; ?></h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($instructions_by_category[$category_key] as $instruction): ?>
                                        <div class="col-6">
                                            <div class="card" style="margin-bottom: 1rem;">
                                                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                                                    <strong><?php echo htmlspecialchars($instruction['title']); ?></strong>
                                                    <div>
                                                        <span class="badge <?php echo $instruction['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                                            <?php echo $instruction['is_active'] ? 'Активна' : 'Неактивна'; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <p><small>Автор: <?php echo htmlspecialchars($instruction['author']); ?></small></p>
                                                    <div style="max-height: 150px; overflow: hidden; margin-bottom: 1rem;">
                                                        <?php echo strip_tags(substr($instruction['content'], 0, 200)); ?>...
                                                    </div>
                                                    <div>
                                                        <button type="button" class="btn btn-primary btn-sm" 
                                                                onclick="editInstruction(<?php echo $instruction['id']; ?>, '<?php echo htmlspecialchars($instruction['title']); ?>', '<?php echo $instruction['category']; ?>', `<?php echo addslashes($instruction['content']); ?>`)">
                                                            ✏️ Редактировать
                                                        </button>
                                                        <form method="POST" style="display: inline;">
                                                            <input type="hidden" name="instruction_id" value="<?php echo $instruction['id']; ?>">
                                                            <button type="submit" name="toggle_instruction" class="btn btn-warning btn-sm">
                                                                <?php echo $instruction['is_active'] ? '❌ Деактивировать' : '✅ Активировать'; ?>
                                                            </button>
                                                        </form>
                                                        <a href="instruction_view.php?id=<?php echo $instruction['id']; ?>" class="btn btn-success btn-sm" target="_blank">
                                                            👁️ Просмотр
                                                        </a>
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
            </div>
        </div>
    </div>

    <!-- Froala Editor JS -->
    <script src="../../froala/js/froala_editor.pkgd.min.js"></script>
    <script>
    // Инициализация редактора
    new FroalaEditor('#instruction_content', {
        toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'fontFamily', 'fontSize', 'color', '|', 'paragraphStyle', 'lineHeight', '|', 'formatOL', 'formatUL', '|', 'insertLink', 'insertImage', '|', 'emoticons', 'insertTable', '|', 'undo', 'redo'],
        language: 'ru',
        heightMin: 300
    });

    // Функция редактирования инструкции
    function editInstruction(id, title, category, content) {
        document.getElementById('instruction_id').value = id;
        document.getElementById('instruction_title').value = title;
        document.querySelector('select[name="category"]').value = category;
        
        // Обновляем содержимое редактора
        var editor = FroalaEditor.INSTANCES[0];
        editor.html.set(content);
        
        document.getElementById('cancelEdit').style.display = 'inline-block';
        document.querySelector('button[name="save_instruction"]').textContent = 'Обновить инструкцию';
        
        // Прокрутка к форме
        document.getElementById('instructionForm').scrollIntoView({ behavior: 'smooth' });
    }

    // Отмена редактирования
    document.getElementById('cancelEdit').addEventListener('click', function() {
        document.getElementById('instruction_id').value = '';
        document.getElementById('instruction_title').value = '';
        document.querySelector('select[name="category"]').value = '';
        
        var editor = FroalaEditor.INSTANCES[0];
        editor.html.set('');
        
        this.style.display = 'none';
        document.querySelector('button[name="save_instruction"]').textContent = 'Сохранить инструкцию';
    });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>