// Developed by World Domination Software LLC
<?php
/**
 * Database Configuration for Staff Authentication - EXAMPLE FILE
 * 
 * Copy this file to db-config.php and update with your actual credentials
 * DO NOT commit db-config.php to version control!
 * Uses mysqli for database operations
 */

// Prevent direct access
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// Database Configuration
// Replace these values with your actual database credentials
define('DB_HOST', 'your_mysql_host');      // e.g., 'localhost' or 'mysql.example.com'
define('DB_NAME', 'your_database_name');   // e.g., 'panel'
define('DB_USER', 'your_database_user');   // e.g., 'dbuser'
define('DB_PASS', 'your_database_pass');   // Strong password
define('DB_CHARSET', 'utf8mb4');           // Character set (usually utf8mb4)

/**
 * Create mysqli database connection
 * @return mysqli|false Database connection or false on failure
 */
function getDatabaseConnection() {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if (!$conn) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return false;
        }
        
        // Set charset
        mysqli_set_charset($conn, DB_CHARSET);
        
        return $conn;
    } catch (mysqli_sql_exception $e) {
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
        
        $query = "SELECT users_login, users_passwd, users_role 
                  FROM ogp_users 
                  WHERE users_login = ? 
                  AND users_passwd = ? 
                  AND users_role = 'admin'
                  LIMIT 1";
        
        $stmt = mysqli_prepare($db, $query);
        if (!$stmt) {
            error_log("Login verification failed: " . mysqli_error($db));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPassword);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        if ($user) {
            return [
                'username' => $user['users_login'],
                'role' => $user['users_role'],
                'login_time' => time()
            ];
        }
        
        return false;
    } catch (mysqli_sql_exception $e) {
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
