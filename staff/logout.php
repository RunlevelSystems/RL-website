<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

portalStaffLogout();
header('Location: /staff/login.php?msg=logged_out');
exit;
