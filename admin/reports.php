<?php
// admin/reports.php
session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/admin_guard.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$yearFilter = $_GET['year'] ?? 'all';
$whereYear  = ($yearFilter !== 'all') ? "WHERE registration_year = :year" : "";

try {

    
    $totalCandidates = (int)$pdo->query("SELECT COUNT(*) FROM candidates")->fetchColumn();

    
    $sqlStatus = "
        SELECT status, COUNT(*) AS cnt
        FROM registrations
        $whereYear
        GROUP BY status
    ";
    $stmt = $pdo->prepare($sqlStatus);
    if ($yearFilter !== 'all') $stmt->bindValue(':year', $yearFilter, PDO::PARAM_INT);
    $stmt->execute();
    $statusRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $pending  = $statusRows['pending']  ?? 0;
    $approved = $statusRows['approved'] ?? 0;
    $passed   = $statusRows['passed']   ?? 0;
    $Failed = $statusRows['failed']   ?? 0;
    $totalReg = $pending + $approved + $passed + $Failed;

    // Percentages
    $approvalRate = $totalReg ? round(($approved / $totalReg) * 100, 1) : 0;
    $passRate     = $approved ? round(($passed / $approved) * 100, 1) : 0;
    $failRate     = $approved ? round(($Failed / $approved) * 100, 1) : 0;
    // Registrations by year
    $byYear = $pdo->query("
        SELECT registration_year, COUNT(*) AS cnt
        FROM registrations
        WHERE registration_year IS NOT NULL
        GROUP BY registration_year
        ORDER BY registration_year DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Throwable $e) {
    http_response_code(500);
    die("<pre style='color:red'>".$e->getMessage()."</pre>");
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reports — Admin</title>
    <link rel="stylesheet" href="../admin/assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<style>
    .input{
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 5px;
        font-size: 14px;
        background: white;
        color: var(--text);
    }
</style>
<body>
    <?php include('header.php'); ?>
    <?php include('sidebar.php'); ?>

    <main class="main-content">

        <div class="dashboard-header">
            <div>
                <h2>Reports</h2>
                <div style="color:var(--muted);font-size:13px;">
                    Statistical overview & performance analysis
                </div>
            </div>

            <div class="header-actions">
                <form method="get" style="display:flex;gap:8px;">
                    <select name="year" class="input">
                        <option value="all">All Years</option>
                        <?php foreach ($byYear as $y): ?>
                        <option value="<?= $y['registration_year'] ?>"
                            <?= $yearFilter == $y['registration_year'] ? 'selected' : '' ?>>
                            <?= $y['registration_year'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn ghost"><i class="fa-solid fa-filter"></i> Filter</button>
                </form>

                <a class="btn ghost" href="reports_export.php?year=<?= urlencode($yearFilter) ?>">
                    <i class="fa-solid fa-file-export"></i> Export CSV
                </a>
            </div>
        </div>

        <!-- SUMMARY CARDS -->
        <div class="dashboard-cards">
            <div class="card">
                <div class="title">Pending</div>
                <div class="value"><?= number_format($pending) ?></div>
            </div>
            <div class="card">
                <div class="title">Approved</div>
                <div class="value"><?= number_format($approved) ?></div>
                <div class="sub"><?= $approvalRate ?>% approval rate</div>
            </div>
            <div class="card">
                <div class="title">Passed</div>
                <div class="value"><?= number_format($passed) ?></div>
                <div class="sub"><?= $passRate ?>% pass rate</div>
            </div>
            <div class="card">
                <div class="title">Failed</div>
                <div class="value"><?= number_format($Failed) ?></div>
                <div class="sub"><?= $failRate ?>% fail rate</div>


            </div>

            <!-- STATUS BREAKDOWN -->
            <div class="card">
                <h3 style="margin-top:0;color:var(--accent)">Registration Status Breakdown</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Pending</td>
                            <td><?= $pending ?></td>
                        </tr>
                        <tr>
                            <td>Approved</td>
                            <td><?= $approved ?></td>
                        </tr>
                        <tr>
                            <td>Passed</td>
                            <td><?= $passed ?></td>
                        </tr>
                        <tr>
                            <td>Failed</td>
                            <td><?= $Failed ?></td>
                        </tr>

                        <tr style="font-weight:bold;">
                            <td>Total</td>
                            <td><?= $totalReg ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- YEARLY SUMMARY -->
            <div class="card">
                <h3 style="margin-top:0;color:var(--accent)">Registrations by Year</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Registrations</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$byYear): ?>
                        <tr>
                            <td colspan="2" style="text-align:center;color:var(--muted)">No data</td>
                        </tr>
                        <?php else: foreach ($byYear as $r): ?>
                        <tr>
                            <td><?= $r['registration_year'] ?></td>
                            <td><?= $r['cnt'] ?></td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

    </main>
</body>

</html>