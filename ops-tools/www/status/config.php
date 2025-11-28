<?php
/**
 * Status Pages Configuration
 * 
 * Database: server_status (for new pages) or peer_status (legacy)
 * 
 * To switch between core and core-dr for DR failover:
 *   1. Change DB_HOST to core.iaregamer.com or core-dr.iaregamer.com
 *   2. DNS will handle the failover when records are updated
 * 
 * For local access on the MySQL host itself, use 127.0.0.1 with localuser
 * For remote access from other servers, use remoteuser with the server's IP
 */

// CONFIG - Update these values for your environment
$DB_HOST = 'core.iaregamer.com';  // Primary: core.iaregamer.com | DR: core-dr.iaregamer.com
$DB_PORT = 3306;
$DB_NAME = 'server_status';       // New unified database name
$DB_USER = 'remoteuser';          // Use 'localuser' for localhost, 'remoteuser' for remote access

// SECURITY: Read password from file instead of hardcoding
// On deployment, create /home/gameserver/tools/.password with the MySQL password
$DB_PASS = '';
$passwordFile = '/home/gameserver/tools/.password';
if (file_exists($passwordFile) && is_readable($passwordFile)) {
    $DB_PASS = trim(file_get_contents($passwordFile));
}
if (empty($DB_PASS)) {
    // Fallback for development - CHANGE IN PRODUCTION
    $DB_PASS = 'PLACEHOLDER_CONFIGURE_PASSWORD_FILE';
}

// Optional: Override hostname detection for public.php
// define('SERVER_HOSTNAME', 'core');

// END CONFIG

function db() {
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    
    if ($DB_PASS === 'PLACEHOLDER_CONFIGURE_PASSWORD_FILE') {
        throw new Exception('Database password not configured. Create /home/gameserver/tools/.password with the MySQL password.');
    }
    
    $dsn = "mysql:host=$DB_HOST;port=$DB_PORT;dbname=$DB_NAME;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ];
    
    return new PDO($dsn, $DB_USER, $DB_PASS, $options);
}
