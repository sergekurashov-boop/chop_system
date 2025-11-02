<?php
session_start();

// Базовые пути проекта
define('BASE_URL', '/chop_system');
define('BASE_DIR', dirname(__DIR__));

// Пути к ресурсам
define('ASSETS_URL', BASE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('INCLUDES_DIR', BASE_DIR . '/includes');

// Основные файлы
define('MAIN_CSS', CSS_URL . '/style.css');
define('MAIN_JS', JS_URL . '/script.js');

// Включим вывод ошибок для разработки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Конфигурация базы данных для UniServerZ
define('DB_HOST', 'localhost');
define('DB_NAME', 'chop_system');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // Пароль для UniServerZ

class Database {
    private static $instance = null;
    private $pdo;
    
    private function __construct() {
        try {
            $this->pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}

function getDB() {
    return Database::getInstance();
}

function redirect($url) {
    header("Location: $url");
    exit;
}

// Автозагрузка функций
require_once 'functions.php';
// Добавляем в config.php после определения констант
function loadTheme() {
    $theme = $_SESSION['selected_theme'] ?? 'default';
    
    // Если тема выбрана через localStorage
    if (isset($_GET['theme'])) {
        $theme = $_GET['theme'];
        $_SESSION['selected_theme'] = $theme;
    }
    
    return $theme;
}

// Автоматическая загрузка темы
$currentTheme = loadTheme();
define('CURRENT_THEME', $currentTheme);
?>