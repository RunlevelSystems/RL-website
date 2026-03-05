// Developed by World Domination Software LLC
<?php
// Start session to access session variables
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page with logout message
header('Location: index.php?msg=logged_out');
exit;
?>
