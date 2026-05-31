<?php
/**
 * Staff user management page.
 * Allows viewing, adding, editing, and disabling portal users.
 * TODO: Move users to database later.
 * TODO: Replace plaintext passwords with password_hash/password_verify before production.
 */
session_start();
define('WDS_SYSTEM', true);
require_once __DIR__ . '/../includes/portal-helpers.php';

// Only admins can manage users
portalRequireStaff(['admin']);

$message = '';
$error   = '';
$editUser = null;

// ---- Handle form actions ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $uname  = trim($_POST['username'] ?? '');
        $pass   = $_POST['password'] ?? '';
        $role   = $_POST['role'] ?? 'staff';
        $dname  = trim($_POST['display_name'] ?? '');

        if ($uname === '' || $pass === '') {
            $error = 'Username and password are required.';
        } elseif (!preg_match('/^[a-zA-Z0-9._-]+$/', $uname)) {
            $error = 'Username may only contain letters, numbers, dot, underscore, or dash.';
        } elseif (!in_array($role, ['admin', 'staff', 'client'], true)) {
            $error = 'Invalid role.';
        } else {
            $users = portalLoadUsers();
            foreach ($users as $u) {
                if (strtolower($u['username'] ?? '') === strtolower($uname)) {
                    $error = 'That username already exists.';
                    break;
                }
            }
            if ($error === '') {
                // TODO: Replace plaintext passwords with password_hash before production.
                $users[] = [
                    'username'     => $uname,
                    'password'     => $pass,
                    'role'         => $role,
                    'status'       => 'active',
                    'display_name' => $dname !== '' ? $dname : $uname,
                    'created_at'   => date('c'),
                ];
                if (portalSaveUsers($users)) {
                    $message = 'User added successfully.';
                } else {
                    $error = 'Failed to save user data.';
                }
            }
        }
    } elseif ($action === 'toggle_status') {
        $uname  = trim($_POST['target_username'] ?? '');
        $users  = portalLoadUsers();
        $found  = false;
        foreach ($users as &$u) {
            if (strtolower($u['username'] ?? '') === strtolower($uname)) {
                $u['status'] = ($u['status'] === 'active') ? 'disabled' : 'active';
                $found = true;
                break;
            }
        }
        unset($u);
        if ($found) {
            portalSaveUsers($users);
            $message = 'User status updated.';
        } else {
            $error = 'User not found.';
        }
    } elseif ($action === 'save_edit') {
        $uname = trim($_POST['target_username'] ?? '');
        $pass  = $_POST['new_password'] ?? '';
        $role  = $_POST['role'] ?? '';
        $dname = trim($_POST['display_name'] ?? '');

        $users = portalLoadUsers();
        $found = false;
        foreach ($users as &$u) {
            if (strtolower($u['username'] ?? '') === strtolower($uname)) {
                if (!in_array($role, ['admin', 'staff', 'client'], true)) {
                    $error = 'Invalid role.';
                    break;
                }
                $u['role']         = $role;
                $u['display_name'] = $dname !== '' ? $dname : $u['username'];
                // TODO: Replace plaintext passwords with password_hash before production.
                if ($pass !== '') {
                    $u['password'] = $pass;
                }
                $found = true;
                break;
            }
        }
        unset($u);
        if ($found && $error === '') {
            portalSaveUsers($users);
            $message = 'User updated.';
        } elseif (!$found) {
            $error = 'User not found.';
        }
    }
}

