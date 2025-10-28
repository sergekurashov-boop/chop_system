<?php
// test_login.php - тестирование входа
require_once 'includes/config.php';
require_once 'includes/auth.php';

echo "<h3>Тестирование аутентификации</h3>";

// Тестовые данные
$test_users = [
    ['admin', 'password'],
    ['senior1', 'password'],
    ['medic1', 'password'],
    ['guard1', 'password']
];

foreach ($test_users as $user_data) {
    $username = $user_data[0];
    $password = $user_data[1];
    
    // Получаем хеш из базы
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        $is_valid = password_verify($password, $user['password']);
        echo "Пользователь: <strong>$username</strong> | ";
        echo "Пароль 'password': " . ($is_valid ? "✅ ВЕРНЫЙ" : "❌ НЕВЕРНЫЙ") . "<br>";
    } else {
        echo "Пользователь: <strong>$username</strong> | ❌ НЕ НАЙДЕН<br>";
    }
}
?>