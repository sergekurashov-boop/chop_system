<?php
// modules/medic/medical_cards.php - РАБОЧАЯ ВЕРСИЯ
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

checkAuth();

if (!hasAccess('medic') && !hasAccess('admin')) {
    die('Доступ запрещен');
}

$pageTitle = "Медицинские карты сотрудников";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
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
                    <h2>🏥 Медицинские карты сотрудников</h2>
                    <div class="quick-actions">
                        <a href="add_medical_exam.php" class="btn btn-primary">➕ Новый медосмотр</a>
                        <a href="medical_reports.php" class="btn btn-success">📊 Отчеты</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="filters">
                        <button class="btn btn-outline active" onclick="filterCards('all')">Все</button>
                        <button class="btn btn-outline" onclick="filterCards('expired_medical')">Просрочен медосмотр</button>
                        <button class="btn btn-outline" onclick="filterCards('ok')">Всё в порядке</button>
                    </div>
                    
                    <div class="medical-cards" id="medicalCards">
                        <!-- Демо-карточки -->
                        <div class="medical-card" data-status="ok">
                            <div class="employee-header">
                                <div>
                                    <div class="employee-name">Иванов Иван Иванович</div>
                                    <div class="employee-position">Старший охранник</div>
                                </div>
                                <span class="status-badge status-ok">✅ OK</span>
                            </div>
                            
                            <div class="medical-info">
                                <div class="info-label">Медосмотр</div>
                                <div class="info-value">
                                    Последний: 15.01.2024<br>
                                    Следующий: <span style="color: #28a745;">15.01.2025</span>
                                </div>
                            </div>
                            
                            <div class="license-info">
                                <div class="info-label">Лицензия</div>
                                <div class="info-value">
                                    Истекает: <span style="color: #28a745;">20.12.2024</span>
                                </div>
                            </div>
                            
                            <div class="quick-actions">
                                <a href="employee_medical.php?id=1" class="action-btn btn-primary">📋 Карта</a>
                                <a href="add_medical_exam.php?employee_id=1" class="action-btn btn-success">➕ Медосмотр</a>
                            </div>
                        </div>
                        
                        <div class="medical-card" data-status="warning">
                            <div class="employee-header">
                                <div>
                                    <div class="employee-name">Петров Петр Петрович</div>
                                    <div class="employee-position">Охранник</div>
                                </div>
                                <span class="status-badge status-warning">⚠️ Внимание</span>
                            </div>
                            
                            <div class="medical-info">
                                <div class="info-label">Медосмотр</div>
                                <div class="info-value">
                                    Последний: 20.02.2024<br>
                                    Следующий: <span style="color: #ffc107;">20.11.2024</span>
                                </div>
                            </div>
                            
                            <div class="license-info">
                                <div class="info-label">Лицензия</div>
                                <div class="info-value">
                                    Истекает: <span style="color: #ffc107;">15.11.2024</span>
                                </div>
                            </div>
                            
                            <div class="quick-actions">
                                <a href="employee_medical.php?id=2" class="action-btn btn-primary">📋 Карта</a>
                                <a href="add_medical_exam.php?employee_id=2" class="action-btn btn-success">➕ Медосмотр</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterCards(status) {
            const cards = document.querySelectorAll('.medical-card');
            const filterButtons = document.querySelectorAll('.filters .btn');
            
            filterButtons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (status === 'all') {
                    card.style.display = 'block';
                } else if (status === 'expired_medical') {
                    card.style.display = card.dataset.status === 'warning' ? 'block' : 'none';
                } else if (status === 'ok') {
                    card.style.display = card.dataset.status === 'ok' ? 'block' : 'none';
                }
            });
        }
    </script>

    <script src="../../assets/js/script.js"></script>
    
    <?php include '../../includes/footer.php'; ?>
</body>
</html>