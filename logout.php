<?php
session_start();

// Уничтожаем сессию
$_SESSION = array();

// Если используется куки сессии, удаляем его
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Редирект на страницу логина
header("Location: login.php");
exit;
?>