<?php
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';
require_once __DIR__ . '/../includes/email.php';

portalRequireStaff(['admin', 'staff']);

$message = '';
$error = '';
$currentStaff = portalGetStaffUser();
$currentStaffRole = (string)($currentStaff['role'] ?? 'staff');
$isAdmin = $currentStaffRole === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');
    $csrfToken = (string)($_POST['csrf_token'] ?? '');

    if (!portalVerifyCsrfToken($csrfToken)) {
        $error = 'Your session expired. Please refresh and try again.';
    } else {
        $users = portalLoadUsers();
        $targetUsername = trim((string)($_POST['target_username'] ?? ''));
        $targetIndex = -1;
        foreach ($users as $idx => $candidate) {
            if (strcasecmp((string)($candidate['username'] ?? ''), $targetUsername) === 0) {
                $targetIndex = (int)$idx;
                break;
            }
        }

        if ($action === 'add') {
            $username = trim((string)($_POST['username'] ?? ''));
            $email = strtolower(trim((string)($_POST['email'] ?? '')));
            $password = (string)($_POST['password'] ?? '');
            $displayName = trim((string)($_POST['display_name'] ?? ''));
            $role = portalNormalizeUserRole((string)($_POST['role'] ?? 'client'));

            if ($username === '' || $email === '' || $password === '') {
                $error = 'Username, email, and password are required.';
            } elseif (!preg_match('/^[a-zA-Z0-9._-]+$/', $username)) {
                $error = 'Username may only contain letters, numbers, dot, underscore, or dash.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } elseif (portalFindUserByUsername($username) || portalFindUserByEmail($email)) {
                $error = 'Username or email already exists.';
            } else {
                $newUser = portalNormalizeUserRecord([
                    'user_id' => bin2hex(random_bytes(8)),
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    'role' => $role,
                    'status' => 'active',
                    'account_status' => $role === 'client' ? 'unverified' : 'verified',
                    'email_verified' => $role === 'client' ? false : true,
                    'email_verification_token' => $role === 'client' ? portalGenerateVerificationToken() : '',
                    'email_verification_sent_at' => $role === 'client' ? date('c') : '',
                    'display_name' => $displayName !== '' ? $displayName : $username,
                    'name' => $displayName !== '' ? $displayName : $username,
                    'created_at' => date('c'),
                    'updated_at' => date('c'),
                ]);
                $users[] = $newUser;
                if (portalSaveUsers($users)) {
                    $message = 'User added successfully.';
                } else {
                    $error = 'Failed to save user data.';
                }
            }
        } elseif ($targetIndex === -1) {
            $error = 'User not found.';
        } elseif ($action === 'toggle_status') {
            if (strcasecmp($users[$targetIndex]['username'] ?? '', $currentStaff['username'] ?? '') === 0) {
                $error = 'You cannot disable your own account.';
            } else {
                $users[$targetIndex]['status'] = (($users[$targetIndex]['status'] ?? 'active') === 'active') ? 'disabled' : 'active';
                $users[$targetIndex]['updated_at'] = date('c');
                if (portalSaveUsers($users)) {
                    $message = 'Account status updated.';
                } else {
                    $error = 'Failed to update account status.';
                }
            }
        } elseif ($action === 'set_account_status') {
            $newAccountStatus = portalNormalizeAccountStatus((string)($_POST['account_status'] ?? 'unverified'));
            $users[$targetIndex]['account_status'] = $newAccountStatus;
            if ($newAccountStatus === 'verified' || $newAccountStatus === 'staff_approved') {
                $users[$targetIndex]['email_verified'] = true;
                $users[$targetIndex]['email_verification_token'] = '';
                $users[$targetIndex]['verification_token'] = '';
            }
            $users[$targetIndex]['updated_at'] = date('c');
            if (portalSaveUsers($users)) {
                $message = 'Account verification status updated.';
            } else {
                $error = 'Failed to update account verification status.';
            }
        } elseif ($action === 'save_edit') {
            $displayName = trim((string)($_POST['display_name'] ?? ''));
            $role = portalNormalizeUserRole((string)($_POST['role'] ?? ($users[$targetIndex]['role'] ?? 'client')));
            $password = (string)($_POST['new_password'] ?? '');
            $staffNotes = trim((string)($_POST['staff_notes'] ?? ''));

            if (!$isAdmin && $role !== ($users[$targetIndex]['role'] ?? 'client')) {
                $error = 'Only admins can change user roles.';
            } else {
                $users[$targetIndex]['display_name'] = $displayName !== '' ? $displayName : (string)($users[$targetIndex]['username'] ?? 'User');
                $users[$targetIndex]['name'] = $users[$targetIndex]['display_name'];
                $users[$targetIndex]['role'] = $role;
                $users[$targetIndex]['staff_notes'] = $staffNotes;
                if ($password !== '') {
                    $users[$targetIndex]['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
                    $users[$targetIndex]['password'] = '';
                }
                $users[$targetIndex]['updated_at'] = date('c');
                if (portalSaveUsers($users)) {
                    $message = 'User updated.';
                } else {
                    $error = 'Failed to update user.';
                }
            }
        } elseif ($action === 'resend_verification') {
            $targetEmail = (string)($users[$targetIndex]['email'] ?? '');
            $targetName = (string)($users[$targetIndex]['display_name'] ?? $users[$targetIndex]['username'] ?? 'Client');
            $tokenUser = null;
            if ($targetEmail !== '' && portalRefreshVerificationTokenByEmail($targetEmail, $tokenUser) && $tokenUser) {
                $token = (string)($tokenUser['email_verification_token'] ?? $tokenUser['verification_token'] ?? '');
                $verifyUrl = rlsSiteBaseUrl() . '/verify-email.php?token=' . urlencode($token);
                send_verification_email($targetEmail, $targetName, $verifyUrl);
                $message = 'Verification email resent.';
            } else {
                $error = 'Could not resend verification email for this account.';
            }
        }
    }
}

