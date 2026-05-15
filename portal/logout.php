<?php
session_start();

unset($_SESSION['wds_client_id'], $_SESSION['wds_client_user'], $_SESSION['wds_client_name'], $_SESSION['wds_client_folder']);

header('Location: index.php');
exit;
