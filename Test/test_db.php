<?php
// test_db.php - проверка подключения к БД
session_start();

$host = 'localhost';
$dbname = 'chop_system';
$username = 'root';
$password = '';

echo "<h3>Тестирование подключения к MySQL</h3>";

try {
    // Попытка 1: Без пароля
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username);
    echo "✅ Успешное подключение без пароля<br>";
    
    // Проверим таблицы
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "✅ Таблицы в базе данных: " . implode(', ', $tables) . "<br>";
    
    // Проверим пользователей
    $stmt = $pdo->query("SELECT username, full_name, role FROM users");
    $users = $stmt->fetchAll();
    
    echo "✅ Пользователи в системе:<br>";
    foreach ($users as $user) {
        echo "- {$user['username']} ({$user['full_name']}) - {$user['role']}<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "<br>";
    
    // Попробуем с пустым паролем
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, '');
        echo "✅ Успешное подключение с пустым паролем<br>";
    } catch (PDOException $e2) {
        echo "❌ Ошибка с пустым паролем: " . $e2->getMessage() . "<br>";
    }
}
?>