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

function portalFindUserByEmail($email) {
    $email = strtolower(trim((string)$email));
    if ($email === '') {
        return null;
    }
    foreach (portalLoadUsers() as $user) {
        if (strtolower(trim((string)($user['email'] ?? ''))) === $email) {
            return $user;
        }
    }
    return null;
}

function portalIsValidEstimateUsername($username) {
    $username = trim((string)$username);
    return (bool) preg_match('/^[A-Za-z0-9_-]{3,}$/', $username);
}

function portalGenerateVerificationToken() {
    return bin2hex(random_bytes(16));
}

function portalGenerateTemporaryPassword($length = 12) {
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
    $max = strlen($alphabet) - 1;
    $out = '';
    for ($i = 0; $i < $length; $i++) {
        $out .= $alphabet[random_int(0, $max)];
    }
    return $out;
}

function portalGenerateUniqueRequestId() {
    $requests = portalLoadProjectRequests();
    $existing = [];
    foreach ($requests as $r) {
        if (!empty($r['request_id'])) {
            $existing[(string)$r['request_id']] = true;
        } elseif (!empty($r['estimate_id'])) {
            $existing[(string)$r['estimate_id']] = true;
        }
    }
    $datePart = date('Ymd');
    $attempts = 0;
    do {
        $estimateId = 'RLS-' . $datePart . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $attempts++;
    } while (isset($existing[$estimateId]) && $attempts < 10000);
    return $estimateId;
}

function portalGenerateUniqueEstimateId() {
    return portalGenerateUniqueRequestId();
}

function portalGetRequestDisplayId(array $request) {
    $requestId = trim((string)($request['request_id'] ?? ''));
    if ($requestId !== '') {
        return $requestId;
    }
    $estimateId = trim((string)($request['estimate_id'] ?? ''));
    if ($estimateId !== '') {
        return $estimateId;
    }
    return 'Legacy Request';
}

function portalGetEstimateDisplayId(array $request) {
    return portalGetRequestDisplayId($request);
}

/**
 * Create a new client-style user entry in users.json for estimate submissions.
 *
 * TODO: Replace plaintext passwords with password_hash/password_verify before production.
 * TODO: Add password reset flow and stop issuing temporary passwords.
 */
function portalCreateClientUserFromEstimate($username, $name, $email, $phone = '', $discordUsername = '', &$error = '') {
    $error = '';
    $username = strtolower(trim((string)$username));
    $name = trim((string)$name);
    $email = trim((string)$email);
    $phone = trim((string)$phone);
    $discordUsername = trim((string)$discordUsername);

    if (!portalIsValidEstimateUsername($username)) {
        $error = 'Username must be at least 3 characters and use only letters, numbers, dash, or underscore.';
        return false;
    }
    if ($name === '') {
        $error = 'Name is required.';
        return false;
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'A valid email address is required.';
        return false;
    }
    if (portalFindUserByUsername($username)) {
        $error = 'That username is already taken. Please log in or choose another username.';
        return false;
    }
    if (portalFindUserByEmail($email)) {
        $error = 'An account with this email already exists. Please log in and submit your estimate from your account.';
        return false;
    }

    $temporaryPassword = portalGenerateTemporaryPassword(12);
    $verificationToken = portalGenerateVerificationToken();
    $users = portalLoadUsers();
    $users[] = [
        'username' => $username,
        'password' => $temporaryPassword,
        'role' => 'client',
        'status' => 'pending_verification',
        'display_name' => $name,
        'email' => $email,
        'phone' => $phone,
        'discord_username' => $discordUsername,
        'verification_token' => $verificationToken,
        'verification_expires_at' => date('c', time() + (48 * 3600)),
        'created_at' => date('c'),
    ];
    if (!portalSaveUsers($users)) {
        $error = 'Unable to create your account right now. Please try again.';
        return false;
    }

    return [
        'username' => $username,
        'temporary_password' => $temporaryPassword,
        'verification_token' => $verificationToken,
        'verification_expires_at' => date('c', time() + (48 * 3600)),
    ];
}

