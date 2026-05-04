<?php
session_start();
require_once '../configuration/db.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$error = '';
$info  = '';
$candidate = null;
$resultFound = false;

/* =====================================
   STRICT RESULT OPEN CHECK
===================================== */
function isResultOpen(PDO $pdo, int $level): bool {

    $allowedLevels = [1,2,3];

    if (!in_array($level, $allowedLevels)) {
        return false;
    }

    $stmt = $pdo->prepare("
        SELECT result_level1, result_level2, result_level3
        FROM registration_publish
        WHERE id = 1
        LIMIT 1
    ");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false;
    }

    return (int)$row["result_level{$level}"] === 1;
}


/* =====================================
   HANDLE FORM
===================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $roll  = trim($_POST['roll_number'] ?? '');
    $level = (int)($_POST['level'] ?? 1);

    if ($roll === '') {
        $error = "Please enter your roll number.";
    }
    else {

        // 🔒 CHECK IF RESULT IS OPEN
        if (!isResultOpen($pdo, $level)) {
            $info = "Result checking for Level {$level} is currently closed.";
        }
        else {

            $stmt = $pdo->prepare("
                SELECT 
                    r.roll_number,
                    r.level,
                    r.status,
                    r.registration_year,
                    c.name
                FROM registrations r
                INNER JOIN candidates c ON r.candidate_id = c.id
                WHERE r.roll_number = ?
                  AND r.level = ?
                  AND r.status IN ('passed','failed')
                LIMIT 1
            ");

            $stmt->execute([$roll, $level]);
            $candidate = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($candidate) {
                $resultFound = true;
            } else {
                $info = "Invalid roll number. Please check again or contact administration.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Examination Result Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/result.css" rel="stylesheet">
</head>

<body>

    <?php include 'navbar.php'; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">

                <div class="card shadow-lg">

                    <div class="card-header bg-dark text-white text-center">
                        <i class="fa-solid fa-dharmachakra fa-2x text-warning"></i>
                        <h4 class="mt-2">Examination Result Portal</h4>
                        <small>Thungwetaw Tawya Monastery</small>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <?php if ($info): ?>
                        <div class="alert alert-warning"><?= htmlspecialchars($info) ?></div>
                        <?php endif; ?>


                        <?php if (!$resultFound): ?>

                        <!-- ================= SEARCH FORM ================= -->
                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Roll Number</label>
                                <input type="text" name="roll_number" class="form-control form-control-lg"
                                    placeholder="Enter your roll number" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Examination Level</label>
                                <select name="level" class="form-select form-select-lg">
                                    <option value="1">Level 1</option>
                                    <option value="2">Level 2</option>
                                    <option value="3">Level 3</option>
                                </select>
                            </div>

                            <button class="btn btn-warning btn-lg w-100">
                                <i class="fa-solid fa-magnifying-glass"></i> Check Result
                            </button>

                        </form>

                        <?php else: ?>

                        <!-- ================= RESULT DISPLAY ================= -->
                        <div class="text-center">

                            <h2 class="fw-bold mb-3">
                                <?= htmlspecialchars($candidate['roll_number']) ?>
                            </h2>

                            <span
                                class="badge bg-<?= $candidate['status'] === 'passed' ? 'success' : 'danger' ?> p-3 fs-5">
                                <?= strtoupper($candidate['status']) ?>
                            </span>

                            <hr>

                            <table class="table table-bordered mt-4">
                                <tr>
                                    <td class="fw-bold">Name</td>
                                    <td><?= htmlspecialchars($candidate['name']) ?></td>
                                </tr>

                                <tr>
                                    <td class="fw-bold">Level</td>
                                    <td>Level <?= $candidate['level'] ?></td>
                                </tr>

                                <tr>
                                    <td class="fw-bold">Examination Year</td>
                                    <td><?= $candidate['registration_year'] ?></td>
                                </tr>

                                <tr>
                                    <td class="fw-bold">Passed Year</td>
                                    <td>
                                        <?php
if ($candidate['status'] === 'passed') {
    echo $candidate['registration_year'];
} else {
    echo '<span class="text-muted">Not Applicable</span>';
}
?>
                                    </td>
                                </tr>

                            </table>

                            <a href="result.php" class="btn btn-outline-secondary w-100 mt-3">
                                ← Search Another
                            </a>

                        </div>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>