<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/admin_guard.php';

/**
 * Escape output
 */
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$pendingCount = 0;
$approvedNoRoll = 0;
$totalCandidates = 0;
$recentRows = [];

try {

    // Pending (latest per candidate)
    $stmt = $pdo->query("
        SELECT COUNT(*) FROM registrations r
        JOIN (
            SELECT candidate_id, MAX(created_at) latest
            FROM registrations
            GROUP BY candidate_id
        ) t ON t.candidate_id = r.candidate_id 
           AND t.latest = r.created_at
        WHERE r.status = 'pending'
    ");
    $pendingCount = (int) $stmt->fetchColumn();

    // Approved without roll
    $approvedNoRoll = (int) $pdo->query("
        SELECT COUNT(*) 
        FROM registrations
        WHERE status = 'approved'
          AND (roll_number IS NULL OR roll_number = '')
    ")->fetchColumn();

    // Total candidates
    $totalCandidates = (int) $pdo->query("
        SELECT COUNT(*) FROM candidates
    ")->fetchColumn();

    // Recent registrations
    $stmt = $pdo->query("
        SELECT 
            c.name,
            r.level,
            r.registration_year,
            r.roll_number,
            r.status,
            r.candidate_id
        FROM registrations r
        JOIN candidates c ON c.id = r.candidate_id
        ORDER BY r.created_at DESC
        LIMIT 6
    ");
    $recentRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $ex) {
    // Log real error, don't show admin users DB internals
    error_log('[Dashboard] ' . $ex->getMessage());
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard — Thungedawtawya</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/dashboard.css">
</head>

<body>

    <?php include __DIR__ . '/header.php'; ?>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <main class="main-content">

        <div class="dashboard-header">
            <h2>Overview</h2>

            <div class="header-actions">
                <a href="registrations.php" class="btn ghost">
                    <i class="fa-solid fa-file-signature"></i> Registrations
                </a>
                <button class="btn primary" id="refreshBtn">
                    <i class="fa-solid fa-arrows-rotate"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Metrics -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="title">Pending</div>
                <div class="value"><?= number_format($pendingCount) ?></div>
                <div class="card-hint">Awaiting review</div>
            </div>

            <div class="card">
                <div class="title">Approved (No Roll)</div>
                <div class="value"><?= number_format($approvedNoRoll) ?></div>
                <div class="card-hint">Ready for roll numbers</div>
            </div>

            <div class="card">
                <div class="title">Total Candidates</div>
                <div class="value"><?= number_format($totalCandidates) ?></div>
                <div class="card-hint">All-time</div>
            </div>
        </div>

        <!-- Recent registrations -->
        <section>
            <h3 class="section-title">Recent Registrations</h3>

            <div class="card table-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Level</th>
                            <th>Year</th>
                            <th>Roll</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if (!$recentRows): ?>
                        <tr>
                            <td colspan="6" class="empty">No recent registrations</td>
                        </tr>
                        <?php else: foreach ($recentRows as $r): ?>
                        <tr>
                            <td><?= e($r['name']) ?></td>
                            <td><?= e($r['level']) ?></td>
                            <td><?= e($r['registration_year']) ?></td>
                            <td><?= e($r['roll_number'] ?: '—') ?></td>
                            <td>
                                <span class="badge <?= e($r['status']) ?>">
                                    <?= ucfirst(e($r['status'])) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="registration_view.php?id=<?= (int)$r['candidate_id'] ?>" class="btn ghost">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>

                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <div class="toast-container"></div>

    <?php
$footer = __DIR__ . '/footer.php';
if (file_exists($footer)) {
    include $footer;
}
?>

    <script>
    (() => {
        const refreshBtn = document.getElementById('refreshBtn');
        const mobileBtn = document.getElementById('mobileSidebarOpen');
        const sidebar = document.getElementById('adminSidebar');

        refreshBtn?.addEventListener('click', () => {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML =
                '<i class="fa-solid fa-spinner fa-spin"></i> Refreshing';
            setTimeout(() => location.reload(), 300);
        });

        mobileBtn?.addEventListener('click', () => {
            sidebar?.classList.toggle('show');
        });
    })();
    </script>

</body>

</html>