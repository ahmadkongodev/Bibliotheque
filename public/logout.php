<?php
require_once __DIR__ . '/../app/helpers.php';

startSession();
$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
startSession();
setFlash('success', 'Vous avez été déconnecté.');
redirect('login.php');