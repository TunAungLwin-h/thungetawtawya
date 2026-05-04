<?php
// admin/admin_mark_passed.php

session_start();
require_once __DIR__ . '/../configuration/db.php';

// 1. Auth Check
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit;
}

// 2. CSRF Check
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['flash'] = "Invalid CSRF token.";
    header('Location: ../admin/registrations.php');
    exit;
}

// 3. Get Input
$registration_id = trim($_POST['registration_id'] ?? '');
$candidate_id    = trim($_POST['candidate_id'] ?? '');
$passed_year     = (int)($_POST['passed_year'] ?? date('Y'));
$marks           = trim($_POST['marks'] ?? '');

// Validate Candidate ID
if (empty($candidate_id)) {
    $_SESSION['flash'] = "Error: No candidate selected.";
    header('Location: ../admin/registrations.php');
    exit;
}

try {
    // Start Transaction to ensure data consistency
    $pdo->beginTransaction();

    // =========================================================
    // SCENARIO 1: UPDATE EXISTING REGISTRATION
    // =========================================================
    if (!empty($registration_id) && $registration_id !== '0') {
        
        // Update the specific registration row
        $stmt = $pdo->prepare("
            UPDATE registrations 
            SET status = 'passed', 
                passed_year = :passed_year, 
                remarks = :marks, 
                updated_at = NOW() 
            WHERE id = :registration_id
        ");
        
        $stmt->execute([
            ':passed_year'     => $passed_year,
            ':marks'           => $marks,
            ':registration_id' => $registration_id
        ]);

    } 
    // =========================================================
    // SCENARIO 2: CREATE NEW REGISTRATION (Level 1 Catch-up)
    // =========================================================
    else {
        // If no registration ID exists (e.g., Level 1 candidate), we create a passed record.
        
        // 1. Generate new ID
        $new_reg_id = bin2hex(random_bytes(16)); // 32 char hex
        $registration_year = date('Y');
        $level = 1; // Default to level 1 if creating from scratch

        // 2. Fetch existing roll number from candidate table if available
        $stmtRoll = $pdo->prepare("SELECT roll_number FROM candidates WHERE id = :candidate_id");
        $stmtRoll->execute([':candidate_id' => $candidate_id]);
        $roll = $stmtRoll->fetchColumn(); 

        // 3. Insert new passed registration
        $stmt = $pdo->prepare("
            INSERT INTO registrations 
            (id, candidate_id, level, registration_year, roll_number, status, passed_year, remarks, created_at, updated_at) 
            VALUES 
            (:id, :candidate_id, :level, :registration_year, :roll_number, 'passed', :passed_year, :remarks, NOW(), NOW())
        ");
        
        $stmt->execute([
            ':id'                => $new_reg_id,
            ':candidate_id'      => $candidate_id,
            ':level'             => $level,
            ':registration_year' => $registration_year,
            ':roll_number'       => $roll, // Can be null
            ':passed_year'       => $passed_year,
            ':remarks'           => $marks
        ]);
    }

    // =========================================================
    // SYNC CANDIDATE TABLE
    // =========================================================
    // Always update the main candidate status to 'passed'
    $stmt2 = $pdo->prepare("
        UPDATE candidates 
        SET status = 'passed', 
            updated_at = NOW() 
        WHERE id = :candidate_id
    ");
    $stmt2->execute([':candidate_id' => $candidate_id]);

    // Commit Transaction
    $pdo->commit();
    $_SESSION['flash'] = "Candidate marked as Passed successfully.";

} catch (Exception $e) {
    // Rollback changes on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    error_log("Mark Passed Error: " . $e->getMessage());
    $_SESSION['flash'] = "Error marking candidate as passed: " . $e->getMessage();
}

// Redirect back
header('Location: ../admin/registrations.php');
exit;
