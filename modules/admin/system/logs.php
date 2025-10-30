<?php
require_once '../../../includes/config.php';
require_once '../../../includes/auth.php';
require_once '../../../includes/functions.php';

checkAuth();

if (!hasAccess('admin')) {
    die('Доступ запрещен');
}

$page_title = "Логи системы";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <!-- Кнопка мобильного меню -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">☰</button>
    
    <?php 
    include '../../../includes/header.php'; 
    include '../../../includes/sidebar.php';
    ?>
    
    <!-- Основной контент -->
    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>📋 Логи системы</h2>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        ℹ️ Модуль логов находится в разработке.
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h4>🔍 Фильтры логов</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Тип события</label>
                                        <select class="form-control">
                                            <option>Все события</option>
                                            <option>Вход в систему</option>
                                            <option>Изменение данных</option>
                                            <option>Ошибки</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Пользователь</label>
                                        <select class="form-control">
                                            <option>Все пользователи</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Дата с</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="form-label">Дата по</label>
                                        <input type="date" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary">Применить фильтры</button>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>📜 Последние события</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Время</th>
                                            <th>Пользователь</th>
                                            <th>Событие</th>
                                            <th>Детали</th>
                                            <th>IP-адрес</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= date('d.m.Y H:i:s') ?></td>
                                            <td>admin</td>
                                            <td><span class="badge badge-success">Вход в систему</span></td>
                                            <td>Успешная авторизация</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <tr>
                                            <td><?= date('d.m.Y H:i:s', time() - 3600) ?></td>
                                            <td>admin</td>
                                            <td><span class="badge badge-info">Просмотр отчета</span></td>
                                            <td>Отчет по сменам</td>
                                            <td>192.168.1.100</td>
                                        </tr>
                                        <!-- Здесь будут реальные логи из БД -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../../assets/js/script.js"></script>

    <?php include '../../../includes/footer.php'; ?>
</body>
</html>