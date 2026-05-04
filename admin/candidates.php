<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/admin_guard.php';

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_candidate'], $_POST['candidate_id'])) {

    $candidate_id = (int)$_POST['candidate_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->execute([$candidate_id]);

        $_SESSION['success_message'] = "Candidate deleted successfully!";
        header("Location: candidates.php");
        exit;
    } catch (Throwable $e) {
        $_SESSION['error_message'] = "Delete failed.";
    }
}

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';

$params = [];
$sql = "SELECT id, name, roll_number, status, created_at 
        FROM candidates 
        WHERE 1=1";

/* Search */
if ($q !== '') {
    $sql .= " AND (name LIKE :search OR roll_number LIKE :search)";
    $params[':search'] = "%$q%";
}

/* Status filter */
$allowedStatuses = ['pending','approve','passed','reject'];

if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

$sql .= " ORDER BY created_at DESC LIMIT 200";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Candidates — Admin</title>
    <link rel="stylesheet" href="../admin/assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">

        <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;">
            <div>
                <h2>Candidates</h2>
                <small style="color:gray;">Search by name / roll or filter by status</small>
            </div>

            <form method="get" style="display:flex;gap:8px;align-items:center;">
                <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search..."
                    style="padding:6px 10px;border-radius:6px;border:1px solid #ccc;">

                <select name="status" style="padding:6px 10px;border-radius:6px;border:1px solid #ccc;">
                    <option value="">All Status</option>
                    <option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option>
                    <option value="approve" <?= $status==='approve'?'selected':'' ?>>Approve</option>
                    <option value="passed" <?= $status==='passed'?'selected':'' ?>>Passed</option>
                    <option value="reject" <?= $status==='reject'?'selected':'' ?>>Reject</option>
                </select>

                <button class="btn ghost" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <a href="candidates.php" class="btn ghost">Reset</a>
            </form>
        </div>


        <!-- Messages -->
        <?php if (!empty($_SESSION['success_message'])): ?>
        <div style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin:10px 0;">
            <?= e($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error_message'])): ?>
        <div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:6px;margin:10px 0;">
            <?= e($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        </div>
        <?php endif; ?>


        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (!$rows): ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:gray;">No candidates found</td>
                    </tr>
                    <?php else: foreach ($rows as $r): ?>
                    <tr>
                        <td><?= e($r['name']) ?></td>
                        <td><?= e($r['roll_number'] ?: '-') ?></td>
                        <td>
                            <span class="badge <?= e($r['status']) ?>">
                                <?= ucfirst(e($r['status'])) ?>
                            </span>
                        </td>
                        <td><?= e(date('Y-m-d', strtotime($r['created_at']))) ?></td>
                        <td>

                            <form method="post" onsubmit="return confirm('Delete this candidate?');"
                                style="display:inline;">
                                <input type="hidden" name="candidate_id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" name="delete_candidate"
                                    style="background:none;border:none;color:#dc3545;cursor:pointer;">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>

                            <a href="registration_view.php?id=<?= (int)$r['id'] ?>"
                                style="margin-left:8px;color:#2563eb;">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                        </td>
                    </tr>
                    <?php endforeach; endif; ?>

                </tbody>
            </table>
        </div>

    </main>

</body>

</html>