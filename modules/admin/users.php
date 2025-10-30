<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$pdo = getDB();
$page_title = "Управление пользователями";

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $role = $_POST['role'];
        $password = password_hash('123456', PASSWORD_DEFAULT); // временный пароль
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, full_name, role, password) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $full_name, $role, $password])) {
            $_SESSION['success_message'] = 'Пользователь добавлен. Временный пароль: 123456';
        } else {
            $_SESSION['error_message'] = 'Ошибка при добавлении пользователя';
        }
    }
    
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $full_name = trim($_POST['full_name']);
        $role = $_POST['role'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, is_active = ? WHERE id = ?");
        if ($stmt->execute([$username, $email, $full_name, $role, $is_active, $user_id])) {
            $_SESSION['success_message'] = 'Данные пользователя обновлены';
        } else {
            $_SESSION['error_message'] = 'Ошибка при обновлении данных';
        }
    }
    
    if (isset($_POST['reset_password'])) {
        $user_id = $_POST['user_id'];
        $password = password_hash('123456', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$password, $user_id])) {
            $_SESSION['success_message'] = 'Пароль сброшен. Новый пароль: 123456';
        } else {
            $_SESSION['error_message'] = 'Ошибка при сбросе пароля';
        }
    }
    
    header("Location: users.php");
    exit;
}

// Получаем список пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();

// Роли пользователей
$roles = [
    'admin' => 'Администратор',
    'senior' => 'Руководитель',
    'medic' => 'Медик', 
    'guard' => 'Охранник',
    'reports' => 'Отчеты'
];
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../includes/header.php'; 
    include '../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>👥 Управление пользователями</h2>
                </div>
                <div class="card-body">
                    <?php 
                    if (isset($_SESSION['success_message'])) {
                        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                        unset($_SESSION['success_message']);
                    }
                    if (isset($_SESSION['error_message'])) {
                        echo '<div class="alert alert-error">' . $_SESSION['error_message'] . '</div>';
                        unset($_SESSION['error_message']);
                    }
                    ?>

                    <!-- Форма добавления пользователя -->
                    <div class="card" style="margin-bottom: 2rem;">
                        <div class="card-header">
                            <h3>➕ Добавить пользователя</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Логин *</label>
                                            <input type="text" name="username" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">ФИО *</label>
                                            <input type="text" name="full_name" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label class="form-label">Роль *</label>
                                            <select name="role" class="form-control" required>
                                                <?php foreach ($roles as $key => $name): ?>
                                                    <option value="<?= $key ?>"><?= $name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="add_user" class="btn btn-primary">➕ Добавить пользователя</button>
                                <small class="text-muted">Пароль по умолчанию: 123456</small>
                            </form>
                        </div>
                    </div>

                    <!-- Список пользователей -->
                    <div class="card">
                        <div class="card-header">
                            <h3>📋 Список пользователей</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Логин</th>
                                            <th>ФИО</th>
                                            <th>Email</th>
                                            <th>Роль</th>
                                            <th>Статус</th>
                                            <th>Дата регистрации</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td><?= htmlspecialchars($user['username']) ?></td>
                                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?= $roles[$user['role']] ?? $user['role'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge <?= $user['is_active'] ? 'badge-success' : 'badge-secondary' ?>">
                                                        <?= $user['is_active'] ? 'Активен' : 'Неактивен' ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary" 
                                                            onclick="editUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>', '<?= htmlspecialchars($user['email']) ?>', '<?= htmlspecialchars($user['full_name']) ?>', '<?= $user['role'] ?>', <?= $user['is_active'] ?>)">
                                                        ✏️
                                                    </button>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                        <button type="submit" name="reset_password" class="btn btn-sm btn-warning" 
                                                                onclick="return confirm('Сбросить пароль пользователя?')">
                                                            🔑
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно редактирования -->
    <div id="editUserModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>✏️ Редактирование пользователя</h3>
                <span class="close" onclick="closeModal('editUserModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form method="POST" id="editUserForm">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="form-group">
                        <label class="form-label">Логин</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ФИО</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Роль</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <?php foreach ($roles as $key => $name): ?>
                                <option value="<?= $key ?>"><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <input type="checkbox" name="is_active" id="edit_is_active"> Активен
                        </label>
                    </div>
                    <button type="submit" name="update_user" class="btn btn-primary">💾 Сохранить</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Отмена</button>
                </form>
            </div>
        </div>
    </div>

    <script src="../../assets/js/script.js"></script>
    <script>
    function editUser(id, username, email, fullName, role, isActive) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_full_name').value = fullName;
        document.getElementById('edit_role').value = role;
        document.getElementById('edit_is_active').checked = isActive;
        openModal('editUserModal');
    }
    </script>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>