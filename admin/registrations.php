<?php
// admin/registrations.php

session_start();
require_once __DIR__ . '/../configuration/db.php';

require_once __DIR__ . '/auth/admin_guard.php';

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Flash messages (one-time)
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// --- Handle incoming filters (GET) ---
$nameQ = trim((string)($_GET['q_name'] ?? ''));
$rollQ = trim((string)($_GET['q_roll'] ?? ''));
$statusF = trim((string)($_GET['q_status'] ?? '')); // pending|approved|passed|rejected or empty
$levelF = trim((string)($_GET['q_level'] ?? ''));
$yearF = trim((string)($_GET['q_year'] ?? ''));

// pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build base SQL (latest registration per candidate)
$baseFrom = "
    FROM candidates c
    LEFT JOIN (
        SELECT r1.* FROM registrations r1
        JOIN (
            SELECT candidate_id, MAX(created_at) AS max_created
            FROM registrations
            WHERE candidate_id IS NOT NULL
            GROUP BY candidate_id
        ) r2 ON r1.candidate_id = r2.candidate_id AND r1.created_at = r2.max_created
    ) r ON r.candidate_id = c.id
";

// Build WHERE clauses and params
$where = [];
$params = [];

if ($nameQ !== '') {
    $where[] = "c.name LIKE :q_name";
    $params[':q_name'] = '%' . $nameQ . '%';
}

if ($rollQ !== '') {
    // search both candidate.roll_number and registration.roll_number
    $where[] = "(COALESCE(r.roll_number, '') LIKE :q_roll OR COALESCE(c.roll_number, '') LIKE :q_roll)";
    $params[':q_roll'] = '%' . $rollQ . '%';
}

if ($statusF !== '') {
    // effective status is registration status if exists otherwise candidate status
    $where[] = "COALESCE(r.status, c.status) = :q_status";
    $params[':q_status'] = $statusF;
}

if ($levelF !== '') {
    // only filter when registration exists (r.level). This will exclude rows without a registration level.
    $where[] = "r.level = :q_level";
    $params[':q_level'] = $levelF;
}

if ($yearF !== '') {
    $where[] = "r.registration_year = :q_year";
    $params[':q_year'] = $yearF;
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = ' WHERE ' . implode(' AND ', $where);
}

// Count total for pagination
$countSql = "SELECT COUNT(*) AS cnt " . $baseFrom . $whereSql;
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = (int)ceil($totalRows / $perPage);

// Fetch rows with limit
$sql = "SELECT
    c.id AS candidate_id,
    c.name,
    c.roll_number AS candidate_roll,
    c.status AS candidate_status,
    r.id AS registration_id,
    r.level AS registration_level,
    r.registration_year,
    r.roll_number AS reg_roll_number,
    r.status AS reg_status,
    c.created_at AS candidate_created
" . $baseFrom . $whereSql . " ORDER BY c.created_at DESC";


// prepare and bind
$stmt = $pdo->prepare($sql . " LIMIT :offset, :perpage");
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':perpage', (int)$perPage, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build query string for pagination links preserving filters
function build_qs($overrides = []) {
    $base = array_merge($_GET, $overrides);
    return http_build_query($base);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Candidate Registrations — Admin</title>
    <link rel="stylesheet" href="../admin/assets/dashboard.css">
    <link rel="stylesheet" href="../admin/assets/registration.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<style>
/* BODY & LAYOUT */
body {
    background: #fefbf6;
    /* Soft warm cream */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #4b3f2f;
    margin: 0;
    padding: 0;
}

.main-content {
    margin-left: 50px;
    padding-right: 40px;
    /* Matches sidebar width */
    padding: 20px;
    transition: margin-left 0.3s;
}

.main-content.sidebar-collapsed {
    margin-left: 70px;
    /* Matches collapsed sidebar width */
}

/* PAGE TITLE */
.page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    font-size: 26px;
    font-weight: 700;
    color: #9c6644;
    margin-bottom: 20px;
    padding-right: 40px;
    padding-top: 60px;
}




/* TABLE WRAPPER */
.table-wrap {
    background: #fff8f0;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);

    padding: 15px;
    margin-top: 20px;

    width: 100%;
    /* Fill available space */
    max-width: none;
    /* REMOVE restriction */
    min-width: 0;
    /* IMPORTANT for grid/flex parents */

    overflow-x: auto;
}



/* TABLE */
.table {
    width: 100%;
    min-width: 1000px;
    /* keeps columns readable */
    border-collapse: collapse;
}

.table thead {
    background: #a67c52;
    /* Earthy golden brown */
    color: #fff;
}

.table thead th {
    background: #a67c52 !important;
    color: #fff;
    font-weight: 600;
    padding: 12px 15px;
    text-align: left;
}


.table tbody tr {
    transition: all 0.25s ease;
    cursor: default;
}

.table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
    background: #fffdf7;
}

/* BADGES */
.badge {
    display: inline-block;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    font-size: 0.85rem;
}

.badge.pending {
    background: #fff6e9;
    color: #a46a2f;
}

.badge.approved {
    background: #d1f7c4;
    color: #2a641a;
}

