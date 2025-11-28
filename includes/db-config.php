<?php
/**
 * Database Configuration for Staff Authentication
 * Connects to the OGP Users database for admin login verification
 * Uses mysqli for database operations
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

// Allowed table names for security validation
define('ALLOWED_USER_TABLES', ['gsp_users', 'ogp_users']);

/**
 * Validate that a table name is in the allowed list
 * @param string $table Table name to validate
 * @return bool True if valid, false otherwise
 */
function isValidUserTable($table) {
    return in_array($table, ALLOWED_USER_TABLES, true);
}

/**
 * Resolve which users table is available (gsp_users preferred, ogp_users fallback)
 * @param mysqli $db
 * @return string Table name
 */
function resolveUsersTable(mysqli $db) {
    static $tableName = null;
    if ($tableName !== null) {
        return $tableName;
    }

    foreach (ALLOWED_USER_TABLES as $candidate) {
        $result = mysqli_query($db, "SHOW TABLES LIKE '" . $candidate . "'");
        if ($result && mysqli_num_rows($result) > 0) {
            mysqli_free_result($result);
            $tableName = $candidate;
            return $tableName;
        }
        if ($result) {
            mysqli_free_result($result);
        }
    }

    throw new RuntimeException('Unable to locate gsp_users or ogp_users table in the panel database.');
}

/**
 * Determine whether the users table supports the modern password hash column
 * @param mysqli $db
 * @param string $table
 * @return bool
 */
function tableHasPassHash(mysqli $db, $table) {
    if (!isValidUserTable($table)) {
        throw new RuntimeException('Invalid table name provided');
    }
    $result = mysqli_query($db, "SHOW COLUMNS FROM `" . $table . "` LIKE 'users_pass_hash'");
    $hasColumn = ($result && mysqli_num_rows($result) > 0);
    if ($result) {
        mysqli_free_result($result);
    }
    return $hasColumn;
}

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
        
        // Validate table name for security (defense in depth)
        if (!isValidUserTable($table)) {
            throw new RuntimeException('Invalid table name resolved');
        }
        
        $columnList = "user_id, users_login, users_passwd, users_role";
        $hasPassHash = tableHasPassHash($db, $table);
        addLoginDebug("Table has users_pass_hash column", $hasPassHash ? "YES" : "NO");
        
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }
        addLoginDebug("Column list for query", $columnList);

        $query = "SELECT {$columnList} FROM `{$table}` WHERE users_login = ? LIMIT 1";
        addLoginDebug("SQL Query", $query);
        addLoginDebug("Query parameter (username)", $username);
        
        $stmt = mysqli_prepare($db, $query);
        if (!$stmt) {
            throw new RuntimeException("Failed to prepare statement: " . mysqli_error($db));
        }
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
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
