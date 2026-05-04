<?php

require_once '../configuration/db.php';
require_once 'auth/admin_guard.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf_token'];




date_default_timezone_set('Asia/Yangon');
try {
    
    $pdo->exec("SET time_zone = '+06:30'");
} catch (Throwable $e) {
  
}

if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit;
}

$year = (int)date('Y');              
$selectedLevel = (int)($_GET['level'] ?? 1);
$message = $_GET['msg'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request token.';
    } else {
    $level  = (int)($_POST['level'] ?? 1);
    $startRaw = trim($_POST['publish_start'] ?? '');
    $endRaw   = trim($_POST['publish_end'] ?? '');
    $active = isset($_POST['is_active']) ? 1 : 0;

    
    $start = null;
    $end = null;

    if ($startRaw !== '') {
        $ds = DateTime::createFromFormat('Y-m-d\TH:i', $startRaw, new DateTimeZone('Asia/Yangon'));
        if ($ds) {
            $start = $ds->format('Y-m-d H:i:s');
        } else {
            $tmp = str_replace('T', ' ', $startRaw);
            $ds2 = DateTime::createFromFormat('Y-m-d H:i', $tmp, new DateTimeZone('Asia/Yangon'));
            $start = $ds2 ? $ds2->format('Y-m-d H:i:s') : null;
        }
    }

    if ($endRaw !== '') {
        $de = DateTime::createFromFormat('Y-m-d\TH:i', $endRaw, new DateTimeZone('Asia/Yangon'));
        if ($de) {
            $end = $de->format('Y-m-d H:i:s');
        } else {
            $tmp = str_replace('T', ' ', $endRaw);
            $de2 = DateTime::createFromFormat('Y-m-d H:i', $tmp, new DateTimeZone('Asia/Yangon'));
            $end = $de2 ? $de2->format('Y-m-d H:i:s') : null;
        }
    }


    if ($start && $end && $start > $end) {
        $error = 'Error: Start date cannot be after end date.';
    } else {
        
        $check = $pdo->prepare("SELECT id FROM roll_publishings WHERE level = ? AND year = ? LIMIT 1");
        $check->execute([$level, $year]);

        if ($check->rowCount()) {
            $stmt = $pdo->prepare("
                UPDATE roll_publishings
                SET publish_start = ?, publish_end = ?, is_active = ?, updated_at = NOW()
                WHERE level = ? AND year = ?
            ");
            $stmt->execute([$start, $end, $active, $level, $year]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO roll_publishings (level, year, publish_start, publish_end, is_active, created_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $created_by = $_SESSION['admin_id'] ?? 1;
            $stmt->execute([$level, $year, $start, $end, $active, $created_by]);
        }

        header("Location: roll_publish.php?level={$level}&msg=saved");
        exit;
    }
    }
}




$publish = [];
$stmt = $pdo->prepare("SELECT * FROM roll_publishings WHERE year = ?");
$stmt->execute([$year]);
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $publish[(int)$r['level']] = $r;
}

if ($selectedLevel == 1) {
    $sql = "
        SELECT
            c.id,
            c.roll_number,
            c.status,
            c.created_at,
            c.name,
            c.email,
            c.monastery_name
        FROM candidates c
        WHERE c.status = 'approved'
          AND c.roll_number IS NOT NULL
        ORDER BY c.roll_number ASC, c.created_at ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

} else {
    $sql = "
        SELECT 
            r.id,
            r.roll_number,
            r.status,
            r.created_at,
            c.name,
            c.email,
            c.monastery_name
        FROM registrations r
        JOIN candidates c ON c.id = r.candidate_id
        WHERE r.level = ?
          AND r.registration_year = ?
          AND r.status = 'approved'
        ORDER BY r.roll_number ASC, r.created_at ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$selectedLevel, $year]);
}

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);



