<?php
// test_connection.php - проверка подключения с правильным паролем
session_start();

$host = 'localhost';
$dbname = 'chop_system';
$username = 'root';
$password = 'root';

echo "<h3>Тестирование подключения к MySQL UniServerZ</h3>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Успешное подключение к базе данных!<br>";
    
    // Проверим таблицы
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "✅ Таблицы в базе данных: <br>";
    foreach ($tables as $table) {
        echo "- $table<br>";
    }
    
    // Проверим пользователей
    $stmt = $pdo->query("SELECT username, full_name, role FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "✅ Пользователи в системе:<br>";
        foreach ($users as $user) {
            echo "- {$user['username']} ({$user['full_name']}) - {$user['role']}<br>";
        }
    } else {
        echo "⚠️ В таблице users нет данных<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения: " . $e->getMessage() . "<br>";
}
?>