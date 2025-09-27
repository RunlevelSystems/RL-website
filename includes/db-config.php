<?php
/**
 * Database Configuration for Staff Authentication
 * Connects to the OGP Users database for admin login verification
 */

// Prevent direct access
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// Database Configuration
define('DB_HOST', 'mysql.iaregamer.com');
define('DB_NAME', 'panel');
define('DB_USER', 'remoteuser');
define('DB_PASS', 'Pkloyn7yvpht!');
define('DB_CHARSET', 'utf8mb4');

/**
 * Create PDO database connection
 * @return PDO|false Database connection or false on failure
 */
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log error in production, show for development
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify admin user credentials against ogp_users table
 * @param string $username The username to check
 * @param string $password The plain text password
 * @return array|false User data array or false on failure
 */
function verifyAdminLogin($username, $password) {
    $db = getDatabaseConnection();
    if (!$db) {
        return false;
    }
    
    try {
        // Hash the password using MD5 as required by the existing system
        $hashedPassword = md5($password);
        
        $stmt = $db->prepare("
            SELECT users_login, users_password, users_role 
            FROM ogp_users 
            WHERE users_login = ? 
            AND users_password = ? 
            AND users_role = 'admin'
        ");
        
        $stmt->execute([$username, $hashedPassword]);
        $user = $stmt->fetch();
        
        if ($user) {
            return [
                'username' => $user['users_login'],
                'role' => $user['users_role'],
                'login_time' => time()
            ];
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Login verification failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is currently logged in as admin
 * @return bool True if logged in as admin, false otherwise
 */
function isLoggedInAdmin() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    return isset($_SESSION['wds_admin_user']) && 
           isset($_SESSION['wds_admin_role']) && 
           $_SESSION['wds_admin_role'] === 'admin';
}

/**
 * Require admin login - redirect to login page if not authenticated
 */
function requireAdminLogin() {
    if (!isLoggedInAdmin()) {
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

?>
