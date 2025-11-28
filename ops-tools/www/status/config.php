<?php
// CONFIG
$DB_HOST = '127.0.0.1'; $DB_PORT = 3306; $DB_NAME = 'peer_status'; $DB_USER = 'localuser'; $DB_PASS = 'CHANGE_ME_TO_.password_CONTENTS';
// END CONFIG
function db() { global $DB_HOST,$DB_PORT,$DB_NAME,$DB_USER,$DB_PASS;
  $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC ]);
  return $pdo;
}
