<?php
require __DIR__ . '/config.php'; $pdo = db();
$host = $_GET['host'] ?? ''; $range = $_GET['range'] ?? '60min';
$valid = ['60min','24h','30d','6mo']; if (!in_array($range, $valid)) $range = '60min';
$interval = [ '60min' => 'INTERVAL 60 MINUTE','24h'=>'INTERVAL 24 HOUR','30d'=>'INTERVAL 30 DAY','6mo'=>'INTERVAL 6 MONTH'][$range];
$stmt = $pdo->prepare("SELECT ts, cpu_used_pct, mem_used_bytes, mem_total_bytes, disk_used_bytes, disk_total_bytes FROM metrics WHERE hostname = :h AND ts >= NOW() - $interval ORDER BY ts ASC");
$stmt->execute([':h'=>$host]); $data=$stmt->fetchAll(); header('Content-Type: application/json'); echo json_encode($data);
