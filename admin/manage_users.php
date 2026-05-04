<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/superadmin_guard.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf_token'];

try {
    // Logic for Create
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            throw new Exception('Invalid request token.');
        }

        $username = trim($_POST['username']);
        $name     = trim($_POST['name']);
        $role     = $_POST['role'] ?? 'admin';
        if (empty($_POST['password'])) { throw new Exception("Password cannot be empty"); }
        if ($username === '' || $name === '') { throw new Exception("Username and full name are required"); }
        if (!in_array($role, ['admin', 'superadmin'], true)) { throw new Exception("Invalid role selected"); }
        
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash, name, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $passwordHash, $name, $role]);
        header("Location: manage_users.php");
        exit;
    }

    // Logic for Delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
            throw new Exception('Invalid request token.');
        }

        $id = (int)$_POST['delete'];
        $currentAdminId = (int)($_SESSION['admin_id'] ?? 0);

        if ($id <= 0) {
            throw new Exception('Invalid admin account selected');
        }

        if ($id === $currentAdminId) {
            throw new Exception('You cannot delete your own account while signed in.');
        }

        $roleStmt = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
        $roleStmt->execute([$id]);
        $targetRole = $roleStmt->fetchColumn();

        if ($targetRole === 'superadmin') {
            $countStmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE role = 'superadmin'");
            if ((int)$countStmt->fetchColumn() <= 1) {
                throw new Exception('At least one super admin account must remain.');
            }
        }

        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: manage_users.php");
        exit;
    }

    $users = $pdo->query("SELECT * FROM admins ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    die("<h2 style='color:red'>Error: " . e($e->getMessage()) . "</h2>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Admins | Super Admin</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    :root {
        --primary: #6b4f2a;
        --primary-light: #f0e6d8;
        --bg-body: #f4efe9;
        --danger: #ef4444;
        --text-dark: #1e293b;
    }

    body {
        font-family: 'Segoe UI', system-ui, sans-serif;
        background: var(--bg-body);
        display: flex;
        margin: 0;
    }

    .main-content {
        flex: 1;
        padding: 40px;
        padding-top: 80px;
        margin-left: 250px;
        /* Aligns with your sidebar */
    }

    .section-card {
        background: #fff;
        padding: 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    h2 {
        color: var(--primary);
        margin-top: 0;
        font-size: 1.5rem;
        border-bottom: 2px solid var(--primary-light);
        padding-bottom: 10px;
    }

    /* Form Styling */
    .user-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .input-group label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
    }

    input,
    select {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        outline: none;
        font-size: 0.95rem;
    }

    input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(107, 79, 42, 0.1);
    }

    .btn-create {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 11px 25px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-create:hover {
        background: #5a4022;
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        text-align: left;
        background: var(--primary-light);
        color: var(--primary);
        padding: 12px;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    td {
        padding: 14px 12px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--text-dark);
    }

    tr:hover {
        background: #fafaf9;
    }

    /* Role Badges */
    .role-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        display: inline-block;
    }

    .role-superadmin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-admin {
        background: #dcfce7;
        color: #166534;
    }

    .btn-del {
        color: var(--danger);
        background: transparent;
        border: none;
        text-decoration: none;
        font-size: 1.1rem;
        transition: 0.2s;
        cursor: pointer;
    }

    .btn-del:hover {
        color: #b91c1c;
        opacity: 0.7;
    }
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <?php include 'header.php'; ?>

        <div class="section-card">
            <h2><i class="fa-solid fa-user-plus"></i> Create New Admin</h2>
            <form method="post" class="user-form">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="e.g. john_doe" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="input-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="superadmin">Super Admin</option>
                    </select>
                </div>
                <button type="submit" name="create" class="btn-create">Create User</button>
            </form>
        </div>

        <div class="section-card">
            <h2><i class="fa-solid fa-users-gear"></i> System Accounts</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?= e($u['name']) ?></strong></td>
                        <td style="color:#64748b;"><?= e($u['username']) ?></td>
                        <td>
                            <span class="role-badge role-<?= $u['role'] ?>">
                                <?= strtoupper(e($u['role'])) ?>
                            </span>
                        </td>
                        <td style="font-size: 0.9rem; color:#94a3b8;">
                            <?= date('M d, Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td style="text-align: right;">
                            <?php if ((int)$u['id'] !== (int)($_SESSION['admin_id'] ?? 0)): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                                <button class="btn-del" type="submit" name="delete" value="<?= (int)$u['id'] ?>"
                                    onclick="return confirm('Permanently delete this account?')" title="Delete Account">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <small style="color:#cbd5e1;">(You)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>

</html>
