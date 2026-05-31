<?php
// Staff login has moved to the unified dashboard login.
// Redirect all requests to /login.php
$redirect = '';
if (isset($_GET['redirect'])) {
    $r = $_GET['redirect'];
    if (!empty($r) && preg_match('#^/#', $r) && !preg_match('#^//|^/\\\\#', $r)) {
        $redirect = '?redirect=' . urlencode($r);
    }
}
header('Location: /login.php' . $redirect);
exit;
