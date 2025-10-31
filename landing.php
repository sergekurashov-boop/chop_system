<?php
// В начало landing.php добавляем
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CHOP Manager - Система управления частным охранным предприятием</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .cta-section {
            background: #f8f9fa;
            padding: 3rem 0;
            text-align: center;
            margin-top: 3rem;
        }
        .demo-badge {
            background: linear-gradient(45deg, #00ff88, #00ccff);
            color: black;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin: 1rem 0;
        }
    </style>
	<!-- Улучшение мобильного отображения -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="#343a40">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <a href="index.php" style="color: white; text-decoration: none;">
                    🛡️ CHOP Manager
                </a>
            </div>
            <ul class="nav-menu">
                <li><a href="#features">Возможности</a></li>
                <li><a href="#demo">Демо</a></li>
                <li><a href="login.php" class="btn btn-primary">Войти в систему</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 1rem;">🛡️ CHOP Manager</h1>
            <p style="font-size: 1.5rem; margin-bottom: 2rem;">Профессиональная система управления для частных охранных предприятий</p>
            <p style="font-size: 1.2rem; max-width: 800px; margin: 0 auto 2rem;">
                Автоматизация учета смен, инструктажей, медицинских осмотров и отчетности в единой системе
            </p>
            <div>
                <a href="demo.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">🚀 Попробовать демо</a>
                <a href="#features" class="btn btn-secondary" style="font-size: 1.2rem; padding: 1rem 2rem;">📋 Возможности</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="container">
        <h2 style="text-align: center; margin-bottom: 3rem;">📊 Ключевые возможности системы</h2>
        
        <div class="features-grid">
            <!-- Управление сменами -->
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Управление сменами</h3>
                <p>Создание 12/24 часовых смен, назначение сотрудников, контроль явки и выполнения обязанностей</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Гибкое планирование смен</li>
                    <li>Назначение охранников</li>
                    <li>Контроль выполнения</li>
                </ul>
            </div>

            <!-- Инструктажи -->
            <div class="feature-card">
                <div class="feature-icon">📖</div>
                <h3>Система инструктажей</h3>
                <p>Проведение и учет первичных инструктажей, контроль прохождения сотрудниками</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Rich-text редактор инструкций</li>
                    <li>Отметки о прохождении</li>
                    <li>История инструктажей</li>
                </ul>
            </div>

            <!-- Медосмотры -->
            <div class="feature-card">
                <div class="feature-icon">🏥</div>
                <h3>Медицинский учет</h3>
                <p>Контроль медицинских осмотров, учет медкнижек, уведомления об истечении срока</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Медкарты сотрудников</li>
                    <li>Контроль сроков</li>
                    <li>Напоминания</li>
                </ul>
            </div>

            <!-- Отчетность -->
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Отчетность</h3>
                <p>Автоматическое формирование отчетов за смену, месяц, по происшествиям и выходам на работу</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Ежедневные отчеты</li>
                    <li>Анализ происшествий</li>
                    <li>Статистика работы</li>
                </ul>
            </div>

            <!-- Безопасность -->
            <div class="feature-card">
                <div class="feature-icon">🔒</div>
                <h3>Безопасность и бэкапы</h3>
                <p>Разграничение прав доступа, система резервного копирования данных, защита информации</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Ролевая модель доступа</li>
                    <li>Резервное копирование</li>
                    <li>Восстановление данных</li>
                </ul>
            </div>

            <!-- НШС -->
            <div class="feature-card">
                <div class="feature-icon">🚨</div>
                <h3>Учет происшествий</h3>
                <p>Фиксация нештатных ситуаций, классификация по типам и уровням серьезности</p>
                <ul style="text-align: left; margin-top: 1rem;">
                    <li>Типизация происшествий</li>
                    <li>Оценка серьезности</li>
                    <li>Статистика НШС</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="cta-section">
        <div class="container">
            <h2>🚀 Готовы попробовать?</h2>
            <p style="font-size: 1.2rem; margin-bottom: 2rem;">
                Протестируйте все возможности системы с демо-доступом
            </p>
            <div class="demo-badge">
                🔥 Демо-версия включает все функции
            </div>
            <br>
            <a href="demo.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 1rem 2rem;">
                🎯 Открыть демо-версию
            </a>
            <p style="margin-top: 1rem; color: #666;">
                Или <a href="login.php">войдите в систему</a> если у вас есть аккаунт
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: var(--dark-gray); color: white; padding: 2rem 0; text-align: center;">
        <div class="container">
            <p>🛡️ CHOP Manager - Система управления частным охранным предприятием</p>
            <p>© 2024 Все права защищены</p>
        </div>
    </footer>
</body>
</html>