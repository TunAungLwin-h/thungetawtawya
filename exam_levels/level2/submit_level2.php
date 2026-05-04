<?php
// public/submit_level2.php
session_start();
require_once __DIR__ . '/../../configuration/db.php';

// CSRF token check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = 'Invalid request';
    header('Location: level2.php');
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    $_SESSION['flash'] = 'Invalid CSRF token';
    header('Location: level2.php');
    exit;
}

$roll = trim((string)($_POST['roll_number'] ?? ''));
$exam_year = (int)($_POST['exam_year'] ?? 0);

if ($roll === '' || $exam_year <= 0) {
    $_SESSION['flash'] = 'Please provide roll number and exam year';
    header('Location: level2.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
    SELECT r.*, c.name AS candidate_name, c.id AS candidate_id,
           c.dob, c.address_region, c.address_township, c.address_village,
           c.father_name, c.mother_name, c.monastery_name, c.abbot_name
    FROM registrations r
    JOIN candidates c ON c.id = r.candidate_id
    WHERE r.roll_number = :roll
      AND r.level = 1
    ORDER BY r.created_at DESC
    LIMIT 1
");

    $stmt->execute([':roll' => $roll]);
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$reg) {
        $message = "No Level 1 registration found for roll " . htmlspecialchars($roll);
        $_SESSION['flash'] = $message;
        header('Location: level2.php');
        exit;
    }

    // Determine passed status using passed_year field
    $isPassed = ($reg['status'] === 'passed');
    $regPassedYear = $reg['passed_year'] ?? null;

} catch (Exception $e) {
    error_log("submit_level2 error: " . $e->getMessage());
    $_SESSION['flash'] = 'Server error, please try again later';
    header('Location: level2.php');
    exit;
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Level 2 — Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/level2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    /* MONASTERY THEME */
    body {
        background-image: image-set(url('../public/assets/images/14.jpg'));
        background-repeat: no-repeat;
        background-size: cover;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
        color: #4b3f2f;
        min-height: 100vh;
        margin: 0;
    }

    .container {
        max-width: 880px;
        margin: 48px auto;
        background: rgba(255, 250, 242, 0.96);
        border-radius: 24px;
        box-shadow: 0 10px 35px rgba(90, 75, 44, 0.12), 0 2px 2px rgba(124, 94, 59, 0.07);
        padding: 36px 32px 24px 32px;
    }

    h2 {
        text-align: center;
        color: #7c5e3b;
        margin: 0 0 28px 0;
        font-weight: 800;
        letter-spacing: .03em;
    }

    .card {
        background: rgba(252, 236, 207, 0.98);
        border-radius: 18px;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.13);
        padding: 35px 30px 28px 30px;
        margin: 0;
    }

    .badge {
        display: inline-block;
        padding: 5px 18px;
        border-radius: 22px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.03em;
        margin-top: 4px;
        margin-bottom: 4px;
        background: #e4d1b0;
        color: #6e522b;
    }

    .badge.passed {
        background: #d0e6b8;
        color: #436135;
    }

    .badge.pending {
        background: #fbf5da;
        color: #b89c45;
    }

    .badge.failed {
        background: #f6cac8;
        color: #871f12;
    }

    .btn,
    .btn-ghost {
        padding: 10px 22px;
        border-radius: 22px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        margin-right: 10px;
        background: #7c5e3b;
        color: white;
        transition: background 0.13s, border-color 0.13s, color 0.13s;
    }

    .btn.primary {
        background: #7c5e3b;
        color: #fff;
        border: none;
    }

    .btn:active,
    .btn:focus,
    .btn:hover {
        background: #4b3f2f;
    }

    .btn-ghost {
        background: transparent;
        border: 1px solid #b7995d;
        color: #b7995d;
        margin-bottom: 20px;
    }

    .btn-ghost:active,
    .btn-ghost:focus,
    .btn-ghost:hover {
        background: #f7eed7;
        color: #7c5e3b;
        border-color: #c3a267;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin: 22px 0 16px 0;
        font-size: 16px;
        border: none;
        box-shadow: 0 3px 20px rgba(124, 94, 59, 0.03);
    }

    .alert.warning {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffe16c;
    }

    .alert.danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #ec6363;
    }

    .muted {
        color: #96896b;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(282px, 1fr));
        gap: 32px;
        margin: 32px 0 14px 0;
    }

    .info-box {
        padding: 22px 18px 16px 18px;
        background: #fff;
        border-radius: 13px;
        color: #3e2b12;
        margin-bottom: 0;
        box-shadow: 0 3px 12px rgba(192, 177, 143, 0.07);
    }

    .info-box h4 {
        color: #916b36;
        margin: 0 0 6px 0;
        font-weight: 700;
        letter-spacing: .02em;
    }

    @media (max-width:700px) {
        .container {
            padding: 6vw 2vw;
        }

        .card {
            padding: 20px 4vw;
        }

        .info-grid {
            gap: 12px;
        }
    }
    </style>
