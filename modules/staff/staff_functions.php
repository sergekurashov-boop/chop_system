<?php
// modules/staff/staff_functions.php

/**
 * Получить всех сотрудников с фильтрами
 */
function getAllEmployees($filters = []) {
    global $pdo;
    
    $sql = "SELECT * FROM employees WHERE 1=1";
    $params = [];
    
    // Фильтр по активности (по умолчанию только активные)
    if (empty($filters['show_inactive'])) {
        $sql .= " AND is_active = 1";
    }
    
    // Фильтр по должности
    if (!empty($filters['position'])) {
        $sql .= " AND position = ?";
        $params[] = $filters['position'];
    }
    
    $sql .= " ORDER BY full_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Получить сотрудника по ID
 */
function getEmployeeById($employee_id) {
    global $pdo;
    
    $sql = "SELECT * FROM employees WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$employee_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Получить статистику по сотрудникам
 */
function getEmployeesCount() {
    global $pdo;
    
    $sql = "SELECT 
        COUNT(*) as total,
        SUM(is_active = 1) as active,
        SUM(is_active = 0) as inactive,
        SUM(medical_exam_expiry < CURDATE()) as expired_medical
    FROM employees";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Проверить просроченные документы
 */
function checkExpiredDocuments() {
    global $pdo;
    
    $sql = "SELECT 
        SUM(medical_exam_expiry < CURDATE()) as expired_medical
    FROM employees 
    WHERE is_active = 1";
    
    $stmt = $pdo->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'expired_medical' => $result['expired_medical'] ?? 0
    ];
}

/**
 * Архивировать сотрудника (безопасное "удаление")
 */
function archiveEmployee($employee_id) {
    global $pdo;
    
    $sql = "UPDATE employees SET is_active = 0 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$employee_id]);
}

/**
 * Активировать сотрудника (вернуть из архива)
 */
function activateEmployee($employee_id) {
    global $pdo;
    
    $sql = "UPDATE employees SET is_active = 1 WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$employee_id]);
}

/**
 * Получить список всех должностей
 */
function getPositionsList() {
    global $pdo;
    
    $sql = "SELECT DISTINCT position FROM employees WHERE position IS NOT NULL AND position != '' ORDER BY position";
    $stmt = $pdo->query($sql);
    $positions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Стандартные должности, если в базе пусто
    if (empty($positions)) {
        $positions = [
            'Начальник охраны',
            'Старший охранник', 
            'Охранник',
            'Патрульный',
            'Диспетчер',
            'Оператор'
        ];
    }
    
    return $positions;
}

/**
 * Получить сотрудников с просроченными медосмотрами
 */
function getEmployeesWithExpiredMedical() {
    global $pdo;
    
    $sql = "SELECT id, full_name, position, medical_exam_expiry 
            FROM employees 
            WHERE medical_exam_expiry < CURDATE() AND is_active = 1 
            ORDER BY medical_exam_expiry";
    
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Обновить дату медосмотра
 */
function updateMedicalExamDate($employee_id, $new_date) {
    global $pdo;
    
    $sql = "UPDATE employees SET medical_exam_expiry = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$new_date, $employee_id]);
}
?>