<?php
// includes/dashboard_functions.php

function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Активные заявки
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM security_requests WHERE status = 'active'");
    $stats['active_requests'] = $stmt->fetch()['count'];
    
    // Всего сотрудников
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE is_active = 1");
    $stats['total_employees'] = $stmt->fetch()['count'];
    
    // Просроченные медосмотры
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM employees WHERE medical_exam_expiry < CURDATE()");
    $stats['expired_medical'] = $stmt->fetch()['count'];
    
    // Последние заявки
    $stmt = $pdo->query("SELECT sr.*, c.name as client_name FROM security_requests sr LEFT JOIN clients c ON sr.client_id = c.id ORDER BY sr.created_at DESC LIMIT 5");
    $stats['recent_requests'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
}
?>