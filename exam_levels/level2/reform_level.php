<?php
// public/reform_level.php
session_start();
require_once __DIR__ . '/../../configuration/db.php';

// CSRF check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = 'Invalid request';
    header('Location: level2.php');
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (empty($csrf) || !hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    $_SESSION['flash'] = 'Invalid CSRF token';
    header('Location: level2.php');
    exit;
}

$candidate_id = (int)($_POST['candidate_id'] ?? 0);
$from_registration_id = $_POST['from_registration_id'] ?? '';
$exam_year = (int)($_POST['exam_year'] ?? date('Y'));
$target_level = (int)($_POST['target_level'] ?? 2);
$registration_year = (int)($_POST['registration_year'] ?? date('Y'));

if ($candidate_id <= 0 || empty($from_registration_id)) {
    $_SESSION['flash'] = 'Invalid parameters';
    header('Location: level2.php');
    exit;
}

try {
    // Check if candidate exists
    $stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
    $stmt->execute([$candidate_id]);
    $candidate = $stmt->fetch();

    if (!$candidate) {
        $_SESSION['flash'] = 'Candidate not found';
        header('Location: level2.php');
        exit;
    }

    // Check if already has Level 2 registration for this year
    $stmt = $pdo->prepare("SELECT id FROM registrations 
        WHERE candidate_id = ? AND level = ? AND registration_year = ?");
    $stmt->execute([$candidate_id, $target_level, $registration_year]);
    $existing = $stmt->fetch();

    if ($existing) {
        $_SESSION['flash'] = 'You already have a Level 2 registration for this year';
        header('Location: level2.php');
        exit;
    }

    // Generate new registration ID (UUID)
    $new_reg_id = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

    /* =====================================================
       🔹 ADDED: Generate NEW roll number (LEVEL + YEAR)
       ===================================================== */
    $stmt = $pdo->prepare("
        SELECT roll_number
        FROM registrations
        WHERE level = ? AND registration_year = ?
        ORDER BY roll_number DESC
        LIMIT 1
    ");
    $stmt->execute([$target_level, $registration_year]);
    $lastRoll = $stmt->fetchColumn();

    if ($lastRoll) {
        $lastSeq = (int)substr($lastRoll, -5);
        $newSeq  = $lastSeq + 1;
    } else {
        $newSeq = 1;
    }

    $new_roll = sprintf(
        '%d-%d-%05d',
        $registration_year,
        $target_level,
        $newSeq
    );
    /* ===================================================== */

    // Insert new Level 2 registration
    $stmt = $pdo->prepare("
        INSERT INTO registrations 
        (
            id,
            candidate_id,
            level,
            registration_year,
            roll_number,          -- 🔹 ADDED
            status,
            previous_registration_id,
            created_at,
            updated_at
        )
        VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())
    ");

    $stmt->execute([
        $new_reg_id,
        $candidate_id,
        $target_level,
        $registration_year,
        $new_roll,              // 🔹 ADDED
        $from_registration_id
    ]);

    /* =====================================================
       🔹 ADDED: Fetch previous roll (correct source)
       ===================================================== */
    $stmt = $pdo->prepare("SELECT roll_number FROM registrations WHERE id = ?");
    $stmt->execute([$from_registration_id]);
    $previous_roll = $stmt->fetchColumn();
    /* ===================================================== */

    // Store success data in session for success page
    $_SESSION['reform_success'] = [
        'registration_id' => $new_reg_id,
        'candidate_name' => $candidate['name'],
        'level' => $target_level,
        'registration_year' => $registration_year,
        'previous_roll' => $previous_roll
    ];

    header('Location: level2_success.php');
    exit;

} catch (Exception $e) {
    error_log("Reform error: " . $e->getMessage());
    $_SESSION['flash'] = 'Error creating registration. Please try again.';
    header('Location: level2.php');
    exit;
}