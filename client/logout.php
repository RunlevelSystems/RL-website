<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalClientLogout();
header('Location: /client/login.php?msg=logged_out');
exit;
