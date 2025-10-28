<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('reports') && !hasAccess('admin')) {
    die('Доступ запрещен');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отчет за смену</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📋 Отчет за смену</h2>
                <a href="dashboard.php" class="btn btn-secondary">← Назад</a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Модуль в разработке</strong><br>
                    Здесь будет формирование отчета по выполнению смены
                </div>
                
                <!-- Форма выбора параметров -->
                <div class="card">
                    <div class="card-header">Параметры отчета</div>
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Дата смены</label>
                                        <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>Смена</label>
                                        <select class="form-control">
                                            <option>Все смены</option>
                                            <option>Утренняя (08:00-20:00)</option>
                                            <option>Ночная (20:00-08:00)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">Сформировать</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>