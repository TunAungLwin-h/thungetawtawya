<?php
// action/check_pre_roll.php
session_start();
require_once __DIR__ . '/../configuration/db.php';

header('Content-Type: application/json; charset=utf-8');

function json($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

// Auth + method + CSRF
if (empty($_SESSION['admin_logged_in'])) json(['success' => false, 'message' => 'Not authenticated'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json(['success' => false, 'message' => 'Invalid method'], 405);

$csrf = $_POST['csrf_token'] ?? '';
if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$pre_roll = trim((string)($_POST['pre_roll'] ?? ''));
$passed_year = (int)($_POST['passed_year'] ?? 0);

if ($pre_roll === '') json(['success' => false, 'message' => 'pre_roll is required'], 400);

try {
    $stmt = $pdo->prepare("SELECT r.*, c.name AS candidate_name, 
                           c.roll_number AS candidate_roll
                           FROM registrations r
                           JOIN candidates c ON c.id = r.candidate_id
                           WHERE (r.roll_number = :roll OR c.roll_number = :roll) 
                           AND r.level = 1
                           LIMIT 1");
    $stmt->execute([':roll' => $pre_roll]);
    $reg = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reg) {
        json(['success' => false, 'message' => 'Registration with that roll not found']);
    }

    // Check if passed - using your actual database fields
    $passed = ($reg['status'] === 'passed');
    $reg_pass_year = $reg['passed_year'] ?? null;

    // If caller supplied passed_year, check consistency
    $warning = null;
    if ($passed && $passed_year > 0 && $reg_pass_year && ((int)$reg_pass_year !== $passed_year)) {
        $warning = 'Passed year in record differs from provided passed_year';
    }

    json([
        'success' => true,
        'data' => [
            'candidate_id' => (int)$reg['candidate_id'],
            'candidate_name' => $reg['candidate_name'],
            'registration_id' => $reg['id'],
            'status' => $reg['status'],
            'passed' => $passed,
            'passed_year' => $reg_pass_year,
            'roll_number' => $reg['roll_number'] ?? $reg['candidate_roll'],
            'warning' => $warning
        ]
    ]);

} catch (Exception $e) {
    error_log("check_pre_roll error: " . $e->getMessage());
    json(['success' => false, 'message' => 'Server error'], 500);
}