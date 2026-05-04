<?php
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

$id     = (int)($_POST['id'] ?? 0);     // candidate ID
$action = $_POST['action'] ?? '';

if ($id <= 0) {
    $_SESSION['flash'] = 'Invalid candidate ID.';
    header('Location: ../admin/registrations.php');
    exit;
}

try {
    // Start transaction for consistency
    $pdo->beginTransaction();

    if ($action === 'approve') {
        // Update candidate status
        $stmt = $pdo->prepare("UPDATE candidates SET status='approved', updated_at=NOW() WHERE id=?");
        $stmt->execute([$id]);
        
        // Also update latest registration status if exists
        $stmt = $pdo->prepare("
            UPDATE registrations 
            SET status='approved', updated_at=NOW() 
            WHERE id = (
                SELECT id FROM (
                    SELECT id FROM registrations 
                    WHERE candidate_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 1
                ) AS latest
            )
        ");
        $stmt->execute([$id]);

        $_SESSION['flash'] = "Registration approved successfully.";

    } elseif ($action === 'reject') {
        // Update candidate status
        $stmt = $pdo->prepare("UPDATE candidates SET status='rejected', updated_at=NOW() WHERE id=?");
        $stmt->execute([$id]);
        
        // Also update latest registration status if exists
        $stmt = $pdo->prepare("
            UPDATE registrations 
            SET status='rejected', updated_at=NOW() 
            WHERE id = (
                SELECT id FROM (
                    SELECT id FROM registrations 
                    WHERE candidate_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 1
                ) AS latest
            )
        ");
        $stmt->execute([$id]);

        $_SESSION['flash'] = "Registration rejected.";

    } elseif ($action === 'delete') {
        // Delete all registrations first
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE candidate_id=?");
        $stmt->execute([$id]);

        // Then delete candidate
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE id=?");
        $stmt->execute([$id]);

        $_SESSION['flash'] = "Candidate deleted permanently.";

    } else {
        $_SESSION['flash'] = "Unknown action.";
    }

    $pdo->commit();
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash'] = "Server error: " . $e->getMessage();
}

header("Location: ../admin/registrations.php");
exit;

?>
