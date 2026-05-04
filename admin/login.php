<?php
session_start();
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Thungedawtawya Monastery</title>
    <link rel="stylesheet" href="../public/assets/css/login.css">
    <style>
    .backlink {
        display: inline-block;
        text-decoration: none;
        color: var(--muted);
        font-size: 13px;
        font-weight: 500;
        margin-top: 20px;
        transition: all 0.3s ease;
        opacity: 0.8;
    }

    .backlink:hover {
        color: var(--accent);
        opacity: 1;
        transform: translateX(-3px);
    }
    </style>
</head>

<body>


    <div class="login-wrap">
        <div class="login-card">
            <div class="logo">
                <h1>Thungedawtawya</h1>
                <p>Monastery Exam Registration — Admin</p>
            </div>

            <form action="authenticate.php" method="post" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf); ?>">

                <div class="form-row">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter admin username" required>
                </div>

                <div class="form-row">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Sign In</button>
                </div>
            </form>
            <div class="form-footer-actions">
                <a href="../public/index.php" class="backlink">← Back to Main Website</a>
            </div>
            <div class="card-footer">
                <p>© <?php echo date("Y"); ?> Thungedawtawya Monastery</p>
            </div>
        </div>
    </div>

</body>

</html>