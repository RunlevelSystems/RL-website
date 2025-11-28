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
        $table = resolveUsersTable($db);
        $columnList = "user_id, users_login, users_passwd, users_role, users_group";
        $hasPassHash = tableHasPassHash($db, $table);
        if ($hasPassHash) {
            $columnList .= ", users_pass_hash";
        }

        $stmt = $db->prepare("SELECT {$columnList} FROM {$table} WHERE users_login = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if (!$user) {
            return false;
        }

        $isAdmin = false;
        if (isset($user['users_role']) && strtolower($user['users_role']) === 'admin') {
            $isAdmin = true;
        } elseif (!empty($user['users_group']) && stripos($user['users_group'], 'admin') !== false) {
            $isAdmin = true;
        }
        if (!$isAdmin) {
            return false;
        }

        $passwordOk = false;
        if ($hasPassHash && !empty($user['users_pass_hash'])) {
            $passwordOk = password_verify($password, $user['users_pass_hash']);
        }
        if (!$passwordOk) {
            $passwordOk = hash_equals($user['users_passwd'], md5($password));
        }
        if (!$passwordOk) {
            return false;
        }

        return [
            'username' => $user['users_login'],
            'role' => 'admin',
            'login_time' => time()
        ];
    } catch (Throwable $e) {
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
