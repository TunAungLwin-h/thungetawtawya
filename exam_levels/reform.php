<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: check_roll_number.php'); 
    exit; 
}

$candidate_id = (int)($_POST['candidate_id'] ?? 0);
$registration_id = $_POST['registration_id'] ?? '';

// Fetch candidate and registration data
$stmt = $pdo->prepare("
    SELECT c.*, r.level, r.roll_number, r.status, r.id as reg_id 
    FROM candidates c 
    JOIN registrations r ON r.candidate_id = c.id 
    WHERE c.id = ? AND r.id = ?
");
$stmt->execute([$candidate_id, $registration_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if data exists and if the status is failed
if (!$data || strtolower($data['status']) !== 'failed') {
    die("Invalid reform target. Only failed records can be processed here.");
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Calculate the next level for the "Upgrade" feature
$current_level = (int)$data['level'];
$next_level = $current_level + 1;
$can_upgrade = ($next_level <= 3); // Assuming level 3 is the max
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reform or Upgrade - Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    body {
        background: #F4ECE1;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .reform-container {
        max-width: 550px;
        margin: 50px auto;
        background: #fff;
        padding: 40px;
        border-radius: 15px;
        border-top: 8px solid #8B4513;
    }

    .stamp {
        border: 3px solid #d4af37;
        color: #d4af37;
        padding: 8px 15px;
        display: inline-block;
        transform: rotate(-5deg);
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        text-align: left;
    }

    .btn-reform {
        background-color: #8B4513;
        color: white;
        border: none;
    }

    .btn-reform:hover {
        background-color: #5D2E0D;
        color: white;
    }

    .upgrade-section {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px dashed #ccc;
    }

    .divider {
        color: #666;
        margin: 20px 0;
        font-style: italic;
        font-size: 0.9rem;
    }
    </style>
</head>

<body>

    <div class="reform-container shadow text-center">
        <div class="stamp">Application Review</div>
        <h2 style="color: #8B4513;">Action Required</h2>
        <p class="text-muted">Current Record: Level <?= htmlspecialchars($data['level']) ?> (Failed)</p>

        <div class="info-card my-4">
            <p class="mb-1 small text-muted text-uppercase">Candidate Name</p>
            <p class="fw-bold mb-3 fs-5"><?= htmlspecialchars($data['name']) ?></p>

            <div class="row">
                <div class="col-6">
                    <p class="mb-0 small text-muted">Current Level</p>
                    <p class="fw-bold">Level <?= htmlspecialchars($data['level']) ?></p>
                </div>
                <div class="col-6">
                    <p class="mb-0 small text-muted">Roll Number</p>
                    <p class="fw-bold"><?= htmlspecialchars($data['roll_number']) ?></p>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h5 class="text-start mb-3"><i class="fa-solid fa-arrows-rotate"></i> Option 1: Re-examine Same Level</h5>
            <form method="POST" action="reform_confirmation.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="candidate_id" value="<?= $data['id'] ?>">
                <input type="hidden" name="registration_id" value="<?= $data['reg_id'] ?>">
                <input type="hidden" name="action_type" value="reform">
                <input type="hidden" name="target_level" value="<?= $data['level'] ?>">

                <button class="btn btn-lg btn-warning w-100 fw-bold shadow-sm">
                    Confirm & Re-take Level <?= $data['level'] ?>
                </button>
            </form>
            <p class="small text-muted mt-2">This will generate a new roll number for the same level.</p>
        </div>

        <?php if ($can_upgrade): ?>
        <div class="upgrade-section">
            <h5 class="text-start mb-3 text-danger"><i class="fa-solid fa-angles-up"></i> Option 2: Manual Level Upgrade
            </h5>
            <form method="POST" action="reform_confirmation.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="candidate_id" value="<?= $data['id'] ?>">
                <input type="hidden" name="registration_id" value="<?= $data['reg_id'] ?>">
                <input type="hidden" name="action_type" value="upgrade">
                <input type="hidden" name="target_level" value="<?= $next_level ?>">

                <button class="btn btn-lg btn-outline-danger w-100 fw-bold">
                    Force Upgrade to Level <?= $next_level ?>
                </button>
            </form>
            <p class="small text-danger mt-2">
                <strong>Warning:</strong> Use this only if the candidate has special permission to skip Level
                <?= $data['level'] ?>.
            </p>
        </div>
        <?php endif; ?>

        <a href="../layouts/register.php" class="btn btn-link text-muted mt-3">Cancel and Go Back</a>
    </div>

</body>

</html>