$roleFilterRaw = strtolower(trim((string)($_GET['role'] ?? '')));
$accountStatusFilterRaw = strtolower(trim((string)($_GET['account_status'] ?? '')));
$statusFilterRaw = strtolower(trim((string)($_GET['status'] ?? '')));
$roleFilter = in_array($roleFilterRaw, ['admin', 'staff', 'client'], true) ? $roleFilterRaw : '';
$accountStatusFilter = in_array($accountStatusFilterRaw, ['unverified', 'verified', 'staff_approved', 'rejected'], true) ? $accountStatusFilterRaw : '';
$statusFilter = in_array($statusFilterRaw, ['active', 'disabled'], true) ? $statusFilterRaw : '';
$search = strtolower(trim((string)($_GET['q'] ?? '')));

$users = portalLoadUsers();
$requests = portalLoadProjectRequests();
$requestsByUser = [];
foreach ($requests as $request) {
    $clientUsername = strtolower(trim((string)($request['client_username'] ?? '')));
    if ($clientUsername === '') {
        continue;
    }
    $requestsByUser[$clientUsername] = ($requestsByUser[$clientUsername] ?? 0) + 1;
}

$filteredUsers = [];
foreach ($users as $user) {
    $rowRole = portalNormalizeUserRole((string)($user['role'] ?? 'client'));
    $rowStatus = portalNormalizeUserStatus((string)($user['status'] ?? 'active'));
    $rowAccountStatus = portalNormalizeAccountStatus((string)($user['account_status'] ?? 'unverified'));
    $username = (string)($user['username'] ?? '');
    $email = (string)($user['email'] ?? '');
    $displayName = (string)($user['display_name'] ?? $username);

    if ($roleFilter !== '' && $roleFilter !== $rowRole) {
        continue;
    }
    if ($accountStatusFilter !== '' && $accountStatusFilter !== $rowAccountStatus) {
        continue;
    }
    if ($statusFilter !== '' && $statusFilter !== $rowStatus) {
        continue;
    }
    if ($search !== '') {
        $haystack = strtolower($username . ' ' . $email . ' ' . $displayName);
        if (strpos($haystack, $search) === false) {
            continue;
        }
    }
    $filteredUsers[] = portalNormalizeUserRecord($user);
}

