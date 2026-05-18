<?php
/**
 * Database Configuration for Staff Authentication
 * Connects to the GSP panel database for admin login verification.
 * Authentication logic mirrors the GSP panel (GameServerPanel/GSP index.php):
 *   md5($password) == $stored_users_passwd, role 'admin' required.
 * Uses mysqli for database operations.
 */

// Prevent direct access
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// Base path + URL helpers
require_once __DIR__ . '/config.php';

// Database Configuration (pulls from the panel DB)
define('DB_HOST', 'mysql.iaregamer.com');
define('DB_NAME', 'panel');
define('DB_USER', 'remoteuser');
define('DB_PASS', 'Pkloyn7yvpht!');
define('DB_CHARSET', 'utf8mb4');

// Table prefix - must match the panel's includes/config.inc.php $table_prefix setting.
// Default is 'gsp_'; change here if the panel was installed with a different prefix.
define('DB_TABLE_PREFIX', 'gsp_');

// Debug flag: set to true temporarily to write detailed auth steps to the PHP error log.
// Must be false in production. Remove or leave false once the issue is diagnosed.
define('WDS_DEBUG_AUTH', false);

// Allowed user table names are derived from the configured prefix plus the legacy OGP
// fallback so that databases migrated from OGP still work.  Never hardcode table names
// in queries; always use resolveUsersTable() instead.
function _buildAllowedUserTables() {
    $prefix = DB_TABLE_PREFIX;
    $tables = [$prefix . 'users'];
    // Legacy OGP fallback: include 'ogp_users' only when the configured prefix differs
    if ($prefix !== 'ogp_') {
        $tables[] = 'ogp_users';
    }
    return array_unique($tables);
}

/**
 * Validate that a table name is in the allowed list (derived from DB_TABLE_PREFIX)
 * @param string $table Table name to validate
 * @return bool True if valid, false otherwise
 */
function isValidUserTable($table) {
    return in_array($table, _buildAllowedUserTables(), true);
}

/**
 * Resolve which users table is available.
 * Checks the configured DB_TABLE_PREFIX first (e.g. gsp_users), then falls back to
 * the legacy ogp_users table for databases migrated from an unmodified OGP install.
 * @param mysqli $db
 * @return string Table name
 * @throws RuntimeException if no matching table is found
 */
function resolveUsersTable(mysqli $db) {
    static $tableName = null;
    if ($tableName !== null) {
        return $tableName;
    }

    $candidates = _buildAllowedUserTables();
    addLoginDebug("Checking candidate tables", implode(', ', $candidates));

    foreach ($candidates as $candidate) {
        // Defensive: only allow table names consisting of safe characters
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $candidate)) {
            continue;
        }
        $result = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $candidate) . "'");
        if ($result && mysqli_num_rows($result) > 0) {
            mysqli_free_result($result);
            $tableName = $candidate;
            addLoginDebug("Users table found", $tableName);
            return $tableName;
        }
        if ($result) {
            mysqli_free_result($result);
        }
        addLoginDebug("Table not found", $candidate);
    }

    throw new RuntimeException(
        'Unable to locate users table with prefix "' . DB_TABLE_PREFIX . '" in the panel database. '
        . 'Checked: ' . implode(', ', $candidates)
    );
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
 * Debug hook for login flow.
 * When WDS_DEBUG_AUTH is true, writes messages to the PHP error log so the
 * failure point can be identified without exposing data to end users.
 * Set WDS_DEBUG_AUTH to false (or remove this block) once the issue is resolved.
 * @param string $message
 * @param mixed  $data  Optional value to append
 */
function addLoginDebug($message, $data = null) {
    if (!defined('WDS_DEBUG_AUTH') || !WDS_DEBUG_AUTH) {
        return;
    }
    $entry = '[WDS Auth] ' . $message;
    if ($data !== null) {
        $entry .= ': ' . (is_array($data) ? json_encode($data) : (string)$data);
    }
    error_log($entry);
}

/**
 * Return debug messages (always empty; logging goes to error_log instead).
 */
function getLoginDebug() {
    return [];
}

/**
 * Verify admin user credentials against the panel database users table.
 * Authentication logic mirrors the GSP panel (Panel/index.php):
 *   md5($password) == $row['users_passwd']  AND  $row['users_role'] == 'admin'
 * resolveUsersTable() dynamically locates the table using DB_TABLE_PREFIX.
 * @param string $username The username to check
 * @param string $password The plain text password
 * @return array|false User data array on success, false on failure
 */
