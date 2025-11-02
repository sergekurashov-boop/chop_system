<?php
// modules/requests/includes/request_functions.php

function getSecurityRequests($filters = []) {
    global $pdo;
    
    $sql = "SELECT sr.*, c.name as client_name, ot.name as object_type_name 
            FROM security_requests sr 
            LEFT JOIN clients c ON sr.client_id = c.id 
            LEFT JOIN object_types ot ON sr.object_type_id = ot.id 
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filters['status'])) {
        $sql .= " AND sr.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['client_id'])) {
        $sql .= " AND sr.client_id = ?";
        $params[] = $filters['client_id'];
    }
    
    $sql .= " ORDER BY sr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function calculateRequestTotal($request_id) {
    global $pdo;
    
    $sql = "SELECT SUM(total_price) as total FROM request_services WHERE request_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['total'] ?? 0;
}

function getRequestPaymentsTotal($request_id) {
    global $pdo;
    
    $sql = "SELECT SUM(amount) as paid_total FROM payments WHERE request_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$request_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['paid_total'] ?? 0;
}
?>