<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$backup_dir = '../../backups/';
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Обработка создания бэкапа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['backup_db'])) {
        $result = backupDatabase();
    } elseif (isset($_POST['backup_system'])) {
        $result = backupSystem();
    } elseif (isset($_POST['restore_db'])) {
        $result = restoreDatabase($_POST['backup_file']);
    }
}

// Функция бэкапа базы данных
function backupDatabase() {
    global $pdo, $backup_dir;
    
    try {
        $timestamp = date('Y-m-d_H-i-s');
        $backup_file = $backup_dir . "db_backup_{$timestamp}.sql";
        
        // Получаем все таблицы
        $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $backup_content = "";
        $backup_content .= "-- Backup created: " . date('Y-m-d H:i:s') . "\n";
        $backup_content .= "-- Database: " . DB_NAME . "\n\n";
        
        foreach ($tables as $table) {
            // Структура таблицы
            $backup_content .= "--\n-- Table structure for table `$table`\n--\n";
            $create_table = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
            $backup_content .= $create_table['Create Table'] . ";\n\n";
            
            // Данные таблицы
            $backup_content .= "--\n-- Dumping data for table `$table`\n--\n";
            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($rows) > 0) {
                $columns = array_keys($rows[0]);
                $backup_content .= "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES \n";
                
                $values = [];
                foreach ($rows as $row) {
                    $escaped_values = array_map(function($value) use ($pdo) {
                        return $value === null ? 'NULL' : $pdo->quote($value);
                    }, $row);
                    $values[] = "(" . implode(', ', $escaped_values) . ")";
                }
                $backup_content .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        if (file_put_contents($backup_file, $backup_content)) {
            return ['success' => true, 'message' => 'Бэкап базы данных создан: ' . basename($backup_file)];
        } else {
            return ['success' => false, 'message' => 'Ошибка записи файла'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
    }
}

// Функция бэкапа системы
function backupSystem() {
    global $backup_dir;
    
    try {
        $timestamp = date('Y-m-d_H-i-s');
        $backup_file = $backup_dir . "system_backup_{$timestamp}.zip";
        
        // Список файлов для бэкапа (исключая временные файлы и бэкапы)
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator('../../'),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        
        $zip = new ZipArchive();
        if ($zip->open($backup_file, ZipArchive::CREATE) === TRUE) {
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen('../../') + 1);
                    
                    // Исключаем папку бэкапов и временные файлы
                    if (strpos($relativePath, 'backups/') === 0) continue;
                    if (strpos($relativePath, '.git/') === 0) continue;
                    
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
            return ['success' => true, 'message' => 'Бэкап системы создан: ' . basename($backup_file)];
        } else {
            return ['success' => false, 'message' => 'Не удалось создать ZIP архив'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()];
    }
}

// Функция восстановления БД
function restoreDatabase($backup_file) {
    global $pdo, $backup_dir;
    
    try {
        $full_path = $backup_dir . $backup_file;
        if (!file_exists($full_path)) {
            return ['success' => false, 'message' => 'Файл бэкапа не найден'];
        }
        
        $sql = file_get_contents($full_path);
        $pdo->exec($sql);
        
        return ['success' => true, 'message' => 'База данных восстановлена из: ' . $backup_file];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Ошибка восстановления: ' . $e->getMessage()];
    }
}

// Получаем список бэкапов
$db_backups = [];
$system_backups = [];
if (file_exists($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        if (strpos($file, 'db_backup_') === 0) {
            $db_backups[] = $file;
        } elseif (strpos($file, 'system_backup_') === 0) {
            $system_backups[] = $file;
        }
    }
    rsort($db_backups);
    rsort($system_backups);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление бэкапами</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Управление бэкапами</h2>
            </div>
            <div class="card-body">
                <?php if (isset($result)): ?>
                    <div class="alert <?php echo $result['success'] ? 'alert-success' : 'alert-error'; ?>">
                        <?php echo $result['message']; ?>
                    </div>
                <?php endif; ?>

                <!-- Бэкап базы данных -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h3>База данных</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <form method="POST">
                                    <button type="submit" name="backup_db" class="btn btn-primary">
                                        Создать бэкап БД
                                    </button>
                                </form>
                            </div>
                            <div class="col-6">
                                <form method="POST">
                                    <div class="form-group">
                                        <label class="form-label">Восстановить из бэкапа:</label>
                                        <select name="backup_file" class="form-control" required>
                                            <option value="">Выберите файл бэкапа</option>
                                            <?php foreach ($db_backups as $backup): ?>
                                                <option value="<?php echo $backup; ?>"><?php echo $backup; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="restore_db" class="btn btn-warning" 
                                            onclick="return confirm('ВНИМАНИЕ: Это перезапишет текущую базу данных! Продолжить?')">
                                        Восстановить БД
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <?php if (count($db_backups) > 0): ?>
                            <h4>Доступные бэкапы БД:</h4>
                            <ul>
                                <?php foreach ($db_backups as $backup): ?>
                                    <li>
                                        <?php echo $backup; ?>
                                        (<?php echo round(filesize($backup_dir . $backup) / 1024, 2); ?> KB)
                                        - <a href="<?php echo $backup_dir . $backup; ?>" download>Скачать</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Бэкапы базы данных отсутствуют</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Бэкап системы -->
                <div class="card">
                    <div class="card-header">
                        <h3>Файлы системы</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <button type="submit" name="backup_system" class="btn btn-primary">
                                Создать бэкап системы (ZIP)
                            </button>
                        </form>
                        
                        <?php if (count($system_backups) > 0): ?>
                            <h4 style="margin-top: 1rem;">Доступные бэкапы системы:</h4>
                            <ul>
                                <?php foreach ($system_backups as $backup): ?>
                                    <li>
                                        <?php echo $backup; ?>
                                        (<?php echo round(filesize($backup_dir . $backup) / 1024 / 1024, 2); ?> MB)
                                        - <a href="<?php echo $backup_dir . $backup; ?>" download>Скачать</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Бэкапы системы отсутствуют</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Информация -->
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h3>Информация</h3>
                    </div>
                    <div class="card-body">
                        <p><strong>Рекомендуется:</strong></p>
                        <ul>
                            <li>Создавать бэкап БД перед крупными изменениями</li>
                            <li>Создавать бэкап системы перед обновлениями</li>
                            <li>Хранить бэкапы в безопасном месте</li>
                            <li>Регулярно проверять целостность бэкапов</li>
                        </ul>
                        <p><strong>Папка бэкапов:</strong> <?php echo realpath($backup_dir); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>