</head>

<body>

    <div class="container">
        <!-- Back buttons -->
        <div style="margin-bottom: 30px;">
            <a href="level2.php" class="btn-ghost" style="margin-right:10px">
                <i class="fa-solid fa-arrow-left"></i> Back to Search
            </a>
            <a href="../../public/index.php" class="btn-ghost">
                <i class="fa-solid fa-home"></i> Back to Home
            </a>
        </div>

        <div class="card">
            <h2>
                <i class="fa-solid fa-user-graduate"></i>
                Search Result for Roll: <?= htmlspecialchars($roll) ?>
            </h2>

            <div class="info-grid">
                <div class="info-box">
                    <h4>Candidate Information</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($reg['candidate_name'] ?? '-') ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($reg['email'] ?? '-') ?></p>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($reg['dob'] ?? '-') ?></p>
                    <p><strong>Address:</strong>
                        <?= htmlspecialchars(($reg['address_village'] ?? '') . ', ' . ($reg['address_township'] ?? '') . ', ' . ($reg['address_region'] ?? '')) ?>
                    </p>
                    <p><strong>Father:</strong> <?= htmlspecialchars($reg['father_name'] ?? '-') ?></p>
                    <p><strong>Mother:</strong> <?= htmlspecialchars($reg['mother_name'] ?? '-') ?></p>
                    <p><strong>Monastery:</strong> <?= htmlspecialchars($reg['monastery_name'] ?? '-') ?></p>
                    <p><strong>Abbot:</strong> <?= htmlspecialchars($reg['abbot_name'] ?? '-') ?></p>
                </div>

                <div class="info-box">
                    <h4>Registration Information</h4>
                    <p><strong>Candidate ID:</strong> <?= (int)($reg['candidate_id'] ?? 0) ?></p>
                    <p><strong>Registration ID:</strong> <?= htmlspecialchars($reg['id'] ?? '-') ?></p>
                    <p><strong>Level:</strong> 1</p>
                    <p><strong>Status:</strong>
                        <span class="badge <?= htmlspecialchars($reg['status'] ?? '') ?>">
                            <?= ucfirst($reg['status'] ?? '-') ?>
                        </span>
                    </p>
                    <p><strong>Passed Year (Record):</strong>
                        <?= $regPassedYear ? (int)$regPassedYear : '<span class="muted">Not set</span>' ?>
                    </p>
                    <p><strong>Exam Year Provided:</strong> <?= (int)$exam_year ?></p>
                </div>
            </div>

            <div style="margin-top: 22px;">
                <?php if ($isPassed): ?>
                <?php if ($regPassedYear && $regPassedYear != $exam_year): ?>
                <div class="alert warning">
                    <strong>Note:</strong> Passed year in our record (<?= $regPassedYear ?>)
                    differs from the year you entered (<?= $exam_year ?>).
                </div>
                <?php endif; ?>
                <form method="post" action="reform_level.php">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="candidate_id" value="<?= (int)$reg['candidate_id'] ?>">
                    <input type="hidden" name="from_registration_id" value="<?= htmlspecialchars($reg['id'] ?? '') ?>">
                    <input type="hidden" name="exam_year" value="<?= (int)$exam_year ?>">
                    <input type="hidden" name="target_level" value="2">
                    <input type="hidden" name="registration_year" value="<?= date('Y') ?>">

                    <button class="btn primary" type="submit">
                        <i class="fa-solid fa-repeat"></i> Reform to Level 2
                    </button>
                    <small style="display: block; margin-top: 8px; color: #967f4a;">
                        This will create a new Level 2 registration using your existing information.
                    </small>
                </form>

                <?php else: ?>
                <div class="alert danger">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <strong>Status: Failed.</strong> This candidate did not pass Level 1.
                </div>

                <div style="margin-top: 20px;">
                    <form method="POST" action="../reform.php">
                        <input type="hidden" name="candidate_id" value="<?= (int)$reg['candidate_id'] ?>">
                        <input type="hidden" name="registration_id" value="<?= htmlspecialchars($reg['id']) ?>">

                        <button class="btn primary" type="submit">
                            <i class="fa-solid fa-gears"></i> Manage Application
                        </button>
                    </form>
                    <p class="muted" style="font-size: 13px; margin-top: 10px;">
                        Click 'Manage' to either re-register for Level 1 or upgrade to Level 2.
                    </p>
                </div>

                <div style="margin-top: 18px;">
                    <a href="level2.php" class="btn-ghost">
                        <i class="fa-solid fa-search"></i> Search Again
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>