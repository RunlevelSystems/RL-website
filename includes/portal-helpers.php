<?php
/**
 * Portal Helpers — Runlevel Systems Business Portal
 *
 * Provides text-file based authentication and data management for the staff
 * and client portal areas.
 *
 * TODO: Move users to database later.
 * TODO: Replace plaintext passwords with password_hash/password_verify before production.
 */

if (!defined('WDS_SYSTEM')) {
    die('Access denied');
}

// ------------------------------------------------------------------
// Data file paths (relative to this file which lives in /includes/)
// ------------------------------------------------------------------
if (!defined('PORTAL_DATA_DIR')) {
    define('PORTAL_DATA_DIR', __DIR__ . '/../data');
}
define('PORTAL_USERS_FILE',      PORTAL_DATA_DIR . '/users.json');
define('PORTAL_CLIENTS_FILE',    PORTAL_DATA_DIR . '/clients.json');
define('PORTAL_REQUESTS_FILE',   PORTAL_DATA_DIR . '/requests.json');
define('PORTAL_COMMERCIAL_FILE', PORTAL_DATA_DIR . '/commercial_requests.json');
define('PORTAL_ESTIMATES_FILE',  PORTAL_DATA_DIR . '/estimate_requests.json');

// Session keys
define('PORTAL_STAFF_SESSION',   'rls_portal_staff');
define('PORTAL_CLIENT_SESSION',  'rls_portal_client');
// Unified dashboard session key (used by /login.php and /dashboard.php)
define('PORTAL_UNIFIED_SESSION', 'rls_session');

// ------------------------------------------------------------------
// Utilities
// ------------------------------------------------------------------

function portalEnsureSession() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function portalEnsureDataDir() {
    if (!is_dir(PORTAL_DATA_DIR)) {
        mkdir(PORTAL_DATA_DIR, 0755, true);
    }
}

/**
 * Load a JSON file and return the decoded array, or [] on failure.
 */
function portalLoadJson($file) {
    if (!file_exists($file)) {
        return [];
    }
    $raw = @file_get_contents($file);
    if ($raw === false || trim($raw) === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Save an array to a JSON file.
 */
function portalSaveJson($file, $data) {
    portalEnsureDataDir();
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }
    return file_put_contents($file, $json, LOCK_EX) !== false;
}

/**
 * Sanitize HTML output.
 */
function pe($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// ------------------------------------------------------------------
// Staff user management
// ------------------------------------------------------------------

function portalLoadUsers() {
    $data = portalLoadJson(PORTAL_USERS_FILE);
    return isset($data['users']) && is_array($data['users']) ? $data['users'] : [];
}

function portalSaveUsers(array $users) {
    return portalSaveJson(PORTAL_USERS_FILE, ['users' => array_values($users)]);
}

function portalFindUserByUsername($username) {
    $username = strtolower(trim((string)$username));
    foreach (portalLoadUsers() as $user) {
        if (strtolower(trim((string)($user['username'] ?? ''))) === $username) {
            return $user;
        }
    }
    return null;
}

/**
 * Verify staff login credentials (admin and staff roles only).
 *
 * TODO: Replace plaintext password comparison with password_hash/password_verify before production.
 *       Current plaintext storage is temporary for development only.
 */
function portalVerifyStaffLogin($username, $password) {
    $user = portalFindUserByUsername($username);
    if (!$user) {
        return false;
    }
    if (($user['status'] ?? '') !== 'active') {
        return false;
    }
    // TODO: Replace plaintext passwords with password_hash/password_verify before production.
    if ($user['password'] !== $password) {
        return false;
    }
    $role = $user['role'] ?? '';
    if (!in_array($role, ['admin', 'staff'], true)) {
        return false;
    }
    return $user;
}

/**
 * Verify unified login credentials against users.json (all roles: admin, staff, client).
 *
 * TODO: Replace plaintext password comparison with password_hash/password_verify before production.
 * TODO: Move users to database later.
 */
function portalVerifyLogin($username, $password) {
    $user = portalFindUserByUsername($username);
    if (!$user) {
        return false;
    }
    if (($user['status'] ?? '') !== 'active') {
        return false;
    }
    // TODO: Replace plaintext passwords with password_hash/password_verify before production.
    if ($user['password'] !== $password) {
        return false;
    }
    return $user;
}

function portalIsStaffLoggedIn() {
    portalEnsureSession();
    // Check legacy portal staff session
    if (!empty($_SESSION[PORTAL_STAFF_SESSION]['username'])) {
        return true;
    }
    // Also accept unified session with admin or staff role
    $unified = $_SESSION[PORTAL_UNIFIED_SESSION] ?? null;
    if (!empty($unified['logged_in']) && in_array($unified['role'] ?? '', ['admin', 'staff'], true)) {
        return true;
    }
    return false;
}

function portalGetStaffUser() {
    portalEnsureSession();
    if (!empty($_SESSION[PORTAL_STAFF_SESSION]['username'])) {
        return $_SESSION[PORTAL_STAFF_SESSION];
    }
    // Fall back to unified session for admin/staff
    $unified = $_SESSION[PORTAL_UNIFIED_SESSION] ?? null;
    if (!empty($unified['logged_in']) && in_array($unified['role'] ?? '', ['admin', 'staff'], true)) {
        return $unified;
    }
    return null;
}

function portalGetStaffRole() {
    $u = portalGetStaffUser();
    return $u ? (string)($u['role'] ?? '') : '';
}

/**
 * Protect a staff page. Redirects to /login.php if not authenticated.
 * Optionally restrict to specific roles (default: admin and staff).
 */
function portalRequireStaff($allowedRoles = ['admin', 'staff']) {
    portalEnsureSession();
    if (!portalIsStaffLoggedIn()) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : '';
        header('Location: /login.php' . ($redirect ? '?redirect=' . $redirect : ''));
        exit;
    }
    $role = portalGetStaffRole();
    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><title>Access Denied</title></head>'
            . '<body style="background:#07111f;color:#eaf3ff;font-family:sans-serif;padding:2rem;">'
            . '<h1 style="color:#ffc600;">Access Denied</h1>'
            . '<p>You do not have permission to view this page.</p>'
            . '<p><a href="/dashboard.php" style="color:#36f3ff;">Return to Dashboard</a></p>'
            . '</body></html>';
        exit;
    }
}

