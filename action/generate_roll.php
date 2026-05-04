<?php
// admin/generate_roll.php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: ../admin/login.php');
    exit;
}
require_once __DIR__ . '/../configuration/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// CSRF
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $_SESSION['flash'] = 'Invalid request (CSRF).';
    header('Location: ../admin/registrations.php');
    exit;
}

$candidate_id = (int)($_POST['candidate_id'] ?? 0);
$registration_id = (int)($_POST['registration_id'] ?? 0);

if ($candidate_id <= 0) {
    $_SESSION['flash'] = 'Invalid candidate ID.';
    header('Location: ../admin/registrations.php');
    exit;
}

try {
    // Determine if this is Level 1 (no registration yet) or higher level
    if ($registration_id <= 0) {
        // Level 1 - no registration record yet
        $level = 1;
        
        // Check if candidate exists and is approved
        $stmt = $pdo->prepare("SELECT status FROM candidates WHERE id = ?");
        $stmt->execute([$candidate_id]);
        $candidate = $stmt->fetch();
        
        if (!$candidate) {
            throw new Exception("Candidate not found.");
        }
        
        if ($candidate['status'] !== 'approved') {
            throw new Exception("Candidate must be approved before generating roll number.");
        }
        
        // Check if candidate already has a roll number
        $stmt = $pdo->prepare("SELECT roll_number FROM candidates WHERE id = ?");
        $stmt->execute([$candidate_id]);
        $existing_roll = $stmt->fetchColumn();
        
        if ($existing_roll) {
            throw new Exception("Candidate already has a roll number: $existing_roll");
        }
    } else {
        // Level 2 or 3 - has registration record
        $stmt = $pdo->prepare("SELECT level, status FROM registrations WHERE id = ? AND candidate_id = ?");
        $stmt->execute([$registration_id, $candidate_id]);
        $registration = $stmt->fetch();
        
        if (!$registration) {
            throw new Exception("Registration not found.");
        }
        
        $level = $registration['level'];
        
        if ($registration['status'] !== 'approved') {
            throw new Exception("Registration must be approved before generating roll number.");
        }
        
        // Check if registration already has a roll number
        $stmt = $pdo->prepare("SELECT roll_number FROM registrations WHERE id = ?");
        $stmt->execute([$registration_id]);
        $existing_roll = $stmt->fetchColumn();
        
        if ($existing_roll) {
            throw new Exception("Registration already has a roll number: $existing_roll");
        }
    }

    $year = (int)date('Y');

    // Ensure roll_sequences table exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS roll_sequences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            year YEAR NOT NULL,
            level TINYINT NOT NULL,
            last_seq INT NOT NULL DEFAULT 0,
            UNIQUE KEY year_level (year, level)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Start transaction
    $pdo->beginTransaction();

    // Ensure a row exists for this year & level
    $ins = $pdo->prepare("INSERT INTO roll_sequences (year, level, last_seq) VALUES (:y, :l, 0) ON DUPLICATE KEY UPDATE last_seq = last_seq");
    $ins->execute([':y' => $year, ':l' => $level]);

    // Increment sequence
    $upd = $pdo->prepare("UPDATE roll_sequences SET last_seq = last_seq + 1 WHERE year = :y AND level = :l");
    $upd->execute([':y' => $year, ':l' => $level]);

    // Fetch sequence
    $sel = $pdo->prepare("SELECT last_seq FROM roll_sequences WHERE year = :y AND level = :l");
    $sel->execute([':y' => $year, ':l' => $level]);
    $seq = (int)$sel->fetchColumn();

    // Format roll number: YYYY-Level-Sequence
    // Get active roll format
$fmt = $pdo->query("
    SELECT * FROM roll_formats WHERE is_active = 1 ORDER BY id DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$yearValue = '';
if ($fmt['year_format'] === 'YYYY') {
    $yearValue = date('Y');
} elseif ($fmt['year_format'] === 'YY') {
    $yearValue = date('y');
}

$seqFormatted = str_pad($seq, $fmt['seq_length'], '0', STR_PAD_LEFT);

$roll = str_replace(
    ['{YEAR}', '{LEVEL}', '{SEQ}'],
    [$yearValue, $level, $seqFormatted],
    $fmt['format_string']
);


    // Update based on level
    if ($level == 1 && $registration_id <= 0) {
        // Level 1: update candidates table only (no registration record yet)
        $update = $pdo->prepare("UPDATE candidates SET roll_number = :roll, updated_at = NOW() WHERE id = :cid");
        $update->execute([':roll' => $roll, ':cid' => $candidate_id]);
    } else {
        // Level 2 or 3: update registration table
        $update = $pdo->prepare("UPDATE registrations SET roll_number = :roll, updated_at = NOW() WHERE id = :rid");
        $update->execute([':roll' => $roll, ':rid' => $registration_id]);
        
        // Also update candidate's roll number for Level 1 if exists as registration
        if ($level == 1) {
            $update_candidate = $pdo->prepare("UPDATE candidates SET roll_number = :roll, updated_at = NOW() WHERE id = :cid");
            $update_candidate->execute([':roll' => $roll, ':cid' => $candidate_id]);
        }
    }

    $pdo->commit();
    $_SESSION['flash'] = "Roll number generated successfully: $roll";

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = 'Error: ' . $e->getMessage();
}

header('Location: ../admin/registrations.php');
exit;
?>
