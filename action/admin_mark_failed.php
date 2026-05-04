<?php
// admin/admin_mark_passed.php
session_start();
require_once __DIR__ . '/../configuration/db.php';

// 1. Auth Check
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit;
}

// 2. CSRF Validation
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['flash'] = "Invalid CSRF token.";
    header('Location: ../admin/registrations.php');
    exit;
}

// 3. Get POST values
$registration_id = trim($_POST['registration_id'] ?? '');
$candidate_id    = trim($_POST['candidate_id'] ?? '');
$passed_year     = (int)($_POST['passed_year'] ?? date('Y'));
$marks           = trim($_POST['marks'] ?? ''); // This maps to the 'remarks' column

if (empty($candidate_id)) {
    $_SESSION['flash'] = "No candidate selected.";
    header('Location: ../admin/registrations.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Check if we are updating an existing registration or creating a new one
    if (!empty($registration_id)) {
        // UPDATE: The 'remarks' column exists in 'registrations' table
        $stmt = $pdo->prepare("
            UPDATE registrations 
            SET status = 'failed', 
                passed_year = :passed_year, 
                remarks = :remarks, 
                updated_at = NOW() 
            WHERE id = :registration_id
        ");
        $stmt->execute([
            ':passed_year'     => $passed_year,
            ':remarks'         => $marks,
            ':registration_id' => $registration_id
        ]);
    } else {
        // INSERT: Create a Level 1 record if none exists
        $new_reg_id = bin2hex(random_bytes(16)); 
        
        // Get candidate roll number from 'candidates' table
        $stmtRoll = $pdo->prepare("SELECT roll_number FROM candidates WHERE id = :candidate_id");
        $stmtRoll->execute([':candidate_id' => $candidate_id]);
        $roll = $stmtRoll->fetchColumn() ?: null;

        $stmt = $pdo->prepare("
            INSERT INTO registrations (id, candidate_id, level, registration_year, roll_number, status, passed_year, remarks, created_at, updated_at)
            VALUES (:id, :candidate_id, 1, :reg_year, :roll, 'failed', :passed_year, :remarks, NOW(), NOW())
        ");
        $stmt->execute([
            ':id'          => $new_reg_id,
            ':candidate_id'=> $candidate_id,
            ':reg_year'    => date('Y'),
            ':roll'        => $roll,
            ':passed_year' => $passed_year,
            ':remarks'     => $marks
        ]);
    }

    // UPDATE CANDIDATE: Note - The 'candidates' table does NOT have a 'remarks' column.
    // We only update the 'status' here.
    $stmt2 = $pdo->prepare("
        UPDATE candidates 
        SET status = 'failed', 
            updated_at = NOW() 
        WHERE id = :candidate_id
    ");
    $stmt2->execute([':candidate_id' => $candidate_id]);

    $pdo->commit();
    $_SESSION['flash'] = "Candidate marked as failed successfully.";

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log("Mark Failed Error: " . $e->getMessage());
    $_SESSION['flash'] = "Error: " . $e->getMessage();
}

header('Location: ../admin/registrations.php');
exit;
