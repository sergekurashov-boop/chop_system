<?php
// modules/requests/debug_js.php
echo "<h3>🔍 ДИАГНОСТИКА JS ПУТЕЙ</h3>";

$js_paths = [
    'http://localhost/chop_system/assets/js/script.js',
    'http://localhost/chop_system/assets/js/sidebar.js',
    '/chop_system/assets/js/script.js', 
    '/chop_system/assets/js/sidebar.js',
    ASSETS_URL . '/js/script.js',
    ASSETS_URL . '/js/sidebar.js'
];

foreach ($js_paths as $path) {
    echo "<p><strong>Проверка:</strong> $path</p>";
    
    // Проверяем что возвращает сервер
    $content = @file_get_contents($path);
    if ($content === FALSE) {
        echo "<p style='color: red'>❌ Файл не доступен</p>";
    } else {
        $first_chars = substr($content, 0, 50);
        echo "<p style='color: green'>✅ Доступен (первые символы: " . htmlspecialchars($first_chars) . ")</p>";
    }
}
?>