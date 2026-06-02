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
    return bin2hex(random_bytes(32));
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

    $temporaryPassword = portalGenerateTemporaryPassword(16);
    $verificationToken = portalGenerateVerificationToken();
    $timestamp = date('c');
    $users = portalLoadUsers();
    $users[] = portalNormalizeUserRecord([
        'user_id' => bin2hex(random_bytes(8)),
        'username' => $username,
        'password_hash' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
        'role' => 'client',
        'status' => 'active',
        'account_status' => 'unverified',
        'email_verified' => false,
        'display_name' => $name,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'discord_username' => $discordUsername,
        'preferred_contact_method' => 'Email',
        'email_verification_token' => $verificationToken,
        'email_verification_sent_at' => $timestamp,
        'verification_expires_at' => date('c', time() + (48 * 3600)),
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
    ]);
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
        $storedToken = (string)($user['email_verification_token'] ?? ($user['verification_token'] ?? ''));
        if ($storedToken === '' || !hash_equals($storedToken, $token)) {
            continue;
        }
        $matched = true;
        $expiresAt = trim((string)($user['verification_expires_at'] ?? ''));
        if ($expiresAt !== '' && strtotime($expiresAt) !== false && strtotime($expiresAt) < time()) {
            unset($user);
            return false;
        }
        $user['email_verified'] = true;
        if (($user['account_status'] ?? '') !== 'staff_approved') {
            $user['account_status'] = 'verified';
        }
        $user['email_verification_token'] = '';
        $user['verification_token'] = '';
        $user['verification_expires_at'] = '';
        $user['updated_at'] = date('c');
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
        if (portalUserBooleanValue($user['email_verified'] ?? null, false) || in_array((string)($user['account_status'] ?? ''), ['verified', 'staff_approved'], true)) {
            $userOut = $user;
            unset($user);
            return false;
        }
        $user['email_verification_token'] = portalGenerateVerificationToken();
        $user['verification_token'] = $user['email_verification_token'];
        $user['verification_expires_at'] = date('c', time() + (48 * 3600));
        $user['email_verification_sent_at'] = date('c');
        $user['updated_at'] = date('c');
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

