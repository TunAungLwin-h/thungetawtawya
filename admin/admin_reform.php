<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';

header('Content-Type: application/json; charset=utf-8');

function json($data, $code=200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

if (empty($_SESSION['admin_logged_in'])) json(['success' => false, 'message' => 'Not authenticated'], 401);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') json(['success' => false, 'message' => 'Invalid method'], 405);

$csrf = $_POST['csrf_token'] ?? '';
if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
}

$candidate_id = isset($_POST['candidate_id']) ? (int)$_POST['candidate_id'] : 0;
$from_registration_id = isset($_POST['from_registration_id']) ? (int)$_POST['from_registration_id'] : 0;
$target_level = isset($_POST['target_level']) ? (int)$_POST['target_level'] : 0;
$registration_year = isset($_POST['registration_year']) ? (int)$_POST['registration_year'] : (int)date('Y');

if ($candidate_id <= 0 || $target_level <= 0) json(['success' => false, 'message' => 'Missing parameters'], 400);

try {
    // Check candidate exists
    $stmt = $pdo->prepare("SELECT id, name FROM candidates WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $candidate_id]);
    $cand = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$cand) json(['success' => false, 'message' => 'Candidate not found'], 404);

    
    if ($from_registration_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM registrations WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $from_registration_id]);
        $from = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$from) json(['success' => false, 'message' => 'Source registration not found'], 404);
        if (($from['status'] ?? '') !== 'passed') json(['success' => false, 'message' => 'Source registration is not passed'], 409);
    } else {
        
        $stmt = $pdo->prepare("SELECT * FROM registrations WHERE candidate_id = :cid AND status = 'passed' AND level < :tlevel ORDER BY level DESC LIMIT 1");
        $stmt->execute([':cid' => $candidate_id, ':tlevel' => $target_level]);
        $from = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$from) {
            json(['success' => false, 'message' => 'No prior passed registration found for candidate'], 409);
        }
    }

    
    $stmt = $pdo->prepare("SELECT id, status FROM registrations WHERE candidate_id = :cid AND level = :lvl LIMIT 1");
    $stmt->execute([':cid' => $candidate_id, ':lvl' => $target_level]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($existing) {
        json(['success' => false, 'message' => 'Registration for target level already exists (id: ' . $existing['id'] . ')', 'existing' => $existing], 409);
    }

    // Create new registration row with status 'pending' (or 'created' per your business rule)
    $ins = $pdo->prepare("INSERT INTO registrations (candidate_id, level, registration_year, status, created_at, updated_at)
                          VALUES (:cid, :level, :year, 'pending', NOW(), NOW())");
    $ins->execute([':cid' => $candidate_id, ':level' => $target_level, ':year' => $registration_year]);

    $newId = (int)$pdo->lastInsertId();

    json(['success' => true, 'message' => 'Created registration for level ' . $target_level, 'registration_id' => $newId]);

} catch (Exception $e) {
    error_log("admin_reform error: " . $e->getMessage());
    json(['success' => false, 'message' => 'Server error creating registration'], 500);
}