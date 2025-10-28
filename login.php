<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    require_once 'includes/auth.php';
    
    if (loginUser($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему ЧОП</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container" style="max-width: 400px; margin: 100px auto; padding: 0 20px;">
        <div class="card">
            <div class="card-header" style="text-align: center;">
                <h2>Вход в систему ЧОП</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Имя пользователя:</label>
                        <input type="text" name="username" class="form-control" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Пароль:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Войти</button>
                </form>
                
                <div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 4px;">
                    <strong>Тестовые доступы:</strong><br>
                    Логин: <code>admin</code> | Пароль: <code>password</code><br>
                    Логин: <code>senior1</code> | Пароль: <code>password</code><br>
                    Логин: <code>medic1</code> | Пароль: <code>password</code>
                </div>
            </div>
        </div>
    </div>
</body>
</html>