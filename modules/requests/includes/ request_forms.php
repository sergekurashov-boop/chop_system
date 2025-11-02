<?php
// modules/requests/includes/request_forms.php

function renderClientSelect($selectedId = null) {
    global $pdo;
    
    $html = '<select name="client_id" class="form-control" required>';
    $html .= '<option value="">-- Выберите клиента --</option>';
    
    // Временная заглушка - всегда показываем тестового клиента
    $html .= '<option value="1" selected>ООО "Ромашка"</option>';
    
    $html .= '</select>';
    return $html;
}

function renderObjectTypeSelect($selectedId = null) {
    global $pdo;
    
    $html = '<select name="object_type_id" class="form-control" required>';
    $html .= '<option value="">-- Выберите тип объекта --</option>';
    
    // Временные варианты
    $html .= '<option value="1">Территория</option>';
    $html .= '<option value="2">Здание</option>';
    $html .= '<option value="3">Комплекс</option>';
    $html .= '<option value="4">Физическое лицо</option>';
    
    $html .= '</select>';
    return $html;
}
?>