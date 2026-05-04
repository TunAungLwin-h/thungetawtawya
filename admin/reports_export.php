<?php
// admin/reports_export.php
// Export CSV for registrations or candidates.
// Usage examples:
//  - /admin/reports_export.php?type=registrations&year=2025&status=passed
//  - /admin/reports_export.php?type=candidates&q=John

session_start();
require_once __DIR__ . '/../configuration/db.php';

require_once __DIR__ . '/auth/admin_guard.php';

$type = $_GET['type'] ?? 'registrations';
$type = in_array($type, ['registrations', 'candidates']) ? $type : 'registrations';

// Build query and headers
try {
    if ($type === 'candidates') {
        $q = trim((string)($_GET['q'] ?? ''));
        $sql = "SELECT id, name, roll_number, status, created_at FROM candidates";
        $params = [];
        if ($q !== '') {
            $sql .= " WHERE name LIKE :q OR roll_number LIKE :q";
            $params[':q'] = "%$q%";
        }
        $sql .= " ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $filename = 'candidates_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['id', 'name', 'roll_number', 'status', 'created_at']);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($out, [
                $row['id'],
                $row['name'],
                $row['roll_number'],
                $row['status'],
                $row['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }

    // registrations
    $year = isset($_GET['year']) ? (int)$_GET['year'] : null;
    $status = trim((string)($_GET['status'] ?? ''));
    $q = trim((string)($_GET['q'] ?? ''));

    $sql = "SELECT r.id AS registration_id, r.candidate_id, c.name AS candidate_name,
                   r.level, r.registration_year, r.roll_number, r.status, r.created_at
            FROM registrations r
            LEFT JOIN candidates c ON c.id = r.candidate_id
            WHERE 1=1";
    $params = [];

    if ($year) {
        $sql .= " AND r.registration_year = :year";
        $params[':year'] = $year;
    }
    if ($status !== '') {
        $sql .= " AND r.status = :status";
        $params[':status'] = $status;
    }
    if ($q !== '') {
        $sql .= " AND (r.roll_number LIKE :q OR c.name LIKE :q)";
        $params[':q'] = "%$q%";
    }

    $sql .= " ORDER BY r.registration_year DESC, r.level ASC, r.roll_number ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $filename = 'registrations_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['registration_id', 'candidate_id', 'candidate_name', 'level', 'registration_year', 'roll_number', 'status', 'created_at']);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            $row['registration_id'],
            $row['candidate_id'],
            $row['candidate_name'],
            $row['level'],
            $row['registration_year'],
            $row['roll_number'],
            $row['status'],
            $row['created_at'],
        ]);
    }
    fclose($out);
    exit;

} catch (Exception $e) {
    // Fallback: show a friendly message when export fails
    error_log("reports_export error: " . $e->getMessage());
    $_SESSION['flash'] = 'Export failed. Please try again.';
    header('Location: ./reports.php');
    exit;
}