.badge.rejected {
    background: #ffd8d8;
    color: #b33a3a;
}

.badge.passed {
    background: #e9f2ff;
    color: #164c86;
}

.badge.failed {
    background-color: white;
    color: red;
}

/* ACTION BUTTONS */
.btn-icon {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    border-radius: 50%;
    color: #fff;
    font-size: 16px;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    transform: scale(1.1);
}

.btn-success {
    background-color: #7c9d5a;
}

.btn-warning {
    background-color: #d4a55d;
}

.btn-info {
    background-color: #a6926e;
}

.btn-danger {
    background-color: #b35151;
}

.btn-primary {
    background-color: #9c7c4c;
}

/* ACTION BAR */
.action-bar {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

/* PAGINATION */
.pagination-container {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}


/* MODAL */
.modal-header {
    background-color: #7c9d5a;
    color: #fff;
}

.modal-footer button {
    border-radius: 6px;
}

#backDashboardBtn {
    top: 20px;
    /* distance from top */
    left: 20px;
    /* distance from left */
    background: #6c757d;
    /* gray button */
    color: white;
    padding: 10px 14px;
    border-radius: 50px;
    text-decoration: none;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: all 0.2s ease;
    z-index: 1000;
}

#backDashboardBtn:hover {
    background: #495057;
    /* darker gray on hover */
    transform: translateY(-2px);
}

#backDashboardBtn i {
    font-size: 16px;
}
</style>

