<?php
/**
 * Database Configuration for Staff Authentication - EXAMPLE FILE
 *
 * Copy this file to db-config.php and fill in your actual credentials.
 * DO NOT commit db-config.php to version control (it is in .gitignore).
 *
 * Authentication mirrors the GSP panel (Panel/index.php):
 *   md5($password) == $row['users_passwd']  AND  $row['users_role'] == 'admin'
 */

// Prevent direct access
if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// Database credentials - must match the panel's includes/config.inc.php values
define('DB_HOST',    'your_mysql_host');      // e.g. 'mysql.example.com' or 'localhost'
define('DB_NAME',    'your_database_name');   // e.g. 'panel' or 'gsp_panel'
define('DB_USER',    'your_database_user');
define('DB_PASS',    'your_database_pass');
define('DB_CHARSET', 'utf8mb4');

// Table prefix - must match $table_prefix in the panel's includes/config.inc.php.
// Default for GameServerPanel installs is 'gsp_', giving table name 'gsp_users'.
// Change to 'ogp_' if the panel was installed from a stock OGP setup.
define('DB_TABLE_PREFIX', 'gsp_');

// Debug flag: set to true temporarily to write detailed auth steps to the PHP error log.
// Always false in production.
define('WDS_DEBUG_AUTH', false);

// Base path + URL helpers (provided by includes/config.php, included by db-config.php)
require_once __DIR__ . '/config.php';

// ----- Helper functions -----

function _buildAllowedUserTables() {
    $prefix = DB_TABLE_PREFIX;
    $tables = [$prefix . 'users'];
    if ($prefix !== 'ogp_') {
        $tables[] = 'ogp_users';
    }
    return array_unique($tables);
}

function isValidUserTable($table) {
    return in_array($table, _buildAllowedUserTables(), true);
}

function addLoginDebug($message, $data = null) {
    if (!defined('WDS_DEBUG_AUTH') || !WDS_DEBUG_AUTH) {
        return;
    }
    $entry = '[Runlevel Auth] ' . $message;
    if ($data !== null) {
        $entry .= ': ' . (is_array($data) ? json_encode($data) : (string)$data);
    }
    error_log($entry);
}

function getLoginDebug() {
    return [];
}

function hasStaffAccessRole($role) {
    return in_array($role, ['admin', 'staff'], true);
}

function resolveUsersTable(mysqli $db) {
    static $tableName = null;
    if ($tableName !== null) {
        return $tableName;
    }
    $candidates = _buildAllowedUserTables();
    foreach ($candidates as $candidate) {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $candidate)) {
            continue;
        }
        $result = mysqli_query($db, "SHOW TABLES LIKE '" . mysqli_real_escape_string($db, $candidate) . "'");
        if ($result && mysqli_num_rows($result) > 0) {
            mysqli_free_result($result);
            $tableName = $candidate;
            return $tableName;
        }
        if ($result) {
            mysqli_free_result($result);
        }
    }
    throw new RuntimeException(
        'Unable to locate users table with prefix "' . DB_TABLE_PREFIX . '". Checked: ' . implode(', ', $candidates)
    );
}

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

function getDatabaseConnection() {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn) {
            error_log("Database connection failed: " . mysqli_connect_error());
            return false;
        }
        mysqli_set_charset($conn, DB_CHARSET);
        return $conn;
    } catch (mysqli_sql_exception $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify admin credentials against the panel database.
 * Auth flow:
 * 1) Find user by users_login from `{$prefix}users`.
 * 2) Verify password against users_passwd (MD5 fallback required for legacy panel data).
 * 3) Authorize by users_role (admin/staff only).
 *
 * Failure classification:
 * - incorrect_login
 * - no_authorization
 */
function verifyAdminLogin($username, $password, &$failureReason = null) {
    $failureReason = null;
    $db = getDatabaseConnection();
    if (!$db) {
        error_log("Runlevel Login: Database connection failed for user: " . $username);
        $failureReason = 'incorrect_login';
        return false;
    }
    try {
        $table      = resolveUsersTable($db);
        if (!isValidUserTable($table)) {
            throw new RuntimeException('Resolved table name is not in the allowed list: ' . $table);
        }
        $columnList = "user_id, users_login, users_passwd, users_role";
        $hasPassHash = tableHasPassHash($db, $table);
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }
        $query = "SELECT {$columnList} FROM `{$table}` WHERE users_login = ? LIMIT 1";
        $stmt  = mysqli_prepare($db, $query);
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
        if (!$user) {
            error_log("Runlevel Login: User not found: " . $username);
            $failureReason = 'incorrect_login';
            return false;
        }
        $passwordOk = false;
        if ($hasPassHash && !empty($user['users_pass_hash'])) {
            $passwordOk = password_verify($password, $user['users_pass_hash']);
        }
        if (!$passwordOk && !empty($user['users_passwd'])) {
            $passwordOk = (md5($password) == $user['users_passwd']);
        }
        if (!$passwordOk) {
            error_log("Runlevel Login: Password verification failed for: " . $username);
            $failureReason = 'incorrect_login';
            return false;
        }
        $userRole = !empty($user['users_role']) ? strtolower(trim($user['users_role'])) : 'user';
        if (!hasStaffAccessRole($userRole)) {
            error_log("Runlevel Login: User '" . $username . "' is authenticated but unauthorized for staff area (users_role: " . $userRole . ")");
            $failureReason = 'no_authorization';
            return false;
        }
        return [
            'username'   => $user['users_login'],
            'role'       => $userRole,
            'login_time' => time(),
        ];
    } catch (Throwable $e) {
        error_log("Runlevel Login exception: " . $e->getMessage());
        $failureReason = 'incorrect_login';
        return false;
    }
}

/**
 * Check if user is currently logged in as admin
 */
function isLoggedInAdmin() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return isset($_SESSION['wds_admin_user']) &&
           isset($_SESSION['wds_admin_role']) &&
           hasStaffAccessRole(strtolower(trim((string)$_SESSION['wds_admin_role'])));
}

/**
 * Require admin login - redirect to login page if not authenticated
 */
function requireAdminLogin() {
    if (!isLoggedInAdmin()) {
        $basePath = function_exists('getBasePath') ? getBasePath() : '';
        $redirect  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'staff-info.php';
        header('Location: ' . $basePath . 'login.php?redirect=' . urlencode($redirect));
        exit;
    }
}
