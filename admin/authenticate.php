<?php
session_start();
include('../configuration/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// CSRF check
$token = $_POST['csrf_token'] ?? '';
if (!$token || !isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
    $_SESSION['auth_error'] = 'Invalid request.';
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['auth_error'] = 'Username and password are required.';
    header('Location: login.php');
    exit;
}

// Fetch admin
$stmt = $pdo->prepare(
    "SELECT id, username, password_hash, name, role 
     FROM admins 
     WHERE username = :u 
     LIMIT 1"
);
$stmt->execute([':u' => $username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin || !password_verify($password, $admin['password_hash'])) {
    $_SESSION['auth_error'] = 'Invalid credentials.';
    header('Location: login.php');
    exit;
}

// Security
session_regenerate_id(true);

$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id']   = $admin['id'];
$_SESSION['admin_name'] = $admin['name'];
$_SESSION['admin_role'] = $admin['role']; // admin | superadmin

header('Location: dashboard.php');
exit;