function portalStaffLogout() {
    portalEnsureSession();
    unset($_SESSION[PORTAL_STAFF_SESSION]);
    unset($_SESSION[PORTAL_UNIFIED_SESSION]);
}

// ------------------------------------------------------------------
// Unified dashboard session helpers
// ------------------------------------------------------------------

/**
 * Check if any user is logged in via the unified dashboard session.
 */
function portalIsLoggedIn() {
    portalEnsureSession();
    return !empty($_SESSION[PORTAL_UNIFIED_SESSION]['logged_in']);
}

/**
 * Get the unified session user array, or null if not logged in.
 */
function portalGetUser() {
    portalEnsureSession();
    return $_SESSION[PORTAL_UNIFIED_SESSION] ?? null;
}

/**
 * Get the role from the unified session.
 */
function portalGetRole() {
    $u = portalGetUser();
    return $u ? (string)($u['role'] ?? '') : '';
}

/**
 * Protect a dashboard page. Redirects to /login.php if not authenticated.
 */
function portalRequireLogin() {
    portalEnsureSession();
    if (!portalIsLoggedIn()) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : '';
        header('Location: /login.php' . ($redirect ? '?redirect=' . $redirect : ''));
        exit;
    }
}

/**
 * Full logout: clears unified session, staff portal session, and client portal session.
 */
function portalLogout() {
    portalEnsureSession();
    unset($_SESSION[PORTAL_UNIFIED_SESSION]);
    unset($_SESSION[PORTAL_STAFF_SESSION]);
    unset($_SESSION[PORTAL_CLIENT_SESSION]);
}

// ------------------------------------------------------------------
// Client management
// ------------------------------------------------------------------

function portalLoadClients() {
    $data = portalLoadJson(PORTAL_CLIENTS_FILE);
    return isset($data['clients']) && is_array($data['clients']) ? $data['clients'] : [];
}

function portalSaveClients(array $clients) {
    return portalSaveJson(PORTAL_CLIENTS_FILE, ['clients' => array_values($clients)]);
}

function portalFindClientByUsername($username) {
    $username = strtolower(trim((string)$username));
    foreach (portalLoadClients() as $client) {
        if (strtolower(trim((string)($client['username'] ?? ''))) === $username) {
            return $client;
        }
    }
    return null;
}

