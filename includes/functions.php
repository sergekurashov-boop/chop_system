<?php
function getCurrentDateTime() {
    return date('Y-m-d H:i:s');
}

function formatDate($date, $format = 'd.m.Y') {
    if (empty($date)) return '';
    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    if ($datetime === false) {
        $datetime = DateTime::createFromFormat('Y-m-d', $date);
    }
    return $datetime ? $datetime->format($format) : $date;
}

function escape($data) {
    if (is_array($data)) {
        return array_map('escape', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function getShiftTypes() {
    return [
        '12_hours' => '12 часов',
        '24_hours' => '24 часа'
    ];
}

function getMedicalStatuses() {
    return [
        'pending' => 'Ожидает',
        'passed' => 'Пройден', 
        'failed' => 'Не пройден'
    ];
}

function addSuccessMessage($message) {
    if (!isset($_SESSION['success_messages'])) {
        $_SESSION['success_messages'] = [];
    }
    $_SESSION['success_messages'][] = $message;
}

function addErrorMessage($message) {
    if (!isset($_SESSION['error_messages'])) {
        $_SESSION['error_messages'] = [];
    }
    $_SESSION['error_messages'][] = $message;
}

function displayMessages() {
    if (!empty($_SESSION['success_messages'])) {
        foreach ($_SESSION['success_messages'] as $message) {
            echo '<div class="alert alert-success">' . escape($message) . '</div>';
        }
        $_SESSION['success_messages'] = [];
    }
    
    if (!empty($_SESSION['error_messages'])) {
        foreach ($_SESSION['error_messages'] as $message) {
            echo '<div class="alert alert-error">' . escape($message) . '</div>';
        }
        $_SESSION['error_messages'] = [];
    }
}

// Функция для проверки прав доступа
function hasAccess($requiredRole) {
    $userRole = $_SESSION['user_role'] ?? 'guest';
    
    if ($userRole === 'admin') return true;
    
    // Иерархия прав
    $hierarchy = [
        'admin' => 5,
        'senior' => 4,
        'medic' => 3,
        'reports' => 2,
        'guard' => 1
    ];
    
    return ($hierarchy[$userRole] ?? 0) >= ($hierarchy[$requiredRole] ?? 0);
}
?>