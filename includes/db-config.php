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
 * Global variable to store debug info for login attempts
 * 
 * ⚠️ WARNING: This debug output is for TEMPORARY TROUBLESHOOTING only!
 * It exposes sensitive information like usernames, password hashes, and DB details.
 * REMOVE or DISABLE this debug code before deploying to production!
 */
$GLOBALS['wds_login_debug'] = [];

/**
 * Add a debug message to the global debug array
 * @param string $message Debug message
 * @param mixed $data Optional data to include
 */
function addLoginDebug($message, $data = null) {
    $entry = ['message' => $message, 'time' => microtime(true)];
    if ($data !== null) {
        $entry['data'] = $data;
    }
    $GLOBALS['wds_login_debug'][] = $entry;
}

/**
 * Get all debug messages
 * @return array Debug messages
 */
function getLoginDebug() {
    return $GLOBALS['wds_login_debug'];
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
    addLoginDebug("=== LOGIN ATTEMPT STARTED ===");
    addLoginDebug("Username provided", $username);
    addLoginDebug("Password length", strlen($password));
    
    addLoginDebug("Attempting database connection...");
    addLoginDebug("DB_HOST", DB_HOST);
    addLoginDebug("DB_NAME", DB_NAME);
    addLoginDebug("DB_USER", DB_USER);
    
    $db = getDatabaseConnection();
    if (!$db) {
        addLoginDebug("ERROR: Database connection failed!");
        error_log("WDS Login: Database connection failed");
        return false;
    }
    addLoginDebug("Database connection successful");

    try {
        addLoginDebug("Resolving users table...");
        $table = resolveUsersTable($db);
        addLoginDebug("Users table resolved", $table);
        
        $columnList = "user_id, users_login, users_passwd, users_role";
        $hasPassHash = tableHasPassHash($db, $table);
        addLoginDebug("Table has users_pass_hash column", $hasPassHash ? "YES" : "NO");
        
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }
        addLoginDebug("Column list for query", $columnList);

        $query = "SELECT {$columnList} FROM {$table} WHERE users_login = ? LIMIT 1";
        addLoginDebug("SQL Query", $query);
        addLoginDebug("Query parameter (username)", $username);
        
        $stmt = $db->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        addLoginDebug("Query executed successfully");
        addLoginDebug("User found", $user ? "YES" : "NO");
        
        if (!$user) {
            addLoginDebug("ERROR: User not found in database");
            error_log("WDS Login: User not found: " . $username);
            return false;
        }
        
        // Log user data (excluding password hash for security, but showing structure)
        $userDebug = [
            'user_id' => $user['user_id'] ?? 'N/A',
            'users_login' => $user['users_login'] ?? 'N/A',
            'users_role' => $user['users_role'] ?? 'N/A',
            'users_passwd_present' => !empty($user['users_passwd']) ? "YES (length: " . strlen($user['users_passwd']) . ")" : "NO",
            'users_pass_hash_present' => isset($user['users_pass_hash']) ? (!empty($user['users_pass_hash']) ? "YES (length: " . strlen($user['users_pass_hash']) . ")" : "EMPTY") : "N/A"
        ];
        addLoginDebug("User record details", $userDebug);

        // Password verification - same logic as GSP billing module
        // Try modern password_hash first, then fall back to legacy md5
        $passwordOk = false;
        addLoginDebug("=== PASSWORD VERIFICATION ===");
        
        if ($hasPassHash && !empty($user['users_pass_hash'])) {
            addLoginDebug("Trying modern password_verify with users_pass_hash...");
            $passwordOk = password_verify($password, $user['users_pass_hash']);
            addLoginDebug("password_verify result", $passwordOk ? "MATCH" : "NO MATCH");
        } else {
            addLoginDebug("Skipping password_verify (no pass_hash column or empty value)");
        }
        
        if (!$passwordOk && !empty($user['users_passwd'])) {
            // Legacy MD5 password check - required for GSP/OGP compatibility
            // Note: MD5 is weak, but necessary for legacy systems. Modern logins
            // should use users_pass_hash with password_verify() instead.
            addLoginDebug("Trying legacy MD5 password check...");
            $inputMd5 = md5($password);
            $storedMd5 = $user['users_passwd'];
            addLoginDebug("Input password MD5", $inputMd5);
            addLoginDebug("Stored password MD5", $storedMd5);
            $passwordOk = ($inputMd5 === $storedMd5);
            addLoginDebug("MD5 comparison result", $passwordOk ? "MATCH" : "NO MATCH");
        }
        
        if (!$passwordOk) {
            addLoginDebug("ERROR: Password verification failed - no method matched");
            error_log("WDS Login: Password verification failed for: " . $username);
            return false;
        }
        addLoginDebug("Password verification SUCCESS");

        // Determine role - use users_role if available, default to 'user'
        $userRole = !empty($user['users_role']) ? strtolower($user['users_role']) : 'user';
        addLoginDebug("=== ROLE CHECK ===");
        addLoginDebug("Raw users_role value", $user['users_role'] ?? 'NULL/EMPTY');
        addLoginDebug("Normalized role", $userRole);
        
        // For WDS staff login, require admin role
        // GSP uses 'admin' role for admin users
        if ($userRole !== 'admin') {
            addLoginDebug("ERROR: User does not have admin role (required: 'admin', got: '" . $userRole . "')");
            error_log("WDS Login: User " . $username . " does not have admin role (role: " . $userRole . ")");
            return false;
        }
        addLoginDebug("Role check SUCCESS - user is admin");

        addLoginDebug("=== LOGIN SUCCESS ===");
        return [
            'username' => $user['users_login'],
            'role' => 'admin',
            'login_time' => time()
        ];
    } catch (Throwable $e) {
        addLoginDebug("EXCEPTION: " . $e->getMessage());
        addLoginDebug("Exception trace", $e->getTraceAsString());
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