function portalFindClientById($id) {
    $id = trim((string)$id);
    foreach (portalLoadClients() as $client) {
        if (($client['id'] ?? '') === $id) {
            return $client;
        }
    }
    return null;
}

function portalVerifyClientLogin($username, $password) {
    $client = portalFindClientByUsername($username);
    if (!$client) {
        return false;
    }
    if (($client['status'] ?? '') !== 'active') {
        return false;
    }
    if (!password_verify((string)$password, (string)($client['password_hash'] ?? ''))) {
        return false;
    }
    return $client;
}

function portalRegisterClient($username, $password, $email, $displayName, &$error = '') {
    $username    = strtolower(trim((string)$username));
    $email       = trim((string)$email);
    $displayName = trim((string)$displayName);

    if ($username === '' || strlen($username) < 3 || !preg_match('/^[a-z0-9._-]+$/', $username)) {
        $error = 'Username must be at least 3 characters and use only letters, numbers, dot, underscore, or dash.';
        return false;
    }
    if (strlen((string)$password) < 8) {
        $error = 'Password must be at least 8 characters.';
        return false;
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'A valid email address is required.';
        return false;
    }
    if (portalFindClientByUsername($username)) {
        $error = 'That username is already taken.';
        return false;
    }

    $clients   = portalLoadClients();
    $clients[] = [
        'id'            => bin2hex(random_bytes(8)),
        'username'      => $username,
        'password_hash' => password_hash((string)$password, PASSWORD_DEFAULT),
        'email'         => $email,
        'display_name'  => $displayName !== '' ? $displayName : $username,
        'role'          => 'client',
        'status'        => 'active',
        'created_at'    => date('c'),
    ];
    if (!portalSaveClients($clients)) {
        $error = 'Failed to save account. Please try again.';
        return false;
    }
    return true;
}

function portalIsClientLoggedIn() {
    portalEnsureSession();
    return !empty($_SESSION[PORTAL_CLIENT_SESSION]['username']);
}

function portalGetClientUser() {
    portalEnsureSession();
    return $_SESSION[PORTAL_CLIENT_SESSION] ?? null;
}

/**
 * Protect a client page. Redirects to /client/login.php if not authenticated.
 */
function portalRequireClient() {
    portalEnsureSession();
    if (!portalIsClientLoggedIn()) {
        $redirect = isset($_SERVER['REQUEST_URI']) ? urlencode($_SERVER['REQUEST_URI']) : '';
        header('Location: /client/login.php' . ($redirect ? '?redirect=' . $redirect : ''));
        exit;
    }
}

function portalClientLogout() {
    portalEnsureSession();
    unset($_SESSION[PORTAL_CLIENT_SESSION]);
}

// ------------------------------------------------------------------
// Request management
// ------------------------------------------------------------------

function portalLoadRequests() {
    $data = portalLoadJson(PORTAL_REQUESTS_FILE);
    return isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
}

function portalAppendRequest(array $request) {
    $data     = portalLoadJson(PORTAL_REQUESTS_FILE);
    $requests = isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
    $requests[] = $request;
    return portalSaveJson(PORTAL_REQUESTS_FILE, ['requests' => $requests]);
}

function portalLoadCommercialRequests() {
    $data = portalLoadJson(PORTAL_COMMERCIAL_FILE);
    return isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
}

function portalAppendCommercialRequest(array $request) {
    $data     = portalLoadJson(PORTAL_COMMERCIAL_FILE);
    $requests = isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
    $requests[] = $request;
    return portalSaveJson(PORTAL_COMMERCIAL_FILE, ['requests' => $requests]);
}

function portalLoadEstimateRequests() {
    $data = portalLoadJson(PORTAL_ESTIMATES_FILE);
    return isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
}

function portalAppendEstimateRequest(array $request) {
    $data     = portalLoadJson(PORTAL_ESTIMATES_FILE);
    $requests = isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];
    $requests[] = $request;
    return portalSaveJson(PORTAL_ESTIMATES_FILE, ['requests' => $requests]);
}

function portalSaveEstimateRequests(array $requests) {
    return portalSaveJson(PORTAL_ESTIMATES_FILE, ['requests' => array_values($requests)]);
}
