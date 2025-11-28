<?php
/**
 * Database Configuration for Staff Authentication
 * Connects to the OGP Users database for admin login verification
 */

// Prevent direct access
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// Database Configuration (pulls from the panel DB)
define('DB_HOST', 'mysql.iaregamer.com');
define('DB_NAME', 'panel');
define('DB_USER', 'remoteuser');
define('DB_PASS', 'Pkloyn7yvpht!');
define('DB_CHARSET', 'utf8mb4');

/**
 * Resolve which users table is available (gsp_users preferred, ogp_users fallback)
 * @param PDO $db
 * @return string Table name
 */
function resolveUsersTable(PDO $db) {
    static $tableName = null;
    if ($tableName !== null) {
        return $tableName;
    }

    $candidates = ['gsp_users', 'ogp_users'];
    foreach ($candidates as $candidate) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$candidate]);
        if ($stmt->fetchColumn()) {
            $tableName = $candidate;
            return $tableName;
        }
    }

    throw new RuntimeException('Unable to locate gsp_users or ogp_users table in the panel database.');
}

/**
 * Determine whether the users table supports the modern password hash column
 * @param PDO $db
 * @param string $table
 * @return bool
 */
function tableHasPassHash(PDO $db, $table) {
    $stmt = $db->prepare("SHOW COLUMNS FROM {$table} LIKE 'users_pass_hash'");
    $stmt->execute();
    return (bool) $stmt->fetchColumn();
}

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
 * Verify admin user credentials against panel database users table
 * Uses resolveUsersTable() to find gsp_users or ogp_users table dynamically.
 * Follows the same authentication approach as GSP billing module.
 * @param string $username The username to check
 * @param string $password The plain text password
 * @return array|false User data array or false on failure
 */
function verifyAdminLogin($username, $password) {
    $db = getDatabaseConnection();
    if (!$db) {
        error_log("WDS Login: Database connection failed");
        return false;
    }

    try {
        $table = resolveUsersTable($db);
        $columnList = "user_id, users_login, users_passwd, users_role";
        $hasPassHash = tableHasPassHash($db, $table);
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }

        $stmt = $db->prepare("SELECT {$columnList} FROM {$table} WHERE users_login = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user) {
            error_log("WDS Login: User not found: " . $username);
            return false;
        }

        // Password verification - same logic as GSP billing module
        // Try modern password_hash first, then fall back to legacy md5
        $passwordOk = false;
        if ($hasPassHash && !empty($user['users_pass_hash'])) {
            $passwordOk = password_verify($password, $user['users_pass_hash']);
        }
        if (!$passwordOk && !empty($user['users_passwd'])) {
            // Legacy MD5 password check - required for GSP/OGP compatibility
            // Note: MD5 is weak, but necessary for legacy systems. Modern logins
            // should use users_pass_hash with password_verify() instead.
            $passwordOk = (md5($password) === $user['users_passwd']);
        }
        if (!$passwordOk) {
            error_log("WDS Login: Password verification failed for: " . $username);
            return false;
        }

        // Determine role - use users_role if available, default to 'user'
        $userRole = !empty($user['users_role']) ? strtolower($user['users_role']) : 'user';
        
        // For WDS staff login, require admin role
        // GSP uses 'admin' role for admin users
        if ($userRole !== 'admin') {
            error_log("WDS Login: User " . $username . " does not have admin role (role: " . $userRole . ")");
            return false;
        }

        return [
            'username' => $user['users_login'],
            'role' => 'admin',
            'login_time' => time()
        ];
    } catch (Throwable $e) {
        error_log("WDS Login verification failed: " . $e->getMessage());
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
