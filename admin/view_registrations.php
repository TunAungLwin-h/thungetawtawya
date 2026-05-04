<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';


// Check admin permissions
require_once __DIR__ . '/auth/admin_guard.php';
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Get filter parameters
$level = isset($_GET['level']) ? intval($_GET['level']) : null;
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query with filters
$where = ['r.registration_year = :year'];
$params = [':year' => $year];

if ($level) {
    $where[] = 'r.level = :level';
    $params[':level'] = $level;
}

if ($status !== 'all') {
    $where[] = 'r.status = :status';
    $params[':status'] = $status;
}

if (!empty($search)) {
    $where[] = '(c.name LIKE :search OR c.email LIKE :search OR r.roll_number LIKE :search)';
    $params[':search'] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count for pagination
$countSql = "
    SELECT COUNT(*) as total 
    FROM registrations r
    JOIN candidates c ON r.candidate_id = c.id
    $whereClause
";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Pagination
$perPage = 20;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;
$totalPages = ceil($totalCount / $perPage);

// Get registrations
$sql = "
    SELECT 
        r.*, 
        c.name, 
        c.dob, 
        c.email,
        c.monastery_name,
        c.abbot_name,
        c.address_village,
        c.address_township,
        c.address_region,
        c.father_name,
        c.mother_name,
        a.username as approved_by
    FROM registrations r
    JOIN candidates c ON r.candidate_id = c.id
    LEFT JOIN admins a ON r.approved_by = a.id
    $whereClause
    ORDER BY r.created_at DESC
    LIMIT $perPage OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$statsSql = "
    SELECT 
        level,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'passed' THEN 1 ELSE 0 END) as passed,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
        SUM(CASE WHEN roll_number IS NOT NULL THEN 1 ELSE 0 END) as has_roll
    FROM registrations 
    WHERE registration_year = ?
    GROUP BY level
    ORDER BY level
