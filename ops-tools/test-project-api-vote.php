// Developed by World Domination Software LLC
<?php
// Simple CLI test harness for project-api vote action

define('WDS_SYSTEM', true);

// Simulate server vars minimal
$_SERVER['REQUEST_METHOD'] = 'POST';

$_POST = [
    'slug' => 'gameserver-panel',
    'delta' => 1,
];
$_REQUEST = $_POST + ['action' => 'vote'];

require __DIR__ . '/../project-api.php';
