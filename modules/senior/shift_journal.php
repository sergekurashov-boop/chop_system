<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('senior') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();

// Обработка отметки о инструктаже
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_briefing'])) {
        $assignment_id = intval($_POST['assignment_id']);
        $stmt = $pdo->prepare("UPDATE shift_assignments SET briefing_completed = 1 WHERE id = ?");
        $stmt->execute([$assignment_id]);
        $_SESSION['success_message'] = 'Отметка о инструктаже сохранена';
    }
    
    if (isset($_POST['add_incident'])) {
        $shift_id = intval($_POST['shift_id']);
        $incident_type = $_POST['incident_type'];
        $description = $_POST['description'];
        $severity = $_POST['severity'];
        
        $stmt = $pdo->prepare("INSERT INTO shift_incidents (shift_id, incident_type, description, severity, reported_by, reported_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$shift_id, $incident_type, $description, $severity, $_SESSION['user_id']]);
        
        // Отправка email уведомления
        sendIncidentNotification($shift_id, $incident_type, $description, $severity);
        
        $_SESSION['success_message'] = 'Сообщение о происшествии отправлено';
    }
}

// Получаем активные смены
$stmt = $pdo->query("
    SELECT s.*, COUNT(sa.id) as assigned_count 
    FROM shifts s 
    LEFT JOIN shift_assignments sa ON s.id = sa.shift_id 
    WHERE s.start_datetime <= NOW() AND s.end_datetime >= NOW()
    GROUP BY s.id 
    ORDER BY s.start_datetime
");
$active_shifts = $stmt->fetchAll();

// Функция отправки уведомлений
function sendIncidentNotification($shift_id, $type, $description, $severity) {
    // Настройки email (заменить на реальные)
    $to = "security@chop.ru, senior@chop.ru";
    $subject = "НШС на смене #$shift_id";
    
    $message = "
    <h3>Сообщение о происшествии</h3>
    <p><strong>Смена:</strong> #$shift_id</p>
    <p><strong>Тип:</strong> $type</p>
    <p><strong>Уровень серьезности:</strong> $severity</p>
    <p><strong>Описание:</strong></p>
    <div>$description</div>
    <p><em>Отправлено из системы ЧОП " . date('d.m.Y H:i') . "</em></p>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    
    // В реальной системе раскомментировать:
    // mail($to, $subject, $message, $headers);
    
    // Логируем для отладки
    error_log("Incident notification: Shift #$shift_id - $type - $severity");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Журнал учета смен</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Froala Editor -->
    <link rel="stylesheet" href="../../froala/css/froala_editor.pkgd.min.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📋 Журнал учета смен</h2>
            </div>
            <div class="card-body">
                <?php displayMessages(); ?>

                <!-- Активные смены -->
                <div class="card">
                    <div class="card-header">
                        <h3>🚨 Активные смены</h3>
                    </div>
                    <div class="card-body">
                        <?php if (count($active_shifts) > 0): ?>
                            <?php foreach ($active_shifts as $shift): ?>
                                <div class="card" style="margin-bottom: 1rem;">
                                    <div class="card-header">
                                        <strong>Смена #<?php echo $shift['id']; ?></strong> - 
                                        <?php echo htmlspecialchars($shift['location']); ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
    <h4>Инструктажи сотрудников</h4>
    <?php
    $stmt = $pdo->prepare("
        SELECT sa.*, e.full_name, e.id as employee_id
        FROM shift_assignments sa 
        JOIN employees e ON sa.employee_id = e.id 
        WHERE sa.shift_id = ?
    ");
    $stmt->execute([$shift['id']]);
    $assignments = $stmt->fetchAll();
    ?>
    
    <?php foreach ($assignments as $assignment): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
            <div>
                <strong><?php echo htmlspecialchars($assignment['full_name']); ?></strong>
                <br>
                <small style="color: #666;">
                    <?php 
                    // Проверяем пройденные инструктажи
                    $stmt_completions = $pdo->prepare("
                        SELECT COUNT(*) as completed_count 
                        FROM instruction_completions 
                        WHERE employee_id = ? AND DATE(completed_at) = CURDATE()
                    ");
                    $stmt_completions->execute([$assignment['employee_id']]);
                    $completions = $stmt_completions->fetch();
                    echo "Инструктажей сегодня: " . $completions['completed_count'];
                    ?>
                </small>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 0.3rem; align-items: flex-end;">
                <?php if ($assignment['briefing_completed']): ?>
                    <span class="badge badge-success">✅ Инструктаж пройден</span>
                    <small style="color: #28a745;">Завершено</small>
                <?php else: ?>
                    <div style="display: flex; gap: 0.3rem;">
                        <!-- Кнопка отметки инструктажа -->
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                            <button type="submit" name="mark_briefing" class="btn btn-primary btn-sm"
                                    title="Отметить прохождение инструктажа">
                                ✅ Отметить
                            </button>
                        </form>
                        
                        <!-- Кнопка проведения инструктажа -->
                        <a href="<?php echo BASE_URL; ?>/modules/senior/instruction_conduct.php?assignment_id=<?php echo $assignment['id']; ?>&shift_id=<?php echo $shift['id']; ?>" 
                           class="btn btn-success btn-sm"
                           title="Провести инструктаж сотрудника">
                            📖 Провести
                        </a>
                    </div>
                    <small style="color: #dc3545;">Ожидает инструктажа</small>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    
    <!-- Статистика по смене -->
    <?php
    $total_assignments = count($assignments);
    $completed_briefings = array_filter($assignments, function($a) { return $a['briefing_completed']; });
    $completed_count = count($completed_briefings);
    ?>
    <div style="margin-top: 1rem; padding: 0.8rem; background: #f8f9fa; border-radius: 4px;">
        <strong>Статистика по смене:</strong><br>
        <small>
            ✅ Завершено: <?php echo $completed_count; ?> из <?php echo $total_assignments; ?><br>
            ⏳ Осталось: <?php echo $total_assignments - $completed_count; ?> сотрудников
        </small>
    </div>
</div>
                                            <div class="col-6">
                                                <h4>📢 Сообщение о происшествии</h4>
                                                <form method="POST">
                                                    <input type="hidden" name="shift_id" value="<?php echo $shift['id']; ?>">
                                                    
                                                    <div class="form-group">
                                                        <label class="form-label">Тип происшествия:</label>
                                                        <select name="incident_type" class="form-control" required>
                                                            <option value="">Выберите тип</option>
                                                            <option value="НШС">Нештатная ситуация (НШС)</option>
                                                            <option value="Происшествие">Происшествие</option>
                                                            <option value="Нарушение">Нарушение режима</option>
                                                            <option value="Техника">Техническая неисправность</option>
                                                            <option value="Другое">Другое</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label class="form-label">Уровень серьезности:</label>
                                                        <select name="severity" class="form-control" required>
                                                            <option value="Низкий">Низкий</option>
                                                            <option value="Средний">Средний</option>
                                                            <option value="Высокий">Высокий</option>
                                                            <option value="Критический">Критический</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label class="form-label">Описание:</label>
                                                        <textarea id="incident_editor_<?php echo $shift['id']; ?>" name="description" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                    
                                                    <button type="submit" name="add_incident" class="btn btn-danger">
                                                        📨 Отправить сообщение
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Нет активных смен в текущий момент.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- История происшествий -->
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>📊 История происшествий</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $stmt = $pdo->query("
                            SELECT si.*, s.location, u.full_name as reporter 
                            FROM shift_incidents si 
                            JOIN shifts s ON si.shift_id = s.id 
                            JOIN users u ON si.reported_by = u.id 
                            ORDER BY si.reported_at DESC 
                            LIMIT 10
                        ");
                        $incidents = $stmt->fetchAll();
                        ?>
                        
                        <?php if (count($incidents) > 0): ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Смена</th>
                                        <th>Тип</th>
                                        <th>Уровень</th>
                                        <th>Сообщил</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($incidents as $incident): ?>
                                        <tr>
                                            <td><?php echo date('d.m.Y H:i', strtotime($incident['reported_at'])); ?></td>
                                            <td>#<?php echo $incident['shift_id']; ?> (<?php echo htmlspecialchars($incident['location']); ?>)</td>
                                            <td><?php echo htmlspecialchars($incident['incident_type']); ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php 
                                                    if ($incident['severity'] === 'Критический') echo 'badge-danger';
                                                    elseif ($incident['severity'] === 'Высокий') echo 'badge-warning';
                                                    elseif ($incident['severity'] === 'Средний') echo 'badge-secondary';
                                                    else echo 'badge-success';
                                                    ?>
                                                ">
                                                    <?php echo htmlspecialchars($incident['severity']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($incident['reporter']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>История происшествий пуста.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Froala Editor JS -->
    <script src="../../froala/js/froala_editor.pkgd.min.js"></script>
    <script>
    // Инициализация редакторов для всех текстовых полей
    document.addEventListener('DOMContentLoaded', function() {
        var editors = document.querySelectorAll('textarea[id^="incident_editor"]');
        editors.forEach(function(editor) {
            new FroalaEditor(editor, {
                toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'fontFamily', 'fontSize', 'color', '|', 'paragraphStyle', 'lineHeight', '|', 'insertLink', 'insertImage', '|', 'emoticons', 'insertTable', '|', 'undo', 'redo'],
                language: 'ru',
                heightMin: 200
            });
        });
    });
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>