<body>
    <!-- header -->
    <?php include('header.php'); ?>


    <div class="main-content" style="margin-left: 50px; padding: 10px;">

        <?php if ($flash): ?>
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
            <div id="adminToast" class="toast align-items-center text-bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= htmlspecialchars($flash) ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <header class="page-title">
            <div><i class="fa fa-users"></i> Candidate Management</div>
            <a href="dashboard.php" id="backDashboardBtn" title="Back to Dashboard">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </header>
        <!-- Results Count -->
        <div class="alert alert-light mb-3">
            Showing <?= count($rows) ?> of <?= $totalRows ?> records
            <?php if ($nameQ || $rollQ || $statusF || $levelF || $yearF): ?>
            <span class="text-muted">(filtered)</span>
            <?php endif; ?>
        </div>
        <div>
            <a href="registrations.php" class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh
            </a>
        </div>
        <div class="table-wrap">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Roll</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Status</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No records found
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php foreach($rows as $r): 
                        $cid = $r['candidate_id']; 
                        $rid = $r['registration_id'];
                        $roll = $r['reg_roll_number'] ?? $r['candidate_roll'] ?? '-';
                        $rawStatus = $r['reg_status'] ?? $r['candidate_status'] ?? 'unknown';
                        $allowedStatus = ['pending','approved','passed','rejected','failed'];
                        $status = in_array($rawStatus, $allowedStatus, true) ? $rawStatus : 'pending';

                        $year = $r['registration_year'] ?? '-';
        
                        
                        // 1. Level Detection: If no registration yet, it's Level 1
                        $level = $r['registration_level'] !== null ? (int)$r['registration_level']: 1;

                        // 2. Roll Number Check
                        $hasRollNumber = !empty($roll) && $roll !== '-';

                        
                        // 3. Status Booleans
                        $isPending  = ($status === 'pending');
                        $isApproved = ($status === 'approved');
                        $isPassed   = ($status === 'passed');
                        $isRejected = ($status === 'rejected');
                        $isFailed   = ($status === 'failed');
                        
                        // 4. Action Availability
                        $canApprove      = $isPending;
                        $canReject       = ($isPending || $isApproved);
                        $canGenerateRoll = $isApproved && !$hasRollNumber;
                        $canMarkPassed   = $isApproved && $hasRollNumber;
                        $canMarkFailed   = $isApproved && $hasRollNumber;
                        $canNextLevel    = $isPassed && $level < 3;
                        
                    ?>
                    <tr>
                        <td>
                            <strong>
                                <?= $hasRollNumber ? htmlspecialchars($roll) : '<span class="text-muted">No Roll</span>' ?>
                            </strong>
                        </td>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= $level ?></td>
                        <td>
                            <span class="badge <?= $status ?>">
                                <?= ucfirst($status) ?>

                            </span>


                        </td>
                        <td><?= $year ?>
                        </td>
                        <td>
                            <div class="action-bar">
                                <!-- View (Always Available) -->
                                <a href="registration_view.php?id=<?= $cid ?>" class="btn btn-secondary btn-icon"
                                    title="View Details">
                                    <i class="fa fa-eye"></i>
                                </a>

                                <!-- Approve (Only when Pending) -->
                                <?php if($canApprove): ?>
                                <form method="post" action="../action/registrations_action.php" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="id" value="<?= $cid ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-success btn-icon" title="Approve Registration">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <!-- Reject (When Pending or Approved, not Rejected/Passed) -->
                                <?php if($canReject): ?>
                                <form method="post" action="../action/registrations_action.php" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="id" value="<?= $cid ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-warning btn-icon" title="Reject Registration">
                                        <i class="fa fa-xmark"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <!-- Generate Roll (Only when Approved AND no roll number) -->
                                <?php if($canGenerateRoll): ?>
                                <form method="post" action="../action/generate_roll.php" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="registration_id" value="<?= $rid ?: 0 ?>">
                                    <input type="hidden" name="candidate_id" value="<?= $cid ?>">
                                    <button class="btn btn-dark btn-icon" title="Generate Roll Number">
                                        <i class="fa fa-id-card"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <!-- Mark Passed (Only when Approved AND has roll number) -->
                                <?php if($canMarkPassed): ?>
                                <button class="btn btn-primary btn-icon" title="Mark as Passed"
                                    onclick="openMarkPassed('<?= $rid ?>','<?= $cid ?>','<?= htmlspecialchars($roll) ?>')">
                                    <i class="fa fa-graduation-cap"></i>
                                </button>
                                <?php endif; ?>

                                <?php if ($canMarkFailed): ?>
                                <button class="btn btn-danger btn-icon" title="Mark as Failed"
                                    onclick="openMarkFailed('<?= $rid ?: 0 ?>','<?= $cid ?>','<?= htmlspecialchars($roll) ?>')">
                                    <i class="fa fa-circle-xmark"></i>
                                </button>
                                <?php endif; ?>


                                <!-- Next Level (Only when Passed AND level < 3) -->
                                <?php if($canNextLevel): ?>
                                <form method="post" action="../action/admin_create_next.php" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="prev_registration_id" value="<?= $rid ?>">
                                    <input type="hidden" name="target_level" value="<?= $level + 1 ?>">
                                    <input type="hidden" name="registration_year" value="<?= date('Y') ?>">
                                    <button class="btn btn-info btn-icon" title="Create Next Level Registration">
                                        <i class="fa fa-arrow-up"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <!-- Delete (Always Available) -->
                                <form method="post" action="../action/registrations_action.php" style="display:inline;"
                                    onsubmit="return confirm('Are you sure you want to permanently delete this candidate and all their registrations?');">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                    <input type="hidden" name="id" value="<?= $cid ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button class="btn btn-danger btn-icon" title="Delete Permanently">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <nav>
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= build_qs(['page' => $page - 1]) ?>">
                                <i class="fa fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= build_qs(['page' => $i]) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php elseif (($i == $page - 3 && $page > 4) || ($i == $page + 3 && $page < $totalPages - 3)): ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                        <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= build_qs(['page' => $page + 1]) ?>">
                                <i class="fa fa-chevron-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Mark Passed Modal -->
    <div class="modal fade" id="markPassedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="../action/admin_mark_passed.php">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="registration_id" id="mp_registration_id">
                <input type="hidden" name="candidate_id" id="mp_candidate_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">🎓 Mark Candidate as Passed</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Roll Number</label>
                        <input type="text" id="mp_roll" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Passed Year</label>
                        <input type="number" name="passed_year" class="form-control" value="<?= date('Y') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Marks / Notes</label>
                        <textarea name="marks" class="form-control" placeholder='{"total":85,"grade":"A"}'></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Confirm Passed</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Mark Failed Modal -->
    <div class="modal fade" id="markFailedModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="post" action="../action/admin_mark_failed.php">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <input type="hidden" name="registration_id" id="mf_registration_id">
                <input type="hidden" name="candidate_id" id="mf_candidate_id">



                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">⚠️ Confirm Failure</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-2">
                        Are you sure you want to mark this candidate as <strong>FAILED</strong>?
                    </p>

                    <div class="alert alert-warning small">
                        • Roll number will be kept<br>
                        • Candidate can re-attempt same level later<br>
                        • This action cannot be undone
                    </div>

                    <div class="mb-3">
                        <label>Roll Number</label>
                        <input type="text" id="mf_roll" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Failure Reason / Notes (optional)</label>
                        <textarea name="notes" class="form-control"
                            placeholder="Attendance issue, insufficient marks, etc."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Confirm Failed
                    </button>
                </div>
            </form>
        </div>
    </div>




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function openMarkPassed(regId, candId, roll) {
        document.getElementById('mp_registration_id').value = regId;
        document.getElementById('mp_candidate_id').value = candId;
        document.getElementById('mp_roll').value = roll;
        new bootstrap.Modal(document.getElementById('markPassedModal')).show();
    }

    <?php if ($flash): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const toastEl = document.getElementById('adminToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        }
    });
    <?php endif; ?>

    // Tooltip initialization
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    const backBtn = document.getElementById('backDashboardBtn');
    backBtn.addEventListener('click', (e) => {
        // e.preventDefault(); // Uncomment if you want JS redirect
        // window.location.href = 'dashboard.php';
        console.log('Going back to dashboard...');
    });

    function openMarkFailed(regId, candId, roll) {
        document.getElementById('mf_registration_id').value = regId;
        document.getElementById('mf_roll').value = roll;
        document.getElementById('mf_candidate_id').value = candId;
        new bootstrap.Modal(document.getElementById('markFailedModal')).show();
    }
    </script>
</body>

</html>