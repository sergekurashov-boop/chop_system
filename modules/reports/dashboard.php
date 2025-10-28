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
    <title>Отчеты - Главная</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/header.php'; ?>
    
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>📊 Система отчетности</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Отчет за смену -->
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>📋 Отчет за смену</h4>
                            </div>
                            <div class="card-body">
                                <p>Ежедневный отчет по выполнению смен</p>
                                <a href="shift_report.php" class="btn btn-primary">Сформировать</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отчет по происшествиям -->
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>🚨 Отчет по происшествиям</h4>
                            </div>
                            <div class="card-body">
                                <p>Анализ нештатных ситуаций</p>
                                <a href="incident_report.php" class="btn btn-warning">Сформировать</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Отчет по выходам на работу -->
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>👥 Выходы на работу</h4>
                            </div>
                            <div class="card-body">
                                <p>Учет рабочего времени</p>
                                <a href="attendance_report.php" class="btn btn-info">Сформировать</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row" style="margin-top: 2rem;">
                    <!-- Месячный отчет -->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>📈 Месячный отчет</h4>
                            </div>
                            <div class="card-body">
                                <p>Сводная статистика за месяц</p>
                                <a href="monthly_report.php" class="btn btn-success">Сформировать</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Медицинские отчеты -->
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>🏥 Медосмотры</h4>
                            </div>
                            <div class="card-body">
                                <p>Отчеты по медицинским осмотрам</p>
                                <a href="medical_report.php" class="btn btn-secondary">Сформировать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>