$current_page = 'staff-portal';
$header_class = 'inner-header';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="../assets/images/RL-icon.png">
    <title>User Management | Staff Portal | Runlevel Systems</title>
    <link href="../assets/css/coreloop.css" rel="stylesheet">
    <style>
        .portal-wrap { padding: 40px 0 80px; }
        .portal-back { color: #36f3ff; font-size: 0.9rem; margin-bottom: 18px; display: inline-block; }
        .portal-back:hover { color: #ffc600; }
        .portal-card { background: #0c1729; border: 1px solid rgba(54,243,255,0.18); border-radius: 10px; padding: 24px; margin-bottom: 24px; }
        .portal-card h3 { color: #ffc600; margin-top: 0; }
        label { color: #a8bedc; font-size: 0.9rem; margin-bottom: 4px; display: block; }
        .portal-input,.portal-select,.portal-textarea { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 9px 12px; font-size: 0.95rem; width: 100%; margin-bottom: 14px; }
        .portal-textarea { min-height: 90px; resize: vertical; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 10px 22px; font-weight: 700; font-size: 0.95rem; cursor: pointer; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-btn-sm { background: rgba(54,243,255,0.15); color: #36f3ff; border: 1px solid rgba(54,243,255,0.3); border-radius: 6px; padding: 5px 14px; font-size: 0.85rem; cursor: pointer; font-weight: 600; }
        .portal-btn-sm:hover { background: rgba(54,243,255,0.3); }
        .portal-btn-danger { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; padding: 5px 14px; font-size: 0.85rem; cursor: pointer; }
        .portal-btn-danger:hover { background: rgba(239,68,68,0.3); }
        .user-table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        .user-table th { text-align: left; color: #36f3ff; font-size: 0.85rem; text-transform: uppercase; padding: 8px 10px; border-bottom: 1px solid rgba(54,243,255,0.2); }
        .user-table td { padding: 10px; color: #c7d7e8; border-bottom: 1px solid rgba(54,243,255,0.08); font-size: 0.9rem; vertical-align: top; }
        .badge { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; margin-right: 4px; margin-bottom: 4px; }
        .badge-admin { background: rgba(255,198,0,0.2); color: #ffc600; }
        .badge-staff { background: rgba(54,243,255,0.15); color: #36f3ff; }
        .badge-client { background: rgba(10,132,255,0.2); color: #60a5fa; }
        .badge-active { background: rgba(34,197,94,0.15); color: #86efac; }
        .badge-disabled { background: rgba(239,68,68,0.15); color: #fca5a5; }
        .badge-unverified { background: rgba(251,191,36,0.18); color: #fbbf24; }
        .badge-verified { background: rgba(34,197,94,0.18); color: #86efac; }
        .badge-staff_approved { background: rgba(54,243,255,0.18); color: #36f3ff; }
        .badge-rejected { background: rgba(239,68,68,0.2); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,0.15); border: 1px solid #22c55e; color: #86efac; border-radius: 6px; padding: 12px; margin-bottom: 18px; }
        .alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px; margin-bottom: 18px; }
        .edit-modal { display: none; background: #090f1c; border-radius: 8px; padding: 16px; margin: 6px 0 8px; }
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Back to Dashboard</a>
        <h1 style="color:#ffc600; margin-bottom: 8px;">User Management</h1>
        <p style="color:#7a9ac0; margin-bottom: 24px;">View, verify, approve, disable, and manage client/staff accounts.</p>

        <?php if ($message !== ''): ?><div class="alert-success"><?php echo pe($message); ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert-error"><?php echo pe($error); ?></div><?php endif; ?>

        <div class="portal-card">
            <h3>Filters</h3>
            <form method="get">
                <div class="row">
                    <div class="col-sm-3">
                        <label>Role</label>
                        <select name="role" class="portal-select">
                            <option value="">All roles</option>
                            <option value="client" <?php echo $roleFilter === 'client' ? 'selected' : ''; ?>>Client</option>
                            <option value="staff" <?php echo $roleFilter === 'staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Account Status</label>
                        <select name="account_status" class="portal-select">
                            <option value="">All account statuses</option>
                            <option value="unverified" <?php echo $accountStatusFilter === 'unverified' ? 'selected' : ''; ?>>Unverified</option>
                            <option value="verified" <?php echo $accountStatusFilter === 'verified' ? 'selected' : ''; ?>>Verified</option>
                            <option value="staff_approved" <?php echo $accountStatusFilter === 'staff_approved' ? 'selected' : ''; ?>>Staff Approved</option>
                            <option value="rejected" <?php echo $accountStatusFilter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Login Status</label>
                        <select name="status" class="portal-select">
                            <option value="">All statuses</option>
                            <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="disabled" <?php echo $statusFilter === 'disabled' ? 'selected' : ''; ?>>Disabled</option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <label>Search</label>
                        <input type="text" name="q" class="portal-input" value="<?php echo pe($search); ?>" placeholder="username/email/name">
                    </div>
                </div>
                <button type="submit" class="portal-btn">Apply Filters</button>
            </form>
        </div>

        <?php if ($isAdmin): ?>
        <div class="portal-card">
            <h3>Add New User</h3>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                <div class="row">
                    <div class="col-sm-2"><label>Username</label><input type="text" name="username" class="portal-input" required></div>
                    <div class="col-sm-3"><label>Email</label><input type="email" name="email" class="portal-input" required></div>
                    <div class="col-sm-2"><label>Password</label><input type="password" name="password" class="portal-input" required></div>
                    <div class="col-sm-2"><label>Display Name</label><input type="text" name="display_name" class="portal-input"></div>
                    <div class="col-sm-2"><label>Role</label>
                        <select name="role" class="portal-select">
                            <option value="client">Client</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="portal-btn">Add User</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="portal-card">
            <h3>Portal Users (<?php echo count($filteredUsers); ?>)</h3>
            <?php if (empty($filteredUsers)): ?>
                <p style="color:#5a7a9e;">No users found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="user-table">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Badges</th>
                            <th>Email</th>
                            <th>Projects</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($filteredUsers as $u): ?>
                            <?php
                            $username = (string)($u['username'] ?? '');
                            $rowRole = portalNormalizeUserRole((string)($u['role'] ?? 'client'));
                            $rowStatus = portalNormalizeUserStatus((string)($u['status'] ?? 'active'));
                            $rowAccountStatus = portalNormalizeAccountStatus((string)($u['account_status'] ?? 'unverified'));
                            $projectCount = (int)($requestsByUser[strtolower($username)] ?? 0);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo pe($username); ?></strong><br>
                                    <span style="color:#7a9ac0;"><?php echo pe((string)($u['display_name'] ?? $username)); ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo pe($rowRole); ?>"><?php echo pe(ucfirst($rowRole)); ?></span>
                                    <span class="badge badge-<?php echo pe($rowAccountStatus); ?>"><?php echo pe(ucwords(str_replace('_', ' ', $rowAccountStatus))); ?></span>
                                    <span class="badge badge-<?php echo pe($rowStatus); ?>"><?php echo pe(ucfirst($rowStatus)); ?></span>
                                </td>
                                <td><?php echo pe((string)($u['email'] ?? '—')); ?></td>
                                <td><a href="/staff/estimate-requests.php?username=<?php echo urlencode($username); ?>" style="color:#36f3ff;"><?php echo $projectCount; ?></a></td>
                                <td><?php echo pe(substr((string)($u['created_at'] ?? ''), 0, 10)); ?></td>
                                <td style="white-space: nowrap;">
                                    <button type="button" class="portal-btn-sm" onclick="toggleEditRow('edit-<?php echo pe($username); ?>')">Edit</button>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                                        <input type="hidden" name="target_username" value="<?php echo pe($username); ?>">
                                        <button type="submit" class="portal-btn-danger"><?php echo $rowStatus === 'active' ? 'Disable' : 'Enable'; ?></button>
                                    </form>
                                </td>
                            </tr>
                            <tr id="edit-<?php echo pe($username); ?>" style="display:none;">
                                <td colspan="6" style="padding:0;">
                                    <div class="edit-modal">
                                        <form method="post">
                                            <input type="hidden" name="action" value="save_edit">
                                            <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                                            <input type="hidden" name="target_username" value="<?php echo pe($username); ?>">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label>Display Name</label>
                                                    <input type="text" name="display_name" class="portal-input" value="<?php echo pe((string)($u['display_name'] ?? '')); ?>">
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Role</label>
                                                    <select name="role" class="portal-select">
                                                        <option value="client" <?php echo $rowRole === 'client' ? 'selected' : ''; ?>>Client</option>
                                                        <option value="staff" <?php echo $rowRole === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                                        <option value="admin" <?php echo $rowRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Set Verification</label>
                                                    <select name="set_account_status" class="portal-select" onchange="if(this.value){document.getElementById('status-form-<?php echo pe($username); ?>').querySelector('[name=account_status]').value=this.value;document.getElementById('status-form-<?php echo pe($username); ?>').submit();}">
                                                        <option value="">No change</option>
                                                        <option value="unverified">Unverified</option>
                                                        <option value="verified">Verified</option>
                                                        <option value="staff_approved">Staff Approved</option>
                                                        <option value="rejected">Rejected</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Reset Password</label>
                                                    <input type="password" name="new_password" class="portal-input" placeholder="Optional">
                                                </div>
                                                <div class="col-sm-3">
                                                    <label>Staff Notes</label>
                                                    <textarea name="staff_notes" class="portal-textarea"><?php echo pe((string)($u['staff_notes'] ?? '')); ?></textarea>
                                                </div>
                                            </div>
                                            <button type="submit" class="portal-btn-sm">Save</button>
                                        </form>
                                        <form id="status-form-<?php echo pe($username); ?>" method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="set_account_status">
                                            <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                                            <input type="hidden" name="target_username" value="<?php echo pe($username); ?>">
                                            <input type="hidden" name="account_status" value="">
                                        </form>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="resend_verification">
                                            <input type="hidden" name="csrf_token" value="<?php echo pe(portalGetCsrfToken()); ?>">
                                            <input type="hidden" name="target_username" value="<?php echo pe($username); ?>">
                                            <button type="submit" class="portal-btn-sm">Resend Verification Email</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<script src="../assets/js/jquery-1.12.3.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script>
function toggleEditRow(id) {
    var row = document.getElementById(id);
    if (!row) return;
    row.style.display = (row.style.display === 'none' || row.style.display === '') ? 'table-row' : 'none';
}
</script>
</body>
</html>