function portalGetCsrfToken() {
    portalEnsureSession();
    if (empty($_SESSION['rls_csrf_token']) || !is_string($_SESSION['rls_csrf_token'])) {
        $_SESSION['rls_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['rls_csrf_token'];
}

function portalVerifyCsrfToken($token) {
    portalEnsureSession();
    $sessionToken = (string)($_SESSION['rls_csrf_token'] ?? '');
    $token = (string)$token;
    if ($sessionToken === '' || $token === '') {
        return false;
    }
    return hash_equals($sessionToken, $token);
}

function portalUserBooleanValue($value, $default = false) {
    if ($value === null || $value === '') {
        return (bool)$default;
    }
    if (is_bool($value)) {
        return $value;
    }
    $normalized = strtolower(trim((string)$value));
    if (in_array($normalized, ['1', 'true', 'yes', 'on'], true)) {
        return true;
    }
    if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
        return false;
    }
    return (bool)$default;
}

function portalNormalizeUserRole($role) {
    $role = strtolower(trim((string)$role));
    if (!in_array($role, ['admin', 'staff', 'client'], true)) {
        return 'client';
    }
    return $role;
}

function portalNormalizeUserStatus($status) {
    $status = strtolower(trim((string)$status));
    if ($status === 'pending_verification') {
        $status = 'active';
    }
    if (!in_array($status, ['active', 'disabled'], true)) {
        return 'active';
    }
    return $status;
}

function portalNormalizeAccountStatus($accountStatus, $emailVerified = false) {
    $accountStatus = strtolower(trim((string)$accountStatus));
    if ($accountStatus === 'pending_verification') {
        $accountStatus = 'unverified';
    }
    if (!in_array($accountStatus, ['unverified', 'verified', 'staff_approved', 'rejected'], true)) {
        $accountStatus = $emailVerified ? 'verified' : 'unverified';
    }
    return $accountStatus;
}

function portalNormalizeUserRecord(array $user) {
    $now = date('c');
    $username = strtolower(trim((string)($user['username'] ?? '')));
    $email = strtolower(trim((string)($user['email'] ?? '')));
    $role = portalNormalizeUserRole($user['role'] ?? 'client');
    $status = portalNormalizeUserStatus($user['status'] ?? 'active');
    $emailVerified = portalUserBooleanValue($user['email_verified'] ?? null, false);
    $accountStatus = portalNormalizeAccountStatus($user['account_status'] ?? ($emailVerified ? 'verified' : 'unverified'), $emailVerified);
    if ($accountStatus === 'verified' || $accountStatus === 'staff_approved') {
        $emailVerified = true;
    }
    if ($accountStatus === 'rejected') {
        $status = 'disabled';
    }

    $displayName = trim((string)($user['display_name'] ?? ''));
    $name = trim((string)($user['name'] ?? ''));
    if ($name === '' && $displayName !== '') {
        $name = $displayName;
    }
    if ($displayName === '' && $name !== '') {
        $displayName = $name;
    }
    if ($name === '') {
        $name = $username;
    }
    if ($displayName === '') {
        $displayName = $name;
    }

    $passwordHash = (string)($user['password_hash'] ?? '');
    $legacyPassword = (string)($user['password'] ?? '');
    if ($passwordHash === '' && $legacyPassword !== '' && password_get_info($legacyPassword)['algo'] !== 0) {
        $passwordHash = $legacyPassword;
        $legacyPassword = '';
    }

    return [
        'user_id' => (string)($user['user_id'] ?? ($user['id'] ?? bin2hex(random_bytes(8)))),
        'username' => $username,
        'email' => $email,
        'password_hash' => $passwordHash,
        'password' => $legacyPassword,
        'role' => $role,
        'status' => $status,
        'account_status' => $accountStatus,
        'email_verified' => $emailVerified,
        'email_verification_token' => (string)($user['email_verification_token'] ?? ($user['verification_token'] ?? '')),
        'email_verification_sent_at' => (string)($user['email_verification_sent_at'] ?? ''),
        'verification_expires_at' => (string)($user['verification_expires_at'] ?? ''),
        'created_at' => (string)($user['created_at'] ?? $now),
        'updated_at' => (string)($user['updated_at'] ?? ($user['created_at'] ?? $now)),
        'last_login' => (string)($user['last_login'] ?? ''),
        'name' => $name,
        'display_name' => $displayName,
        'phone' => trim((string)($user['phone'] ?? '')),
        'company' => trim((string)($user['company'] ?? '')),
        'preferred_contact_method' => trim((string)($user['preferred_contact_method'] ?? 'Email')) ?: 'Email',
        'staff_notes' => trim((string)($user['staff_notes'] ?? '')),
        'discord_username' => trim((string)($user['discord_username'] ?? '')),
    ];
}

function portalFindUserIndexByUsername(array $users, $username) {
    $username = strtolower(trim((string)$username));
    foreach ($users as $index => $user) {
        if (strtolower(trim((string)($user['username'] ?? ''))) === $username) {
            return (int)$index;
        }
    }
    return -1;
}

function portalFindUserIndexByUserId(array $users, $userId) {
    $userId = trim((string)$userId);
    if ($userId === '') {
        return -1;
    }
    foreach ($users as $index => $user) {
        if (trim((string)($user['user_id'] ?? '')) === $userId) {
            return (int)$index;
        }
    }
    return -1;
}

function portalFindUserByLoginIdentifier($identifier) {
    $identifier = strtolower(trim((string)$identifier));
    if ($identifier === '') {
        return null;
    }
    foreach (portalLoadUsers() as $user) {
        $username = strtolower(trim((string)($user['username'] ?? '')));
        $email = strtolower(trim((string)($user['email'] ?? '')));
        if ($identifier === $username || ($email !== '' && $identifier === $email)) {
            return $user;
        }
    }
    return null;
}

function portalPasswordMatches(array $user, $password) {
    $password = (string)$password;
    $hash = (string)($user['password_hash'] ?? '');
    if ($hash !== '' && password_verify($password, $hash)) {
        return true;
    }
    $legacy = (string)($user['password'] ?? '');
    return $legacy !== '' && hash_equals($legacy, $password);
}

function portalUpgradeUserPasswordIfNeeded(array $user, $password) {
    $needsUpgrade = (string)($user['password_hash'] ?? '') === '';
    if (!$needsUpgrade) {
        return;
    }
    $users = portalLoadUsers();
    $index = portalFindUserIndexByUserId($users, (string)($user['user_id'] ?? ''));
    if ($index < 0) {
        $index = portalFindUserIndexByUsername($users, (string)($user['username'] ?? ''));
    }
    if ($index < 0) {
        return;
    }
    $users[$index]['password_hash'] = password_hash((string)$password, PASSWORD_DEFAULT);
    $users[$index]['password'] = '';
    $users[$index]['updated_at'] = date('c');
    portalSaveUsers($users);
}

function portalUserCanLogin(array $user, &$failureReason = '') {
    $failureReason = '';
    $status = portalNormalizeUserStatus($user['status'] ?? 'active');
    if ($status !== 'active') {
        $failureReason = ($status === 'disabled') ? 'disabled' : 'inactive';
        return false;
    }
    $accountStatus = portalNormalizeAccountStatus($user['account_status'] ?? 'unverified', portalUserBooleanValue($user['email_verified'] ?? null, false));
    if ($accountStatus === 'rejected') {
        $failureReason = 'disabled';
        return false;
    }
    return true;
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
    $rows = isset($data['users']) && is_array($data['users']) ? $data['users'] : [];
    $normalized = [];
    foreach ($rows as $row) {
        $normalized[] = portalNormalizeUserRecord((array)$row);
    }
    return $normalized;
}

function portalSaveUsers(array $users) {
    $normalized = [];
    foreach ($users as $user) {
        $normalized[] = portalNormalizeUserRecord((array)$user);
    }
    return portalSaveJson(PORTAL_USERS_FILE, ['users' => array_values($normalized)]);
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

function portalUpdateUser(array $updatedUser) {
    $users = portalLoadUsers();
    $index = portalFindUserIndexByUserId($users, (string)($updatedUser['user_id'] ?? ''));
    if ($index < 0) {
        $index = portalFindUserIndexByUsername($users, (string)($updatedUser['username'] ?? ''));
    }
    if ($index < 0) {
        return false;
    }
    $updatedUser['updated_at'] = date('c');
    $users[$index] = portalNormalizeUserRecord($updatedUser);
    return portalSaveUsers($users);
}

/**
 * Verify staff login credentials (admin and staff roles only).
 *
 * TODO: Replace plaintext password comparison with password_hash/password_verify before production.
 *       Current plaintext storage is temporary for development only.
 */
function portalVerifyStaffLogin($username, $password) {
    $failureReason = '';
    $user = portalVerifyLogin($username, $password, $failureReason);
    if (!$user) {
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
    $user = portalFindUserByLoginIdentifier($username);
    if (!$user) {
        $failureReason = 'invalid_credentials';
        return false;
    }
    if (!portalUserCanLogin($user, $failureReason)) {
        return false;
    }
    if (!portalPasswordMatches($user, $password)) {
        $failureReason = 'invalid_credentials';
        return false;
    }
    portalUpgradeUserPasswordIfNeeded($user, $password);
    $user['last_login'] = date('c');
    $user['updated_at'] = date('c');
    portalUpdateUser($user);
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

function portalRegisterClient($username, $password, $email, $displayName, &$error = '', array $extras = []) {
    $error = '';
    $username    = strtolower(trim((string)$username));
    $email       = strtolower(trim((string)$email));
    $displayName = trim((string)$displayName);
    $name = trim((string)($extras['name'] ?? $displayName));
    $phone = trim((string)($extras['phone'] ?? ''));
    $company = trim((string)($extras['company'] ?? ''));
    $preferredContactMethod = trim((string)($extras['preferred_contact_method'] ?? 'Email'));
    if (!in_array($preferredContactMethod, ['Email', 'Phone', 'Dashboard Message'], true)) {
        $preferredContactMethod = 'Email';
    }

    function portalGenerateUsernameFromEmail($email, $fallbackPrefix = 'client') {
        $email = strtolower(trim((string)$email));
        $localPart = $fallbackPrefix;
        if (strpos($email, '@') !== false) {
            $localPart = substr($email, 0, strpos($email, '@'));
        }
        $localPart = preg_replace('/[^a-z0-9._-]/', '-', $localPart);
        $localPart = trim((string)$localPart, '-_.');
        if ($localPart === '') {
            $localPart = $fallbackPrefix;
        }
        if (strlen($localPart) < 3) {
            $localPart .= '-rls';
        }
        $candidate = substr($localPart, 0, 24);
        $suffix = 0;
        while (portalFindUserByUsername($candidate)) {
            $suffix++;
            $candidate = substr($localPart, 0, max(3, 20 - strlen((string)$suffix))) . '-' . $suffix;
        }
        return $candidate;
    }

    function portalFindOrCreateClientByEmail($email, $name = '', $phone = '', $company = '', &$wasCreated = false, &$error = '') {
        $error = '';
        $wasCreated = false;
        $email = strtolower(trim((string)$email));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'A valid email address is required.';
            return null;
        }

        $existing = portalFindUserByEmail($email);
        if ($existing) {
            return $existing;
        }

        $username = portalGenerateUsernameFromEmail($email);
        $temporaryPassword = portalGenerateTemporaryPassword(18);
        $verificationToken = portalGenerateVerificationToken();
        $timestamp = date('c');
        $user = portalNormalizeUserRecord([
            'user_id' => bin2hex(random_bytes(8)),
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
            'role' => 'client',
            'status' => 'active',
            'account_status' => 'unverified',
            'email_verified' => false,
            'email_verification_token' => $verificationToken,
            'email_verification_sent_at' => $timestamp,
            'verification_expires_at' => date('c', time() + (48 * 3600)),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
            'name' => trim((string)$name) !== '' ? trim((string)$name) : $username,
            'display_name' => trim((string)$name) !== '' ? trim((string)$name) : $username,
            'phone' => trim((string)$phone),
            'company' => trim((string)$company),
            'preferred_contact_method' => 'Email',
        ]);

        $users = portalLoadUsers();
        $users[] = $user;
        if (!portalSaveUsers($users)) {
            $error = 'Unable to create a client account right now.';
            return null;
        }

        $wasCreated = true;
        return $user;
    }
    $marketingOptIn = portalUserBooleanValue($extras['marketing_opt_in'] ?? false, false);
    $verificationToken = trim((string)($extras['email_verification_token'] ?? ''));
    if ($verificationToken === '') {
        $verificationToken = portalGenerateVerificationToken();
    }

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
    if (portalFindUserByUsername($username)) {
        $error = 'That username is already taken.';
        return false;
    }
    if (portalFindUserByEmail($email)) {
        $error = 'An account with that email already exists.';
        return false;
    }

    $timestamp = date('c');
    $users = portalLoadUsers();
    $users[] = portalNormalizeUserRecord([
        'user_id' => bin2hex(random_bytes(8)),
        'username' => $username,
        'email' => $email,
        'password_hash' => password_hash((string)$password, PASSWORD_DEFAULT),
        'role' => 'client',
        'status' => 'active',
        'account_status' => 'unverified',
        'email_verified' => false,
        'email_verification_token' => $verificationToken,
        'email_verification_sent_at' => $timestamp,
        'verification_expires_at' => date('c', time() + (48 * 3600)),
        'created_at' => $timestamp,
        'updated_at' => $timestamp,
        'name' => $name !== '' ? $name : ($displayName !== '' ? $displayName : $username),
        'display_name' => $displayName !== '' ? $displayName : ($name !== '' ? $name : $username),
        'phone' => $phone,
        'company' => $company,
        'preferred_contact_method' => $preferredContactMethod,
        'staff_notes' => $marketingOptIn ? 'Client opted in to project-related email updates.' : '',
    ]);
    if (!portalSaveUsers($users)) {
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
