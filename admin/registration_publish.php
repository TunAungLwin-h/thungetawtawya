<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/superadmin_guard.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }

    $stmt = $pdo->prepare("
    UPDATE registration_publish 
    SET level1 = ?, level2 = ?, level3 = ?,
        result_level1 = ?, result_level2 = ?, result_level3 = ?
    WHERE id = 1
");

$stmt->execute([
    isset($_POST['level1']) ? 1 : 0,
    isset($_POST['level2']) ? 1 : 0,
    isset($_POST['level3']) ? 1 : 0,
    isset($_POST['result_level1']) ? 1 : 0,
    isset($_POST['result_level2']) ? 1 : 0,
    isset($_POST['result_level3']) ? 1 : 0
]);


    $_SESSION['success'] = "Registration publish status updated successfully.";
    header("Location: registration_publish.php");
    exit;
}


$status = $pdo->query(
    "SELECT level1, level2, level3,
            result_level1, result_level2, result_level3
     FROM registration_publish WHERE id = 1"
)->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registration Publish Control</title>

    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    .main-content {
        padding-left: 76px;
        background: #f4f7fb;


    }

    .publish-card {
        max-width: 700px;
        background: #fffdf7;
        border: 1px solid #e6ddcc;
        border-radius: 16px;
        padding: 30px;
        padding-left: 36px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .05);
    }

    .publish-card h2 {
        margin: 0 0 6px;
        color: #5c4324;
    }

    .publish-card p {
        color: #8a7a64;
        margin-bottom: 22px;
    }

    /* ===== Toggle Box ===== */
    .toggle-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border: 1px solid #e3d9c8;
        border-radius: 14px;
        margin-bottom: 16px;
        background: #fffaf2;
        transition: .2s;
    }

    .toggle-box:hover {
        background: #fff6e6;
    }

    .toggle-info h4 {
        margin: 0;
        color: #6b4f2a;
        font-size: 16px;
    }

    .toggle-info small {
        display: inline-block;
        margin-top: 4px;
        font-size: 13px;
    }

    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .badge-on {
        background: #d4edda;
        color: #155724;
    }

    .badge-off {
        background: #f8d7da;
        color: #721c24;
    }

    /* ===== Switch ===== */
    .switch {
        position: relative;
        width: 54px;
        height: 28px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #ccc;
        border-radius: 30px;
        transition: .3s;
    }

    .slider:before {
        content: "";
        position: absolute;
        height: 22px;
        width: 22px;
        left: 3px;
        top: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
    }

    input:checked+.slider {
        background: #6b4f2a;
    }

    input:checked+.slider:before {
        transform: translateX(26px);
    }

    /* ===== Save Button ===== */
    .save-btn {
        margin-top: 24px;
        background: #6b4f2a;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        border: none;
        font-size: 15px;
        cursor: pointer;
    }

    .save-btn:hover {
        background: #5a3f20;
    }

    /* ===== Alert ===== */
    .alert-success {
        background: #d4edda;
        color: #155724;
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 18px;
        border: 1px solid #c3e6cb;
    }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <div class="publish-card">

            <h2><i class="fa-solid fa-toggle-on"></i> Registration Publish Control</h2>
            <p>Only Super Admin can enable or disable registration levels.</p>

            <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <?php
            $levels = [
                'level1' => 'Level 1 Registration',
                'level2' => 'Level 2 Registration',
                'level3' => 'Level 3 Registration'
            ];
            foreach ($levels as $key => $label):
            $enabled = (int)$status[$key] === 1;
            ?>

                <div class="toggle-box">
                    <div class="toggle-info">
                        <h4><?= $label ?></h4>
                        <small class="badge <?= $enabled ? 'badge-on' : 'badge-off' ?>">
                            <?= $enabled ? 'Published' : 'Closed' ?>
                        </small>
                    </div>

                    <label class="switch">
                        <input type="checkbox" name="<?= $key ?>" <?= $enabled ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <?php endforeach; ?>

                <h3 style="margin-top:30px;color:#6b4f2a;">Result Search Control</h3>
                <p style="color:#8a7a64;">Enable or disable result checking page.</p>

                <?php
$resultLevels = [
    'result_level1' => 'Level 1 Result Search',
    'result_level2' => 'Level 2 Result Search',
    'result_level3' => 'Level 3 Result Search'
];

foreach ($resultLevels as $key => $label):
$enabled = (int)$status[$key] === 1;
?>

                <div class="toggle-box">
                    <div class="toggle-info">
                        <h4><?= $label ?></h4>
                        <small class="badge <?= $enabled ? 'badge-on' : 'badge-off' ?>">
                            <?= $enabled ? 'Open' : 'Closed' ?>
                        </small>
                    </div>

                    <label class="switch">
                        <input type="checkbox" name="<?= $key ?>" <?= $enabled ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <?php endforeach; ?>


                <button type="submit" class="save-btn">
                    <i class="fa-solid fa-save"></i> Save Changes
                </button>

            </form>

        </div>

    </main>

</body>

</html>
