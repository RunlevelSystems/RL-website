<?php
// Unified logout — clears all portal sessions and redirects to login page.
session_start();
define('WDS_SYSTEM', true);
require_once 'includes/portal-helpers.php';

portalLogout();

// Destroy the session cookie if it exists
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

session_destroy();

header('Location: /login.php?msg=logged_out');
exit;

