<?php
// modules/requests/request_functions.php
// ВСЕ функции модуля в одном файле

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
    
    $sql .= " ORDER BY sr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function renderClientSelect($selectedId = null) {
    global $pdo;
    
    $html = '<select name="client_id" class="form-control" required>';
    $html .= '<option value="">-- Выберите клиента --</option>';
    
    try {
        $stmt = $pdo->query("SELECT id, name FROM clients ORDER BY name");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($clients as $client) {
            $selected = ($client['id'] == $selectedId) ? 'selected' : '';
            $html .= "<option value='{$client['id']}' $selected>{$client['name']}</option>";
        }
    } catch (PDOException $e) {
        $html .= '<option value="">Ошибка загрузки клиентов</option>';
    }
    
    $html .= '</select>';
    return $html;
}

function renderObjectTypeSelect($selectedId = null) {
    global $pdo;
    
    $html = '<select name="object_type_id" class="form-control" required>';
    $html .= '<option value="">-- Выберите тип объекта --</option>';
    
    try {
        $stmt = $pdo->query("SELECT id, name FROM object_types ORDER BY name");
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($types as $type) {
            $selected = ($type['id'] == $selectedId) ? 'selected' : '';
            $html .= "<option value='{$type['id']}' $selected>{$type['name']}</option>";
        }
    } catch (PDOException $e) {
        $html .= '<option value="">Ошибка загрузки типов объектов</option>';
    }
    
    $html .= '</select>';
    return $html;
}
?>