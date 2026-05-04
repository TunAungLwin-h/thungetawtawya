<?php
// admin/admin_create_next.php
session_start();
require_once __DIR__ . '/../configuration/db.php';

// Check admin login
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit;
}

// Validate CSRF
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['flash'] = "Invalid CSRF token.";
    header('Location: ../admin/registrations.php');
    exit;
}

// Get POST values
$prev_registration_id = $_POST['prev_registration_id'] ?? null;
$target_level = (int)($_POST['target_level'] ?? 1);
$registration_year = (int)($_POST['registration_year'] ?? date('Y'));

if (!$prev_registration_id || $target_level < 2 || $target_level > 3) {
    $_SESSION['flash'] = "Invalid previous registration or target level.";
    header('Location: ../admin/registrations.php');
    exit;
}

try {
    // Fetch previous registration & candidate_id
    $stmt = $pdo->prepare("SELECT candidate_id FROM registrations WHERE id = :prev_id");
    $stmt->execute([':prev_id' => $prev_registration_id]);
    $candidate_id = $stmt->fetchColumn();

    if (!$candidate_id) {
        $_SESSION['flash'] = "Previous registration not found.";
        header('Location: ../admin/registrations.php');
        exit;
    }

    // Check if next level registration already exists
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM registrations
        WHERE candidate_id = :candidate_id AND level = :level
    ");
    $stmt->execute([
        ':candidate_id' => $candidate_id,
        ':level' => $target_level
    ]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['flash'] = "Next level registration already exists for this candidate.";
        header('Location: ../admin/registrations.php');
        exit;
    }

    // Generate new roll number
    $stmt = $pdo->prepare("
        SELECT id, last_seq FROM roll_sequences 
        WHERE year = :year AND level = :level
    ");
    $stmt->execute([
        ':year' => $registration_year,
        ':level' => $target_level
    ]);
    $seq = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($seq) {
        $new_seq = $seq['last_seq'] + 1;
        $roll_seq_id = $seq['id'];

        // Update last_seq in roll_sequences
        $stmt = $pdo->prepare("UPDATE roll_sequences SET last_seq = :new_seq WHERE id = :id");
        $stmt->execute([':new_seq' => $new_seq, ':id' => $roll_seq_id]);
    } else {
        // If no sequence exists for this year/level, create it
        $new_seq = 1;
        $stmt = $pdo->prepare("INSERT INTO roll_sequences (year, level, last_seq) VALUES (:year, :level, :last_seq)");
        $stmt->execute([':year' => $registration_year, ':level' => $target_level, ':last_seq' => $new_seq]);
    }

    // Format roll number: YEAR-LEVEL-SEQ (padded 5 digits)
    $roll_number = $registration_year . '-' . $target_level . '-' . str_pad($new_seq, 5, '0', STR_PAD_LEFT);

    // Insert new registration
    $new_registration_id = bin2hex(random_bytes(16)); // 32 char hex
    $stmt = $pdo->prepare("
        INSERT INTO registrations 
        (id, candidate_id, level, registration_year, roll_number, status, previous_registration_id, created_at, updated_at)
        VALUES
        (:id, :candidate_id, :level, :year, :roll_number, 'pending', :prev_id, NOW(), NOW())
    ");
    $stmt->execute([
        ':id' => $new_registration_id,
        ':candidate_id' => $candidate_id,
        ':level' => $target_level,
        ':year' => $registration_year,
        ':roll_number' => $roll_number,
        ':prev_id' => $prev_registration_id
    ]);

    $_SESSION['flash'] = "Next level registration created successfully (Roll: $roll_number).";

} catch (PDOException $e) {
    error_log("Create Next Level Error: " . $e->getMessage());
    $_SESSION['flash'] = "Error creating next level registration.";
}

header('Location: ../admin/registrations.php');
exit;
