<?php
// includes/paths.php
function getBasePath($levels = 2) {
    return str_repeat('../', $levels);
}

function getCSSPath($levels = 2) {
    return getBasePath($levels) . 'assets/css/style.css';
}

function getJSPath($levels = 2) {
    return getBasePath($levels) . 'assets/js/script.js';
}

function getHeaderPath($levels = 2) {
    return getBasePath($levels) . 'includes/header.php';
}

function getSidebarPath($levels = 2) {
    return getBasePath($levels) . 'includes/sidebar.php';
}

function getFooterPath($levels = 2) {
    return getBasePath($levels) . 'includes/footer.php';
}
?>