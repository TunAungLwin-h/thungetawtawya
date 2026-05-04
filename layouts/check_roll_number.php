<?php
session_start();
require_once '../configuration/db.php';

date_default_timezone_set('Asia/Yangon');
try {
    $pdo->exec("SET time_zone = '+06:30'");
} catch (Throwable $e) {}

$error = '';
$info  = '';
$candidate = null;
$resultFound = false;
$current_year = (int)date('Y');
$selected_year = $current_year;

function isPublished(PDO $pdo, int $level, int $year): bool
{
    $stmt = $pdo->prepare("
        SELECT is_active
        FROM roll_publishings
        WHERE level = ?
          AND year = ?
        ORDER BY year DESC
        LIMIT 1
    ");
    $stmt->execute([$level, $year]);
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pub) {
        return false;
    }

    return ((int)$pub['is_active'] === 1);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(trim($_POST['email'] ?? ''));
    $level = (int)($_POST['level'] ?? 1);
    $selected_year = (int)($_POST['year'] ?? $current_year);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($selected_year < 2020 || $selected_year > ($current_year + 1)) {
        $error = 'Please enter a valid exam year.';
    } else {

        if (!isPublished($pdo, $level, $selected_year)) {
            $info = "Roll numbers for Level {$level} in {$selected_year} are not published yet.";
        } else {

            // 🔹 LEVEL 1 (roll_number in candidates table)
            if ($level === 1) {

                $stmt = $pdo->prepare("
                    SELECT
                        c.name,
                        c.monastery_name,
                        c.roll_number,
                        1 AS level,
                        YEAR(c.created_at) AS registration_year,
                        c.created_at
                    FROM candidates c
                    WHERE LOWER(c.email) = ?
                      AND c.status IN ('approved','passed')
                      AND c.roll_number IS NOT NULL
                      AND YEAR(c.created_at) = ?
                    LIMIT 1
                ");
                $stmt->execute([$email, $selected_year]);

            } 
            // 🔹 LEVEL 2 & 3
            else {

                $stmt = $pdo->prepare("
                    SELECT
                        c.name,
                        c.monastery_name,
                        r.roll_number,
                        r.level,
                        r.registration_year,
                        r.created_at
                    FROM candidates c
                    INNER JOIN registrations r ON c.id = r.candidate_id
                    WHERE LOWER(c.email) = ?
                      AND r.level = ?
                      AND r.registration_year = ?
                      AND r.status IN ('approved','passed')
                      AND r.roll_number IS NOT NULL
                    ORDER BY r.created_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$email, $level, $selected_year]);
            }

            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidate) {
                $resultFound = true;
            } else {
                $info = "No approved record found for Level {$level} in {$selected_year}.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Exam Roll Number | Monastery</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/check_roll_number.css" rel="stylesheet">
</head>


<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="monastery-card">

            <div class="monastery-header">
                <h2>Exam Roll Number</h2>
                <p>Thungwetaw Tawya Monastery</p>
            </div>

            <div class="content-area">

                <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($info): ?>
                <div class="alert alert-warning"><?= htmlspecialchars($info) ?></div>
                <?php endif; ?>

                <?php if (!$resultFound): ?>

                <form method="POST" class="mb-5">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Exam Level</label>
                            <select name="level" class="form-select">
                                <option value="1" <?= (($_POST['level'] ?? '1') === '1') ? 'selected' : '' ?>>Level 1</option>
                                <option value="2" <?= (($_POST['level'] ?? '') === '2') ? 'selected' : '' ?>>Level 2</option>
                                <option value="3" <?= (($_POST['level'] ?? '') === '3') ? 'selected' : '' ?>>Level 3</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Exam Year</label>
                            <select name="year" class="form-select">
                                <?php for ($year = $current_year + 1; $year >= 2020; $year--): ?>
                                <option value="<?= $year ?>" <?= $selected_year === $year ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button class="btn btn-monk">Find My Roll Number</button>
                        </div>
                    </div>
                </form>

                <div class="row text-center g-4">
                    <?php for ($i=1;$i<=3;$i++): ?>
                    <div class="col-md-4">
                        <div class="level-pill">
                            <strong>Level <?= $i ?></strong><br>
                            <?php if (isPublished($pdo,$i,$selected_year)): ?>
                            <span class="status-dot status-on">● Published</span>
                            <?php else: ?>
                            <span class="status-dot status-off">○ Not Published</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <?php else: ?>

                <div class="roll-card text-center">
                    <div class="small text-muted">OFFICIAL ROLL NUMBER</div>
                    <div class="roll-number"><?= htmlspecialchars($candidate['roll_number']) ?></div>

                    <div class="divider"></div>

                    <div class="row text-start">
                        <div class="col-md-6">
                            <p><span class="label">Name:</span> <?= htmlspecialchars($candidate['name']) ?></p>
                            <p><span class="label">Monastery:</span>
                                <?= htmlspecialchars($candidate['monastery_name']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><span class="label">Level:</span> <?= htmlspecialchars($candidate['level']) ?></p>
                            <p><span class="label">Year:</span> <?= htmlspecialchars((string)$candidate['registration_year']) ?></p>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button onclick="location.href='check_roll_number.php'" class="btn btn-outline-secondary me-2">Back</button>
                        <button onclick="window.print()" class="btn btn-monk">Print Admit Card</button>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>