$isPublished = isset($publish[$selectedLevel]) && ((int)$publish[$selectedLevel]['is_active'] === 1);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin | Roll Management</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
    :root {
        --admin-brown: #5d462a;
        --accent-gold: #bfa47a;
    }

    body {
        background: #f4f1ee;
        font-family: Inter, sans-serif;
        color: #333;
    }

    .admin-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: #fff;
    }

    .level-pill {
        cursor: pointer;
        transition: all .3s;
        border: 1px solid #e0dace;
        background: #fff;
        border-radius: 15px;
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .level-pill.active {
        background: var(--admin-brown);
        color: #fff;
        border-color: var(--admin-brown);
    }

    .table-container {
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #eee;
    }

    .custom-table thead {
        background: #fdfaf6;
        color: var(--admin-brown);
        font-weight: 600;
    }

    .btn-save {
        background: var(--admin-brown);
        color: white;
        border-radius: 10px;
        padding: 10px 25px;
        border: none;
    }

    .btn-save:hover {
        background: #4a3822;
        color: white;
    }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Command Center</h2>
                <p class="text-muted">Manage Roll Number Visibility for <?= htmlspecialchars($year) ?></p>
            </div>
            <div>
                <a href="dashboard.php" class="btn btn-white shadow-sm rounded-pill px-4 me-2"><i
                        class="fa fa-home me-2"></i>Dashboard</a>
                <button class="btn btn-info shadow-sm rounded-pill px-4 text-white" data-bs-toggle="modal"
                    data-bs-target="#helpModal"><i class="fa fa-question-circle me-2"></i>Help</button>
            </div>
        </div>

        <?php if ($message === 'saved'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><i class="fa-solid fa-circle-check me-2"></i>
            Settings updated successfully.</div>
        <?php endif; ?>
        <?php if ($error): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4"><i class="fa-solid fa-circle-xmark me-2"></i>
            <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-4 text-center">
            <?php for ($l = 1; $l <= 3; $l++): ?>
            <div class="col-md-4">
                <a href="?level=<?= $l ?>" class="level-pill p-4 <?= $selectedLevel === $l ? 'active shadow' : '' ?>">
                    <h5 class="mb-1">Level <?= $l ?></h5>
                    <small class="<?= $selectedLevel === $l ? 'text-white-50' : 'text-muted' ?>">
                        <?= !empty($publish[$l]['is_active']) ? '● Live' : '○ Internal Only' ?>
                    </small>
                </a>
            </div>
            <?php endfor; ?>
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="admin-card p-4 h-100">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-sliders me-2"></i>Visibility Toggle</h5>
                    <form method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                        <input type="hidden" name="level" value="<?= htmlspecialchars($selectedLevel) ?>">
                        <div class="mb-3">
                            <label class="small fw-bold text-muted">START PUBLISHING</label>
                            <input type="datetime-local" name="publish_start"
                                class="form-control form-control-lg border-0 bg-light"
                                value="<?= isset($publish[$selectedLevel]['publish_start']) && $publish[$selectedLevel]['publish_start'] ? date('Y-m-d\TH:i', strtotime($publish[$selectedLevel]['publish_start'])) : '' ?>">
                        </div>

                        <div class="mb-4">
                            <label class="small fw-bold text-muted">END PUBLISHING</label>
                            <input type="datetime-local" name="publish_end"
                                class="form-control form-control-lg border-0 bg-light"
                                value="<?= isset($publish[$selectedLevel]['publish_end']) && $publish[$selectedLevel]['publish_end'] ? date('Y-m-d\TH:i', strtotime($publish[$selectedLevel]['publish_end'])) : '' ?>">
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="activeSwitch"
                                <?= $isPublished ? 'checked' : '' ?>>
                            <label class="form-check-label fw-bold" for="activeSwitch">Master Public Switch</label>
                            <div class="small text-muted">When off, students cannot see their roll numbers even within
                                the date range.</div>
                        </div>

                        <button class="btn btn-save w-100 py-3 fw-bold shadow-sm"><i
                                class="fa-solid fa-cloud-arrow-up me-2"></i>Apply Level
                            <?= htmlspecialchars($selectedLevel) ?> Settings</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="admin-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0"><i class="fa-solid fa-list-ol me-2"></i>Approved Candidates</h5>
                        <span class="badge bg-light text-dark border rounded-pill px-3 py-2"><?= count($rows) ?>
                            Total</span>
                    </div>

                    <div class="table-container">
                        <table class="table custom-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Roll</th>
                                    <th>Candidate</th>
                                    <th>Monastery</th>
                                    <th class="text-end pe-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">No approved candidates for this
                                        level.</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td class="ps-3 fw-bold text-primary">
                                        <?= $r['roll_number'] ? htmlspecialchars($r['roll_number']) : '<span class="text-muted">Pending</span>' ?>
                                    </td>

                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($r['name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($r['email']) ?></div>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($r['monastery_name']) ?></td>
                                    <td class="text-end pe-3 small text-muted">
                                        <?= date('M d, Y', strtotime($r['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- HELP MODAL -->
    <div class="modal fade" id="helpModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fa fa-circle-question me-2"></i> Roll Publishing Help
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul>
                        <li><strong>Level 1:</strong> Roll numbers come from approved candidates.</li>
                        <li><strong>Level 2 & 3:</strong> Roll numbers come from approved registrations.</li>
                        <li><strong>Start / End Time:</strong> Controls visibility window.</li>
                        <li><strong>Master Public Switch:</strong> Must be ON to show roll numbers.</li>
                    </ul>
                    <div class="alert alert-warning mt-3">
                        Even if dates are valid, students won’t see roll numbers unless the switch is ON.
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
