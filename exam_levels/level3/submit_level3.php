<?php
session_start();
$db_file = __DIR__ . '/../../configuration/db.php';
if (!file_exists($db_file)) {
    die("Database configuration file not found at: $db_file");
}
require_once $db_file;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($pdo) || !($pdo instanceof PDO)) {
    error_log("PDO connection not established");
    $_SESSION['flash'] = 'Database connection failed';
    header('Location: level3.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = 'Invalid request';
    header('Location: level3.php');
    exit;
}

// CSRF validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['flash'] = 'Session expired. Please refresh the page.';
    header('Location: level3.php');
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
    $_SESSION['flash'] = 'Invalid CSRF token';
    header('Location: level3.php');
    exit;
}

$roll = trim((string)($_POST['roll_number'] ?? ''));
$exam_year = (int)($_POST['exam_year'] ?? 0);

if ($roll === '' || $exam_year <= 0) {
    $_SESSION['flash'] = 'Please provide roll number and exam year';
    header('Location: level3.php');
    exit;
}

try {
    error_log("Searching for Level 2 registration with roll: $roll");

    $test = $pdo->query("SELECT 1");
    if (!$test) {
        throw new Exception("Database connection test failed");
    }

    $checkFields = $pdo->query("DESCRIBE registrations");
    $fields = $checkFields->fetchAll(PDO::FETCH_COLUMN);
    error_log("Fields in registrations table: " . implode(', ', $fields));

    $stmt = $pdo->prepare("
        SELECT 
            r.id,
            r.candidate_id,
            r.level,
            r.roll_number,
            r.status,
            r.passed_year,
            r.registration_year,
            c.name AS candidate_name,
            c.dob,
            c.address_region,
            c.address_township,
            c.address_village,
            c.father_name,
            c.mother_name
        FROM registrations r
        JOIN candidates c ON c.id = r.candidate_id
        WHERE r.roll_number = :roll
          AND r.level = 2
        LIMIT 1
    ");

    $stmt->execute([':roll' => $roll]);
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reg) {
        error_log("No registration found for Level 2 roll: $roll. Trying candidate.roll_number.");

        $stmt = $pdo->prepare("
            SELECT 
                r.id,
                r.candidate_id,
                r.level,
                r.roll_number,
                r.status,
                r.passed_year,
                r.registration_year,
                c.name AS candidate_name,
                c.dob,
                c.address_region,
                c.address_township,
                c.address_village,
                c.father_name,
                c.mother_name
            FROM registrations r
            JOIN candidates c ON c.id = r.candidate_id
            WHERE c.roll_number = :roll
              AND r.level = 2
            LIMIT 1
        ");

        $stmt->execute([':roll' => $roll]);
        $reg = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$reg) {
        $_SESSION['flash'] = "No Level 2 registration found for roll: " . htmlspecialchars($roll);
        header('Location: level3.php');
        exit;
    }

    error_log("Found registration: " . print_r($reg, true));
    // Determine passed status using status field
    $isPassed = ($reg['status'] === 'passed');
    $regPassedYear = $reg['passed_year'] ?? null;

} catch (Exception $e) {
    error_log("submit_level3 error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    error_log("Stack trace: " . $e->getTraceAsString());

    $_SESSION['flash'] = 'Server error: ' . htmlspecialchars($e->getMessage());
    header('Location: level3.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Level 3 — Search Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/level2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    body {
        background-image: image-set(url('../public/assets/images/14.jpg'));
        background-repeat: no-repeat;
        background-size: cover;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
        color: #4b3f2f;
        margin: 0;
        min-height: 100vh;
    }

    .container {
        max-width: 900px;
        margin: 40px auto 24px auto;
        background: rgba(255, 250, 242, 0.96);
        border-radius: 24px;
        box-shadow: 0 10px 35px rgba(90, 75, 44, 0.14), 0 2px 2px rgba(124, 94, 59, 0.07);
        padding: 36px 32px 24px 32px;
    }

    .back-buttons {
        margin-bottom: 26px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
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
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #7c5e3b;
        color: white;
        transition: background 0.13s, border-color 0.13s, color 0.13s;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #b7995d;
        color: #b7995d;
    }

    .btn-outline:active,
    .btn-outline:focus,
    .btn-outline:hover {
        background: #f7eed7;
        color: #7c5e3b;
        border-color: #c3a267;
    }

    .btn-secondary {
        background: #e8ddce;
        color: #46351b;
        border: 1.5px solid #99733c;
    }

    .btn-secondary:hover,
    .btn-secondary:active,
    .btn-secondary:focus {
        background: #fbeedb;
        color: #7c5e3b;
        border-color: #7c5e3b;
    }

    .card {
        background: #fcf1e2;
        border-radius: 16px;
        box-shadow: 0 9px 34px rgba(0, 0, 0, .07);
        padding: 30px 26px 23px 26px;
        margin-bottom: 22px;
    }

    h2 {
        color: #7c5e3b;
        margin-bottom: 25px;
        font-weight: 700;
        letter-spacing: .01em;
        border-bottom: 2px solid #e7d8be;
        padding-bottom: 6px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
        margin: 25px 0;
    }

    .info-box {
        background: #fdf7ea;
        border-radius: 10px;
        padding: 24px 16px 13px 16px;
        color: #372d1a;
        box-shadow: 0 3px 18px rgba(192, 177, 143, 0.055);
    }

    .info-box h4 {
        color: #916b36;
        margin-bottom: 11px;
        font-size: 18px;
        font-weight: 700;
    }

    .info-box p {
        margin: 8px 0;
        padding: 5.5px 0;
        border-bottom: 1px dashed #eace94;
        font-size: 15px;
    }

    .info-box p:last-child {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 5px 18px;
        border-radius: 22px;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.03em;
        margin-top: 4px;
        margin-bottom: 2px;
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

    .badge.failed,
    .badge.rejected {
        background: #f6cac8;
        color: #871f12;
    }

    .badge.approved {
        background: #eaddbb;
        color: #99733c;
    }

    .alert {
        padding: 16px 20px;
        border-radius: 11px;
        margin: 22px 0 16px 0;
        font-size: 16px;
        border: none;
        box-shadow: 0 2px 15px rgba(124, 94, 59, 0.05);
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffe16c;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border-left: 4px solid #ec6363;
    }

    .muted {
        color: #96896b;
    }

    .reform-form {
        margin-top: 25px;
        padding-top: 18px;
        border-top: 1.5px solid #f0e1c7;
    }

    .btn-primary {
        background: #7c5e3b;
        color: #fff;
        border-radius: 22px;
        border: none;
        font-size: 17px;
        font-weight: 700;
        padding: 12px 26px;
        margin-top: 6px;
        cursor: pointer;
        transition: background 0.13s;
    }

    .btn-primary:hover {
        background: #543d26;
    }

    .info,
    .debug-info {
        color: #96896b;
        font-size: 13px;
        margin-top: 8px;
    }

    @media (max-width:700px) {
        .container {
            padding: 6vw 2vw;
        }

        .card {
            padding: 16px 2vw;
        }

        .info-grid {
            gap: 12px;
        }

        h2 {
            font-size: 1.1em;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Back buttons -->
        <div class="back-buttons">
            <a href="level3.php" class="btn btn-outline">
                <i class="fa-solid fa-arrow-left"></i> Back to Search
            </a>
            <a href="../../layouts/register.php" class="btn btn-secondary">
                <i class="fa-solid fa-home"></i> Back to Home
            </a>
        </div>
        <div class="card">
            <h2>
                <i class="fa-solid fa-search"></i>
                Search Result for Level 2 Roll: <?= htmlspecialchars($roll) ?>
            </h2>
            <div class="info-grid">
                <div class="info-box">
                    <h4><i class="fas fa-user"></i> Candidate Information</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($reg['candidate_name'] ?? '-') ?></p>
                    <p><strong>Date of Birth:</strong> <?= htmlspecialchars($reg['dob'] ?? '-') ?></p>
                    <p><strong>Address:</strong>
                        <?= htmlspecialchars(($reg['address_village'] ?? '') . ', ' . 
                                           ($reg['address_township'] ?? '') . ', ' . 
                                           ($reg['address_region'] ?? '')) ?>
                    </p>
                    <p><strong>Father:</strong> <?= htmlspecialchars($reg['father_name'] ?? '-') ?></p>
                    <p><strong>Mother:</strong> <?= htmlspecialchars($reg['mother_name'] ?? '-') ?></p>
                </div>

                <div class="info-box">
                    <h4><i class="fas fa-file-alt"></i> Registration Information</h4>
                    <p><strong>Candidate ID:</strong> <?= (int)($reg['candidate_id'] ?? 0) ?></p>
                    <p><strong>Registration ID:</strong> <?= htmlspecialchars($reg['id'] ?? '-') ?></p>
                    <p><strong>Level:</strong> 2</p>
                    <p><strong>Roll Number:</strong> <?= htmlspecialchars($reg['roll_number'] ?? '-') ?></p>
                    <p><strong>Status:</strong>
                        <span class="badge <?= htmlspecialchars($reg['status'] ?? '') ?>">
                            <?= ucfirst($reg['status'] ?? '-') ?>
                        </span>
                    </p>
                    <p><strong>Passed Year:</strong>
                        <?= $regPassedYear ? (int)$regPassedYear : '<span class="muted">Not set</span>' ?>
                    </p>
                    <p><strong>Exam Year Provided:</strong> <?= (int)$exam_year ?></p>
                </div>
            </div>
            <div class="reform-form">
                <?php if ($isPassed): ?>
                <?php if ($regPassedYear && $regPassedYear != $exam_year): ?>
                <div class="alert alert-warning">
                    <strong><i class="fas fa-exclamation-triangle"></i> Note:</strong>
                    Passed year in our record (<?= $regPassedYear ?>)
                    differs from the year you entered (<?= $exam_year ?>).
                </div>
                <?php endif; ?>
                <!-- Reform to Level 3 form -->
                <form method="post" action="reform_to_level3.php">
                    <input type="hidden" name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="candidate_id" value="<?= (int)$reg['candidate_id'] ?>">
                    <input type="hidden" name="from_registration_id" value="<?= htmlspecialchars($reg['id'] ?? '') ?>">
                    <input type="hidden" name="exam_year" value="<?= (int)$exam_year ?>">
                    <input type="hidden" name="target_level" value="3">
                    <input type="hidden" name="registration_year" value="<?= date('Y') ?>">
                    <button class="btn-primary" type="submit">
                        <i class="fa-solid fa-graduation-cap"></i> Reform to Level 3
                    </button>
                    <div class="info">
                        <i class="fas fa-info-circle"></i>
                        This will create a new Level 3 registration using your existing information.
                    </div>
                </form>
                <?php else: ?>
                <div class="alert danger">
                    <i class="fa-solid fa-circle-xmark"></i>
                    <strong>Status: Failed.</strong> This candidate did not pass Level 2.
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
                    <a href="level3.php" class="btn-ghost">
                        <i class="fa-solid fa-search"></i> Search Again
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Debug information (remove in production) -->
        <div class="debug-info">
            <p><strong>Debug Info:</strong> PHP <?= phpversion() ?>, PDO: <?= class_exists('PDO') ? 'Yes' : 'No' ?></p>
            <p>Found registration ID: <?= $reg['id'] ?? 'None' ?></p>
        </div>
    </div>
</body>

</html>