// Load after modifications
$users = portalLoadUsers();
$currentStaff = portalGetStaffUser();

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
        .portal-input { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 9px 12px; font-size: 0.95rem; width: 100%; margin-bottom: 14px; }
        .portal-select { background: #09111d; color: #eaf3ff; border: 1px solid rgba(54,243,255,0.25); border-radius: 6px; padding: 9px 12px; font-size: 0.95rem; width: 100%; margin-bottom: 14px; }
        .portal-btn { background: #ffc600; color: #08111f; border: none; border-radius: 6px; padding: 10px 22px; font-weight: 700; font-size: 0.95rem; cursor: pointer; }
        .portal-btn:hover { background: #36f3ff; }
        .portal-btn-sm { background: rgba(54,243,255,0.15); color: #36f3ff; border: 1px solid rgba(54,243,255,0.3); border-radius: 6px; padding: 5px 14px; font-size: 0.85rem; cursor: pointer; font-weight: 600; }
        .portal-btn-sm:hover { background: rgba(54,243,255,0.3); }
        .portal-btn-danger { background: rgba(239,68,68,0.15); color: #fca5a5; border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; padding: 5px 14px; font-size: 0.85rem; cursor: pointer; }
        .portal-btn-danger:hover { background: rgba(239,68,68,0.3); }
        .user-table { width: 100%; border-collapse: collapse; }
        .user-table th { text-align: left; color: #36f3ff; font-size: 0.85rem; text-transform: uppercase; padding: 8px 10px; border-bottom: 1px solid rgba(54,243,255,0.2); }
        .user-table td { padding: 10px; color: #c7d7e8; border-bottom: 1px solid rgba(54,243,255,0.08); font-size: 0.9rem; vertical-align: middle; }
        .badge-role { display: inline-block; padding: 2px 9px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; }
        .badge-admin { background: rgba(255,198,0,0.2); color: #ffc600; }
        .badge-staff { background: rgba(54,243,255,0.15); color: #36f3ff; }
        .badge-client { background: rgba(10,132,255,0.2); color: #60a5fa; }
        .badge-active { background: rgba(34,197,94,0.15); color: #86efac; }
        .badge-disabled { background: rgba(239,68,68,0.15); color: #fca5a5; }
        .alert-success { background: rgba(34,197,94,0.15); border: 1px solid #22c55e; color: #86efac; border-radius: 6px; padding: 12px; margin-bottom: 18px; }
        .alert-error { background: rgba(239,68,68,0.18); border: 1px solid #ef4444; color: #fecaca; border-radius: 6px; padding: 12px; margin-bottom: 18px; }
        .edit-modal { display: none; background: #0c1729; border: 1px solid rgba(255,198,0,0.3); border-radius: 10px; padding: 22px; margin-top: 12px; }
        .notice-box { background: rgba(255,198,0,0.07); border: 1px solid rgba(255,198,0,0.2); border-radius: 8px; padding: 12px 16px; margin-bottom: 24px; color: #c7a800; font-size: 0.875rem; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/navigation.php'; ?>

<section class="portal-wrap">
    <div class="container">
        <a href="/staff/dashboard.php" class="portal-back">← Back to Dashboard</a>
        <h1 style="color:#ffc600; margin-bottom: 8px;">👥 User Management</h1>
        <p style="color:#7a9ac0; margin-bottom: 24px;">Manage staff and client portal users. Admin access only.</p>

        <div class="notice-box">
            ⚠️ <strong>Temporary system.</strong> Passwords are stored in plaintext in <code>data/users.json</code>.
            TODO: Replace with password_hash/password_verify before production. TODO: Move users to database later.
        </div>

        <?php if ($message !== ''): ?><div class="alert-success"><?php echo pe($message); ?></div><?php endif; ?>
        <?php if ($error !== ''): ?><div class="alert-error"><?php echo pe($error); ?></div><?php endif; ?>

        <!-- Add User -->
        <div class="portal-card">
            <h3>Add New User</h3>
            <form method="post">
                <input type="hidden" name="action" value="add">
                <div class="row" style="gap: 12px;">
                    <div style="flex: 1 1 160px;">
                        <label>Username</label>
                        <input type="text" name="username" class="portal-input" required placeholder="e.g. rls-staff">
                    </div>
                    <div style="flex: 1 1 160px;">
                        <label>Password<!-- TODO: Replace plaintext with password_hash --></label>
                        <input type="text" name="password" class="portal-input" required placeholder="Temporary plaintext">
                    </div>
                    <div style="flex: 1 1 140px;">
                        <label>Display Name</label>
                        <input type="text" name="display_name" class="portal-input" placeholder="Full name">
                    </div>
                    <div style="flex: 0 0 130px;">
                        <label>Role</label>
                        <select name="role" class="portal-select">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="portal-btn">Add User</button>
            </form>
        </div>

        <!-- User Table -->
        <div class="portal-card">
            <h3>All Portal Users</h3>
            <?php if (empty($users)): ?>
                <p style="color:#5a7a9e;">No users found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Display Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong><?php echo pe($u['username'] ?? '—'); ?></strong></td>
                                    <td><?php echo pe($u['display_name'] ?? '—'); ?></td>
                                    <td>
                                        <?php $role = $u['role'] ?? 'staff'; ?>
                                        <span class="badge-role badge-<?php echo pe($role); ?>"><?php echo pe(ucfirst($role)); ?></span>
                                    </td>
                                    <td>
                                        <?php $status = $u['status'] ?? 'active'; ?>
                                        <span class="badge-role badge-<?php echo pe($status); ?>"><?php echo pe(ucfirst($status)); ?></span>
                                    </td>
                                    <td><?php echo pe(substr($u['created_at'] ?? '—', 0, 10)); ?></td>
                                    <td style="white-space: nowrap;">
                                        <button type="button" class="portal-btn-sm"
                                                onclick="toggleEditRow('edit-<?php echo pe($u['username'] ?? ''); ?>')">Edit</button>
                                        <?php if (strtolower($u['username'] ?? '') !== strtolower($currentStaff['username'] ?? '')): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="target_username" value="<?php echo pe($u['username'] ?? ''); ?>">
                                                <button type="submit" class="portal-btn-danger"
                                                        onclick="return confirm('Toggle status for <?php echo pe($u['username'] ?? ''); ?>?')">
                                                    <?php echo ($u['status'] ?? 'active') === 'active' ? 'Disable' : 'Enable'; ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr id="edit-<?php echo pe($u['username'] ?? ''); ?>" style="display:none;">
                                    <td colspan="6" style="padding: 0;">
                                        <div style="background:#090f1c; border-radius:8px; padding:18px; margin:4px 0 8px;">
                                            <form method="post">
                                                <input type="hidden" name="action" value="save_edit">
                                                <input type="hidden" name="target_username" value="<?php echo pe($u['username'] ?? ''); ?>">
                                                <div class="row" style="gap:12px; align-items:flex-end;">
                                                    <div style="flex:1 1 160px;">
                                                        <label>New Password (leave blank to keep)</label>
                                                        <input type="text" name="new_password" class="portal-input" placeholder="Leave blank to keep">
                                                    </div>
                                                    <div style="flex:1 1 140px;">
                                                        <label>Display Name</label>
                                                        <input type="text" name="display_name" class="portal-input" value="<?php echo pe($u['display_name'] ?? ''); ?>">
                                                    </div>
                                                    <div style="flex:0 0 130px;">
                                                        <label>Role</label>
                                                        <select name="role" class="portal-select">
                                                            <option value="admin" <?php echo ($u['role'] ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                            <option value="staff" <?php echo ($u['role'] ?? '') === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                                            <option value="client" <?php echo ($u['role'] ?? '') === 'client' ? 'selected' : ''; ?>>Client</option>
                                                        </select>
                                                    </div>
                                                    <div style="flex:0 0 auto; padding-bottom:14px;">
                                                        <button type="submit" class="portal-btn">Save</button>
                                                    </div>
                                                </div>
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
