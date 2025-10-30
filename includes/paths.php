<?php
// includes/paths.php

function getModuleCSSPath($module, $levels = 2) {
    return getBasePath($levels) . "modules/{$module}/assets/css/";
}

function getModuleJSPath($module, $levels = 2) {
    return getBasePath($levels) . "modules/{$module}/assets/js/";
}

// Для видео модуля
function getVideoCSS() {
    return getModuleCSSPath('video') . 'video.css';
}

function getVideoJS() {
    return getModuleJSPath('video') . 'video.js';
}

function getVideoGeneratorJS() {
    return getModuleJSPath('video') . 'video-generator.js';
}
?>