";
$statsStmt = $pdo->prepare($statsSql);
$statsStmt->execute([$year]);
$statistics = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get years for filter
$yearsStmt = $pdo->query("SELECT DISTINCT registration_year FROM registrations ORDER BY registration_year DESC");
$years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrations - Thungwetaw Monastery</title>
    <link rel="stylesheet" href="../../public/assets/dashboard.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Your Admin CSS -->
    <link rel="stylesheet" href="../../public/assets/css/admin.css">
    <style>
    /* MONASTERY THEME STYLES */
    :root {
        --primary-brown: #7c5e3b;
        --light-brown: #c9b79c;
        --cream: #f9f5ee;
        --dark-brown: #4b3f2f;
        --gold: #d4af37;
    }

    body {
        background: linear-gradient(135deg, #f9f5ee 0%, #f0e8db 100%);
        font-family: 'Segoe UI', 'Noto Sans Myanmar', sans-serif;
        color: var(--dark-brown);
        min-height: 100vh;
    }

    .monastery-header {
        background: linear-gradient(to right, var(--primary-brown), #8b6b47);
        color: white;
        padding: 1.5rem;
        border-bottom: 5px solid var(--gold);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .header-title {
        font-family: 'Times New Roman', serif;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .header-title small {
        font-size: 0.7em;
        opacity: 0.9;
    }

    .sidebar {
        background: #fffaf2;
        border-right: 1px solid var(--light-brown);
        min-height: calc(100vh - 76px);
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.05);
    }

    .nav-link {
        color: var(--dark-brown);
        padding: 12px 20px;
        border-left: 4px solid transparent;
        transition: all 0.3s;
        margin: 4px 0;
    }

    .nav-link:hover,
    .nav-link.active {
        background: rgba(124, 94, 59, 0.1);
        color: var(--primary-brown);
        border-left-color: var(--primary-brown);
    }

    .nav-icon {
        width: 24px;
        text-align: center;
        margin-right: 10px;
    }

    .main-content {
        padding: 25px;
        background: #fff;
        min-height: calc(100vh - 76px);
    }

    .page-title {
        color: var(--primary-brown);
        border-bottom: 2px solid var(--light-brown);
        padding-bottom: 15px;
        margin-bottom: 25px;
        font-family: 'Times New Roman', serif;
    }

    .stat-card {
        background: linear-gradient(135deg, #fffaf2 0%, #f5ede1 100%);
        border: 1px solid var(--light-brown);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .stat-card.level-1 {
        border-top: 4px solid #3498db;
    }

    .stat-card.level-2 {
        border-top: 4px solid #2ecc71;
    }

    .stat-card.level-3 {
        border-top: 4px solid #9b59b6;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-brown);
        line-height: 1;
    }

    .stat-label {
        color: var(--dark-brown);
        font-size: 0.9rem;
        margin-top: 8px;
    }

    .registrations-table {
        background: #fff;
        border: 1px solid var(--light-brown);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .table-header {
        background: linear-gradient(to right, var(--primary-brown), #8b6b47);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: rgba(124, 94, 59, 0.1);
    }

    th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: var(--primary-brown);
        border-bottom: 2px solid var(--light-brown);
    }

    td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        vertical-align: top;
    }

    tr:hover {
        background: rgba(124, 94, 59, 0.05);
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-passed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-failed {
        background: #f8d7da;
        color: #721c24;
    }

    .roll-number {
        font-family: monospace;
        font-weight: bold;
        color: var(--primary-brown);
        background: #f8f4ec;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px dashed var(--light-brown);
    }

    .action-btn {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
        margin: 2px;
    }

    .btn-view {
        background: #e3f2fd;
        color: #1565c0;
        border: 1px solid #bbdefb;
    }

    .btn-edit {
        background: #e8f5e8;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .btn-approve {
        background: #f3e5f5;
        color: #7b1fa2;
        border: 1px solid #e1bee7;
    }

    .btn-delete {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .search-box {
        background: #fffaf2;
        border: 2px solid var(--light-brown);
        border-radius: 30px;
        padding: 8px 20px;
        width: 300px;
    }

    .search-box:focus {
        outline: none;
        border-color: var(--primary-brown);
    }

    .filter-btn {
        background: var(--light-brown);
        color: var(--dark-brown);
        border: none;
        border-radius: 30px;
        padding: 8px 20px;
        transition: all 0.3s;
    }

    .filter-btn:hover {
        background: var(--primary-brown);
        color: white;
    }

    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 20px 0;
    }

    .page-link {
        color: var(--primary-brown);
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid var(--light-brown);
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s;
    }

    .page-link:hover,
    .page-link.active {
        background: var(--primary-brown);
        color: white;
        border-color: var(--primary-brown);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--dark-brown);
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--light-brown);
        margin-bottom: 20px;
    }

    .export-btn {
        background: linear-gradient(to right, #2ecc71, #27ae60);
        color: white;
        border: none;
        border-radius: 30px;
        padding: 10px 25px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-card {
        background: #fffaf2;
        border: 1px solid var(--light-brown);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .level-badge {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        background: #e3f2fd;
        color: #1565c0;
    }

    .level-badge.level-1 {
        background: #e3f2fd;
        color: #1565c0;
    }

    .level-badge.level-2 {
        background: #e8f5e8;
        color: #2e7d32;
    }

    .level-badge.level-3 {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .candidate-info {
        font-size: 0.9rem;
        color: #666;
    }

    .info-row {
        margin: 3px 0;
    }

    .info-label {
        font-weight: 600;
        color: var(--dark-brown);
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="monastery-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="header-title mb-0">
                        <i class="bi bi-house-door-fill"></i> Thungwetaw Monastery
                        <small>| Registration Management System</small>
                    </h1>
                    <p class="mb-0" style="opacity: 0.9;">သုန္ဂေတာဝတိုင်းရင်းသာရိပုတ္တာရသိက္ခာပုဒ်ပညာသင်တန်းကျောင်း</p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span style="opacity: 0.8;">Welcome,</span>
                        <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                    </div>
                    <a href="logout.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Page Title -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="page-title mb-0">
                        <i class="bi bi-list-check"></i> Registration Records
                    </h2>
                </div>


                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <?php foreach ($statistics as $stat): ?>
                    <div class="col-md-4 mb-3">
                        <div class="stat-card level-<?= $stat['level'] ?>">
                            <div class="stat-number"><?= $stat['total'] ?></div>
                            <div class="stat-label">Level <?= $stat['level'] ?> Registrations</div>
                            <div class="mt-3">
                                <small class="d-block">
                                    <span class="badge bg-success">Approved: <?= $stat['approved'] ?></span>
                                </small>
                                <small class="d-block mt-1">
                                    <span class="badge bg-warning">Pending: <?= $stat['pending'] ?></span>
                                </small>
                                <small class="d-block mt-1">
                                    <span class="badge bg-info">With Roll: <?= $stat['has_roll'] ?></span>
                                </small>
                            </div>
                            <a href="?level=<?= $stat['level'] ?>&year=<?= $year ?>"
                                class="btn btn-sm btn-outline-primary mt-3">
                                View Details
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Filters -->
                <div class="filter-card">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-2">
                            <label class="form-label">Level</label>
                            <select name="level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="1" <?= $level == 1 ? 'selected' : '' ?>>Level 1</option>
                                <option value="2" <?= $level == 2 ? 'selected' : '' ?>>Level 2</option>
                                <option value="3" <?= $level == 3 ? 'selected' : '' ?>>Level 3</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Year</label>
                            <select name="year" class="form-select">
                                <?php foreach ($years as $y): ?>
                                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>All Status</option>
                                <option value="pending" <?= $status == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= $status == 'approved' ? 'selected' : '' ?>>Approved
                                </option>
                                <option value="passed" <?= $status == 'passed' ? 'selected' : '' ?>>Passed</option>
                                <option value="failed" <?= $status == 'failed' ? 'selected' : '' ?>>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control search-box"
                                placeholder="Search by name, email, or roll number..."
                                value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn filter-btn w-100">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Registrations Table -->
                <div class="registrations-table">
                    <div class="table-header">
                        <div>
                            <h5 class="mb-0">
                                <i class="bi bi-card-checklist"></i>
                                Registrations
                                <?php if ($level): ?>
                                <span class="level-badge level-<?= $level ?>">Level <?= $level ?></span>
                                <?php endif; ?>
                                <span class="badge bg-light text-dark ms-2"><?= $totalCount ?> records</span>
                            </h5>
                        </div>
                        <div>
                            <span class="me-2">Page <?= $page ?> of <?= $totalPages ?></span>
                        </div>
                    </div>

                    <?php if (count($registrations) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th width="100">Roll No.</th>
                                    <th width="150">Candidate</th>
                                    <th width="100">Level</th>
                                    <th width="120">Status</th>
                                    <th>Monastery Info</th>
                                    <th width="120">Registration Date</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td>
                                        <?php if ($reg['roll_number']): ?>
                                        <span class="roll-number"><?= htmlspecialchars($reg['roll_number']) ?></span>
                                        <?php else: ?>
                                        <span class="text-muted">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($reg['name']) ?></strong>
                                        <div class="candidate-info">
                                            <div class="info-row">
                                                <span class="info-label">Email:</span>
                                                <?= htmlspecialchars($reg['email']) ?>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">DOB:</span>
                                                <?= date('d M, Y', strtotime($reg['dob'])) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="level-badge level-<?= $reg['level'] ?>">
                                            Level <?= $reg['level'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $reg['status'] ?>">
                                            <?= ucfirst($reg['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="candidate-info">
                                            <div class="info-row">
                                                <span class="info-label">Monastery:</span>
                                                <?= htmlspecialchars($reg['monastery_name']) ?>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Abbot:</span>
                                                <?= htmlspecialchars($reg['abbot_name']) ?>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Location:</span>
                                                <?= htmlspecialchars($reg['address_village']) ?>,
                                                <?= htmlspecialchars($reg['address_township']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?= date('d M, Y', strtotime($reg['created_at'])) ?>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('h:i A', strtotime($reg['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <a href="view_candidate.php?id=<?= $reg['candidate_id'] ?>"
                                            class="action-btn btn-view" title="View Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="edit_registration.php?id=<?= $reg['id'] ?>" class="action-btn btn-edit"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <?php if ($reg['status'] == 'pending'): ?>
                                        <a href="approve_registration.php?id=<?= $reg['id'] ?>"
                                            class="action-btn btn-approve" title="Approve">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <h4>No Registrations Found</h4>
                        <p class="text-muted">
                            <?php if ($level || $status !== 'all' || $search): ?>
                            Try adjusting your filters or search criteria.
                            <?php else: ?>
                            No registrations have been submitted yet.
                            <?php endif; ?>
                        </p>
                        <a href="view_registrations.php" class="btn btn-primary">
                            <i class="bi bi-arrow-clockwise"></i> Clear Filters
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="d-flex justify-content-center py-3 border-top">
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                <li>
                                    <a class="page-link"
                                        href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                        <i class="bi bi-chevron-left"></i> Previous
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                for ($i = $startPage; $i <= $endPage; $i++): 
                                ?>
                                <li>
                                    <a class="page-link <?= $i == $page ? 'active' : '' ?>"
                                        href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                <li>
                                    <a class="page-link"
                                        href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                        Next <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Highlight active filters
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('level') || urlParams.has('status') || urlParams.has('search')) {
            document.querySelector('.registrations-table').scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
    </script>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</body>

</html>