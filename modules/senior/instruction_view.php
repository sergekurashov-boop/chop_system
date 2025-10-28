<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!isset($_GET['id'])) {
    die('ID инструкции не указан');
}

$instruction_id = intval($_GET['id']);
$pdo = getDB();

// Получаем инструкцию
$stmt = $pdo->prepare("SELECT * FROM instructions WHERE id = ?");
$stmt->execute([$instruction_id]);
$instruction = $stmt->fetch();

if (!$instruction) {
    die('Инструкция не найдена');
}

// Категории
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
    <title><?php echo htmlspecialchars($instruction['title']); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .instruction-content {
            line-height: 1.6;
            font-size: 16px;
        }
        .instruction-content h2, .instruction-content h3, .instruction-content h4 {
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .instruction-content p {
            margin-bottom: 1rem;
        }
        .instruction-content ul, .instruction-content ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2><?php echo htmlspecialchars($instruction['title']); ?></h2>
                <p class="text-muted">Категория: <?php echo $categories[$instruction['category']]; ?></p>
            </div>
            <div class="card-body instruction-content">
                <?php echo $instruction['content']; ?>
            </div>
            <div class="card-footer">
                <button onclick="window.print()" class="btn btn-primary">🖨️ Печать</button>
                <button onclick="window.close()" class="btn btn-secondary">Закрыть</button>
            </div>
        </div>
    </div>
</body>
</html>