<?php
/**
 * Alberione Advogados — Admin Logout
 * admin/logout.php
 */
require_once __DIR__ . '/../config/config.php';

if (is_logged_in()) {
    log_auth('logout', (int)$_SESSION['admin_id']);
}

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $p['path'], $p['domain'], $p['secure'], $p['httponly']
    );
}
session_destroy();

header('Location: /admin/login.php');
exit;