function verifyAdminLogin($username, $password) {
    addLoginDebug("=== LOGIN ATTEMPT ===");
    addLoginDebug("Username", $username);
    addLoginDebug("Password length", strlen($password));
    addLoginDebug("DB_HOST", DB_HOST);
    addLoginDebug("DB_NAME", DB_NAME);
    addLoginDebug("DB_USER", DB_USER);
    addLoginDebug("DB_TABLE_PREFIX", DB_TABLE_PREFIX);

    $db = getDatabaseConnection();
    if (!$db) {
        addLoginDebug("ERROR: Database connection failed");
        error_log("WDS Login: Database connection failed for user: " . $username);
        return false;
    }
    addLoginDebug("Database connected");

    try {
        $table = resolveUsersTable($db);
        addLoginDebug("Using table", $table);

        // Defense-in-depth: confirm resolved name is in the allowed list
        if (!isValidUserTable($table)) {
            throw new RuntimeException('Resolved table name is not in the allowed list: ' . $table);
        }

        $columnList = "user_id, users_login, users_passwd, users_role";
        $hasPassHash = tableHasPassHash($db, $table);
        addLoginDebug("users_pass_hash column present", $hasPassHash ? "YES" : "NO");
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }

        $query = "SELECT {$columnList} FROM `{$table}` WHERE users_login = ? LIMIT 1";
        addLoginDebug("Query", $query);

        $stmt = mysqli_prepare($db, $query);
        if (!$stmt) {
            throw new RuntimeException("Failed to prepare statement: " . mysqli_error($db));
        }
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result === false) {
            throw new RuntimeException("mysqli_stmt_get_result failed - mysqlnd extension may be missing");
        }
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        addLoginDebug("User found", $user ? "YES" : "NO");
        if (!$user) {
            error_log("WDS Login: User not found: " . $username);
            return false;
        }
        addLoginDebug("users_role", $user['users_role'] ?? 'NULL');
        addLoginDebug("users_passwd length", strlen($user['users_passwd'] ?? ''));

        // ---------------------------------------------------------------
        // Password verification - identical to GSP panel (Panel/index.php):
        //   isset($userInfo['users_passwd']) && md5($password) == $userInfo['users_passwd']
        // Try modern password_hash first (users_pass_hash column), then MD5 fallback.
        // ---------------------------------------------------------------
        $passwordOk = false;

        if ($hasPassHash && !empty($user['users_pass_hash'])) {
            addLoginDebug("Trying password_verify against users_pass_hash");
            $passwordOk = password_verify($password, $user['users_pass_hash']);
            addLoginDebug("password_verify result", $passwordOk ? "MATCH" : "NO MATCH");
        }

        if (!$passwordOk && !empty($user['users_passwd'])) {
            // Legacy MD5 check - required for GSP/OGP compatibility.
            // The panel stores passwords as MD5($password) in users_passwd.
            addLoginDebug("Trying MD5 check against users_passwd");
            $inputMd5  = md5($password);
            $storedMd5 = $user['users_passwd'];
            $passwordOk = ($inputMd5 == $storedMd5);
            addLoginDebug("MD5 result", $passwordOk ? "MATCH" : "NO MATCH");
            if (!$passwordOk) {
                addLoginDebug("MD5 of input", $inputMd5);
                addLoginDebug("Stored hash length", strlen($storedMd5));
            }
        }

        if (!$passwordOk) {
            error_log("WDS Login: Password verification failed for: " . $username);
            return false;
        }
        addLoginDebug("Password OK");

        // Role check: GSP panel requires users_role === 'admin'
        $userRole = !empty($user['users_role']) ? strtolower(trim($user['users_role'])) : 'user';
        addLoginDebug("Normalized role", $userRole);
        if ($userRole !== 'admin') {
            error_log("WDS Login: User '" . $username . "' does not have admin role (role: " . $userRole . ")");
            return false;
        }
        addLoginDebug("=== LOGIN SUCCESS ===");

        return [
            'username'   => $user['users_login'],
            'role'       => 'admin',
            'login_time' => time(),
        ];

    } catch (Throwable $e) {
        addLoginDebug("EXCEPTION: " . $e->getMessage());
        error_log("WDS Login verification exception: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is currently logged in as admin
 * @return bool True if logged in as admin, false otherwise
 */
function isLoggedInAdmin() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return isset($_SESSION['wds_admin_user']) &&
           isset($_SESSION['wds_admin_role']) &&
           $_SESSION['wds_admin_role'] === 'admin';
}

/**
 * Require admin login - redirect to login page if not authenticated.
 * Uses getBasePath() to produce a correct relative path regardless of whether
 * the calling page is at the site root or inside a subdirectory (e.g. staff/).
 */
function requireAdminLogin() {
    if (!isLoggedInAdmin()) {
        $basePath = function_exists('getBasePath') ? getBasePath() : '';
        $redirect  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'staff-info.php';
        header('Location: ' . $basePath . 'login.php?redirect=' . urlencode($redirect));
        exit;
    }
}

?>
