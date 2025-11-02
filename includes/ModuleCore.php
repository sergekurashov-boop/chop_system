<?php
// includes/ModuleCore.php

class ModuleCore {
    
    /**
     * Подключает базовые зависимости для любого модуля
     */
    public static function init() {
        // Подключаем конфиг и авторизацию
        require_once __DIR__ . '/config.php';
        require_once __DIR__ . '/auth.php';
        require_once __DIR__ . '/functions.php';
        
        // Проверяем авторизацию
        if (!isLoggedIn()) {
            header("Location: " . BASE_URL . "/landing.php");
            exit;
        }
        
        return [
            'pdo' => getDB(),
            'userRole' => getUserRole(),
            'baseUrl' => BASE_URL,
            'assetsUrl' => ASSETS_URL
        ];
    }
    
    /**
     * Подключает header и sidebar
     */
    public static function includeLayout() {
        include __DIR__ . '/header.php';
        include __DIR__ . '/sidebar.php';
    }
    
    /**
     * Подключает footer и скрипты
     */
    public static function includeFooter() {
        include __DIR__ . '/footer.php';
        echo '<script src="' . ASSETS_URL . '/js/script.js"></script>';
        echo '<script src="' . ASSETS_URL . '/js/sidebar.js"></script>';
    }
    
    /**
     * Генерирует полный путь к файлу модуля
     */
    public static function modulePath($module, $file = '') {
        return BASE_DIR . "/modules/{$module}/{$file}";
    }
}
?>