function portalActivateUserByVerificationToken($token) {
    $token = trim((string)$token);
    if ($token === '') {
        return false;
    }
    $users = portalLoadUsers();
    $updated = false;
    $matched = false;
    foreach ($users as &$user) {
        if (($user['verification_token'] ?? '') !== $token) {
            continue;
        }
        $matched = true;
        $expiresAt = trim((string)($user['verification_expires_at'] ?? ''));
        if ($expiresAt !== '' && strtotime($expiresAt) !== false && strtotime($expiresAt) < time()) {
            unset($user);
            return false;
        }
        $user['status'] = 'active';
        $user['verification_token'] = '';
        $user['verification_expires_at'] = '';
        $updated = true;
        break;
    }
    unset($user);

    if (!$matched || !$updated) {
        return false;
    }
    if (!portalSaveUsers($users)) {
        return false;
    }
    return true;
}

function portalRefreshVerificationTokenByEmail($email, &$userOut = null) {
    $email = strtolower(trim((string)$email));
    if ($email === '') {
        return false;
    }
    $users = portalLoadUsers();
    foreach ($users as &$user) {
        if (strtolower(trim((string)($user['email'] ?? ''))) !== $email) {
            continue;
        }
        if (($user['status'] ?? '') === 'active') {
            $userOut = $user;
            unset($user);
            return false;
        }
        $user['verification_token'] = portalGenerateVerificationToken();
        $user['verification_expires_at'] = date('c', time() + (48 * 3600));
        if (!portalSaveUsers($users)) {
            unset($user);
            return false;
        }
        $userOut = $user;
        unset($user);
        return true;
    }
    unset($user);
    return false;
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
define('PORTAL_PROJECT_REQUESTS_FILE', PORTAL_DATA_DIR . '/project_requests.json');
define('PORTAL_ESTIMATES_FILE',        PORTAL_DATA_DIR . '/estimate_requests.json');
define('PORTAL_PROPOSALS_FILE',        PORTAL_DATA_DIR . '/proposals.json');
define('PORTAL_PAYMENTS_FILE',         PORTAL_DATA_DIR . '/payments.json');
define('PORTAL_PAYPAL_WEBHOOK_LOG',    PORTAL_DATA_DIR . '/paypal-webhook-log.json');
define('PORTAL_PROJECT_AGREEMENTS_FILE', PORTAL_DATA_DIR . '/project_agreements.json');
define('PORTAL_ADMIN_SETTINGS_FILE', PORTAL_DATA_DIR . '/admin_settings.json');
define('PORTAL_EMAIL_LOG_FILE', PORTAL_DATA_DIR . '/email-log.json');

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

function portalDefaultAdminSettings() {
    return [
        'paypal' => [
            'mode' => 'manual',
            'sandbox' => [
                'client_id'    => '',
                'secret'       => '',
                'business_email' => '',
                'webhook_id'   => '',
                'payment_link' => '',
            ],
            'live' => [
                'client_id'    => '',
                'secret'       => '',
                'business_email' => '',
                'webhook_id'   => '',
                'payment_link' => '',
            ],
            'currency'                     => 'USD',
            'invoice_message'              => '',
            'payment_instructions'         => '',
            'require_payment_before_work'  => true,
            'enable_manual_recording'      => true,
            'enable_webhook_logging'       => false,
            // Legacy flat fields kept for backward compat
            'client_id'     => '',
            'secret'        => '',
            'environment'   => 'sandbox',
            'business_email'=> '',
            'invoice_defaults' => '',
        ],
        'email' => [
            'from_name' => 'Runlevel Systems',
            'from_email' => 'billing@runlevelsystems.com',
            'reply_to' => 'billing@runlevelsystems.com',
            'smtp_host' => 'mail.runlevelsystems.com',
            'smtp_port' => 465,
            'smtp_security' => 'ssl',
            'smtp_auth' => true,
            'smtp_username' => 'billing@runlevelsystems.com',
            // TODO: Move secrets to environment variables or protected server config before production.
            'smtp_password' => '',
            'smtp_debug' => 'off',
        ],
        'site' => [
            'company_name' => 'Runlevel Systems',
            'support_email' => '',
            'support_phone' => '',
        ],
        'business' => [
            'legal_name' => '',
            'address' => '',
        ],
        'updated_at' => '',
        'updated_by' => '',
    ];
}

function portalNormalizeEmailSettings(array $emailSettings) {
    $defaults = portalDefaultAdminSettings()['email'];
    $email = array_merge($defaults, $emailSettings);
    $email['from_name'] = trim((string)($email['from_name'] ?? $defaults['from_name']));
    $email['from_email'] = trim((string)($email['from_email'] ?? $defaults['from_email']));
    $email['reply_to'] = trim((string)($email['reply_to'] ?? $defaults['reply_to']));
    $email['smtp_host'] = trim((string)($email['smtp_host'] ?? $defaults['smtp_host']));
    $email['smtp_port'] = (int)($email['smtp_port'] ?? $defaults['smtp_port']);
    if ($email['smtp_port'] <= 0) {
        $email['smtp_port'] = (int)$defaults['smtp_port'];
    }
    $security = strtolower(trim((string)($email['smtp_security'] ?? $defaults['smtp_security'])));
    $email['smtp_security'] = in_array($security, ['ssl', 'tls', 'none'], true) ? $security : $defaults['smtp_security'];
    $smtpAuth = $email['smtp_auth'] ?? $defaults['smtp_auth'];
    $email['smtp_auth'] = filter_var($smtpAuth, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if ($email['smtp_auth'] === null) {
        $email['smtp_auth'] = !empty($smtpAuth);
    }
    $email['smtp_username'] = trim((string)($email['smtp_username'] ?? $defaults['smtp_username']));
    $email['smtp_password'] = (string)($email['smtp_password'] ?? '');
    $debug = strtolower(trim((string)($email['smtp_debug'] ?? $defaults['smtp_debug'])));
    $email['smtp_debug'] = in_array($debug, ['off', 'basic', 'verbose'], true) ? $debug : $defaults['smtp_debug'];
    return $email;
}

function portalLoadAdminSettings() {
    $stored = portalLoadJson(PORTAL_ADMIN_SETTINGS_FILE);
    $defaults = portalDefaultAdminSettings();
    $paypalStored = isset($stored['paypal']) && is_array($stored['paypal']) ? $stored['paypal'] : [];
    $emailStored = isset($stored['email']) && is_array($stored['email']) ? $stored['email'] : [];
    $siteStored = isset($stored['site']) && is_array($stored['site']) ? $stored['site'] : [];
    $businessStored = isset($stored['business']) && is_array($stored['business']) ? $stored['business'] : [];
    $paypal = array_merge($defaults['paypal'], $paypalStored);
    // Ensure nested sandbox/live sub-arrays are merged properly
    $paypal['sandbox'] = array_merge($defaults['paypal']['sandbox'], isset($paypalStored['sandbox']) && is_array($paypalStored['sandbox']) ? $paypalStored['sandbox'] : []);
    $paypal['live']    = array_merge($defaults['paypal']['live'],    isset($paypalStored['live'])    && is_array($paypalStored['live'])    ? $paypalStored['live']    : []);
    return [
        'paypal' => $paypal,
        'email' => portalNormalizeEmailSettings($emailStored),
        'site' => array_merge($defaults['site'], $siteStored),
        'business' => array_merge($defaults['business'], $businessStored),
        'updated_at' => (string)($stored['updated_at'] ?? ''),
        'updated_by' => (string)($stored['updated_by'] ?? ''),
    ];
}

function portalSaveAdminSettings(array $settings) {
    $defaults = portalDefaultAdminSettings();
    $paypalIn = isset($settings['paypal']) && is_array($settings['paypal']) ? $settings['paypal'] : [];
    $paypal = array_merge($defaults['paypal'], $paypalIn);
    $paypal['sandbox'] = array_merge($defaults['paypal']['sandbox'], isset($paypalIn['sandbox']) && is_array($paypalIn['sandbox']) ? $paypalIn['sandbox'] : []);
    $paypal['live']    = array_merge($defaults['paypal']['live'],    isset($paypalIn['live'])    && is_array($paypalIn['live'])    ? $paypalIn['live']    : []);
    $payload = [
        'paypal' => $paypal,
        'email' => portalNormalizeEmailSettings(isset($settings['email']) && is_array($settings['email']) ? $settings['email'] : []),
        'site' => array_merge($defaults['site'], isset($settings['site']) && is_array($settings['site']) ? $settings['site'] : []),
        'business' => array_merge($defaults['business'], isset($settings['business']) && is_array($settings['business']) ? $settings['business'] : []),
        'updated_at' => (string)($settings['updated_at'] ?? ''),
        'updated_by' => (string)($settings['updated_by'] ?? ''),
    ];
    return portalSaveJson(PORTAL_ADMIN_SETTINGS_FILE, $payload);
}

function portalLoadEmailLog() {
    $data = portalLoadJson(PORTAL_EMAIL_LOG_FILE);
    $items = isset($data['emails']) && is_array($data['emails']) ? $data['emails'] : [];
    $emails = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $emails[] = [
            'email_id' => (string)($item['email_id'] ?? ''),
            'timestamp' => (string)($item['timestamp'] ?? ''),
            'to' => (string)($item['to'] ?? ''),
            'subject' => (string)($item['subject'] ?? ''),
            'status' => (string)($item['status'] ?? ''),
            'error_message' => (string)($item['error_message'] ?? ''),
            'context' => (string)($item['context'] ?? ''),
        ];
    }
    return $emails;
}

function portalAppendEmailLog(array $entry) {
    $emails = portalLoadEmailLog();
    $emails[] = [
        'email_id' => (string)($entry['email_id'] ?? ''),
        'timestamp' => (string)($entry['timestamp'] ?? date('c')),
        'to' => (string)($entry['to'] ?? ''),
        'subject' => (string)($entry['subject'] ?? ''),
        'status' => (string)($entry['status'] ?? 'unknown'),
        'error_message' => (string)($entry['error_message'] ?? ''),
        'context' => (string)($entry['context'] ?? ''),
    ];
    if (count($emails) > 200) {
        $emails = array_slice($emails, -200);
    }
    return portalSaveJson(PORTAL_EMAIL_LOG_FILE, ['emails' => array_values($emails)]);
}

/**
 * Sanitize HTML output.
 */
function pe($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * Allow only local relative paths beginning with "/".
 */
function portalSanitizeReturnPath($path, $default = '/dashboard.php') {
    $path = trim((string)$path);
    if ($path === '') {
        return $default;
    }
    if ($path[0] !== '/') {
        return $default;
    }
    if (preg_match('#^//#', $path) || strpos($path, '\\') !== false) {
        return $default;
    }
    $parts = parse_url($path);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) {
        return $default;
    }
    return $path;
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
function portalVerifyLogin($username, $password, &$failureReason = '') {
    $failureReason = '';
    $user = portalFindUserByUsername($username);
    if (!$user) {
        $failureReason = 'invalid_credentials';
        return false;
    }
    $status = (string)($user['status'] ?? '');
    if ($status !== 'active') {
        if ($status === 'pending_verification') {
            $failureReason = 'pending_verification';
        } elseif ($status === 'disabled') {
            $failureReason = 'disabled';
        } else {
            $failureReason = 'inactive';
        }
        return false;
    }
    // TODO: Replace plaintext passwords with password_hash/password_verify before production.
    if ($user['password'] !== $password) {
        $failureReason = 'invalid_credentials';
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
    return portalLoadProjectRequests();
}

function portalAppendRequest(array $request) {
    return portalAppendProjectRequest($request);
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
    return portalLoadProjectRequests();
}

function portalAppendEstimateRequest(array $request) {
    return portalAppendProjectRequest($request);
}

function portalSaveEstimateRequests(array $requests) {
    return portalSaveProjectRequests($requests);
}

function portalNormalizeProjectRequest(array $request) {
    if (!isset($request['request_id']) || trim((string)$request['request_id']) === '') {
        $legacy = trim((string)($request['estimate_id'] ?? ''));
        if ($legacy !== '') {
            $request['request_id'] = $legacy;
        } else {
            $seed = (string)($request['id'] ?? '') . '|' . (string)($request['created_at'] ?? '');
            $hash = str_pad((string)((abs(crc32($seed)) % 9000) + 1000), 4, '0', STR_PAD_LEFT);
            $request['request_id'] = 'RLS-' . date('Ymd', strtotime((string)($request['created_at'] ?? 'now'))) . '-' . $hash;
        }
    }
    if (!isset($request['estimate_id']) || trim((string)$request['estimate_id']) === '') {
        $request['estimate_id'] = $request['request_id'];
    }
    $status = trim((string)($request['status'] ?? 'new'));
    $statusMap = [
        'reviewed' => 'reviewing',
        'proposal_needed' => 'proposal_drafted',
        'quoted' => 'proposal_drafted',
        'approved' => 'accepted',
    ];
    $request['status'] = isset($statusMap[$status]) ? $statusMap[$status] : $status;
    $request['timeline'] = (string)($request['timeline'] ?? ($request['desired_timeline'] ?? ''));
    $request['budget_comfort'] = (string)($request['budget_comfort'] ?? ($request['budget_range'] ?? ''));
    $request['project_stage'] = (string)($request['project_stage'] ?? '');
    $request['project_size'] = (string)($request['project_size'] ?? '');
    $request['estimated_cost_range'] = (string)($request['estimated_cost_range'] ?? '');
    $request['estimated_time_range'] = (string)($request['estimated_time_range'] ?? '');
    $request['staff_summary'] = (string)($request['staff_summary'] ?? '');
    $request['recommended_next_step'] = (string)($request['recommended_next_step'] ?? '');
    $request['admin_notes'] = (string)($request['admin_notes'] ?? ($request['internal_notes'] ?? ($request['notes'] ?? '')));
    $request['internal_notes'] = $request['admin_notes'];
    $request['admin_notes_updated_at'] = (string)($request['admin_notes_updated_at'] ?? '');
    $request['client_notes'] = (string)($request['client_notes'] ?? '');
    $request['client_notes_updated_at'] = (string)($request['client_notes_updated_at'] ?? '');
    $request['staff_response'] = (string)($request['staff_response'] ?? '');
    $request['staff_response_updated_at'] = (string)($request['staff_response_updated_at'] ?? '');
    $request['invoice_reference'] = (string)($request['invoice_reference'] ?? '');
    $request['invoice_status'] = (string)($request['invoice_status'] ?? '');
    $request['amount_due'] = (string)($request['amount_due'] ?? '');
    $request['amount_paid'] = (string)($request['amount_paid'] ?? '');
    $request['balance_due'] = (string)($request['balance_due'] ?? '');
    $request['payment_notes'] = (string)($request['payment_notes'] ?? '');
    $request['payment_received_at'] = (string)($request['payment_received_at'] ?? '');
    $request['payment_link'] = (string)($request['payment_link'] ?? '');
    $request['invoice_sent_at'] = (string)($request['invoice_sent_at'] ?? '');
    $request['attachments'] = isset($request['attachments']) && is_array($request['attachments']) ? array_values($request['attachments']) : [];
    $request['proposal_ids'] = isset($request['proposal_ids']) && is_array($request['proposal_ids']) ? array_values($request['proposal_ids']) : [];
    $request['agreement_ids'] = isset($request['agreement_ids']) && is_array($request['agreement_ids']) ? array_values($request['agreement_ids']) : [];
    if (!isset($request['created_at']) || trim((string)$request['created_at']) === '') {
        $request['created_at'] = date('c');
    }
    return $request;
}

function portalLoadProjectRequests() {
    $projectData = portalLoadJson(PORTAL_PROJECT_REQUESTS_FILE);
    $projectRequests = isset($projectData['requests']) && is_array($projectData['requests']) ? $projectData['requests'] : [];

    $legacyData = portalLoadJson(PORTAL_ESTIMATES_FILE);
    $legacyRequests = isset($legacyData['requests']) && is_array($legacyData['requests']) ? $legacyData['requests'] : [];

    $seen = [];
    $all = [];
    foreach (array_merge($projectRequests, $legacyRequests) as $row) {
        $normalized = portalNormalizeProjectRequest((array)$row);
        $key = (string)($normalized['id'] ?? '') . '|' . (string)($normalized['request_id'] ?? '');
        if (isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $all[] = $normalized;
    }
    return $all;
}

function portalSaveProjectRequests(array $requests) {
    $normalized = [];
    foreach ($requests as $request) {
        $normalized[] = portalNormalizeProjectRequest((array)$request);
    }
    return portalSaveJson(PORTAL_PROJECT_REQUESTS_FILE, ['requests' => array_values($normalized)]);
}

function portalAppendProjectRequest(array $request) {
    $requests = portalLoadProjectRequests();
    $requests[] = portalNormalizeProjectRequest($request);
    return portalSaveProjectRequests($requests);
}

function portalLoadProposals() {
    $data = portalLoadJson(PORTAL_PROPOSALS_FILE);
    return isset($data['proposals']) && is_array($data['proposals']) ? $data['proposals'] : [];
}

function portalSaveProposals(array $proposals) {
    return portalSaveJson(PORTAL_PROPOSALS_FILE, ['proposals' => array_values($proposals)]);
}

function portalGenerateUniqueProposalId() {
    $proposals = portalLoadProposals();
    $existing = [];
    foreach ($proposals as $p) {
        if (!empty($p['proposal_id'])) {
            $existing[(string)$p['proposal_id']] = true;
        }
    }
    $datePart = date('Ymd');
    $attempts = 0;
    do {
        $id = 'PROP-' . $datePart . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $attempts++;
    } while (isset($existing[$id]) && $attempts < 10000);
    return $id;
}

function portalGenerateUniquePaymentId() {
    $payments = portalLoadPayments();
    $existing = [];
    foreach ($payments as $p) {
        if (!empty($p['payment_id'])) {
            $existing[(string)$p['payment_id']] = true;
        }
    }
    $datePart = date('Ymd');
    $attempts = 0;
    do {
        $id = 'PAY-' . $datePart . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        $attempts++;
    } while (isset($existing[$id]) && $attempts < 10000);
    return $id;
}

function portalLoadPayments() {
    $data = portalLoadJson(PORTAL_PAYMENTS_FILE);
    return isset($data['payments']) && is_array($data['payments']) ? $data['payments'] : [];
}

function portalSavePayments(array $payments) {
    return portalSaveJson(PORTAL_PAYMENTS_FILE, ['payments' => array_values($payments)]);
}

function portalLoadProjectAgreements() {
    $data = portalLoadJson(PORTAL_PROJECT_AGREEMENTS_FILE);
    return isset($data['agreements']) && is_array($data['agreements']) ? $data['agreements'] : [];
}

function portalSaveProjectAgreements(array $agreements) {
    return portalSaveJson(PORTAL_PROJECT_AGREEMENTS_FILE, ['agreements' => array_values($agreements)]);
}
