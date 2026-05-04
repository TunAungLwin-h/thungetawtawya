<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';

// CSRF protection
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')
) {
    die("Unauthorized Access");
}

$candidate_id     = (int) ($_POST['candidate_id'] ?? 0);
$registration_id  = $_POST['registration_id'] ?? '';

if (!$candidate_id || !$registration_id) {
    die("Invalid request data");
}

try {
    $pdo->beginTransaction();

    // 1. Verify previous registration is FAILED
    $stmt = $pdo->prepare("
        SELECT level 
        FROM registrations 
        WHERE id = ? 
          AND candidate_id = ? 
          AND status = 'failed'
        LIMIT 1
    ");
    $stmt->execute([$registration_id, $candidate_id]);
    $level = $stmt->fetchColumn();

    if (!$level) {
        throw new Exception("Invalid registration state.");
    }

  // 2. Generate SAFE sequence number (ALWAYS WORKS)
$year = (int) date('Y');

$stmt = $pdo->prepare("
    INSERT INTO roll_sequences (year, level, last_seq)
    VALUES (:y, :l, LAST_INSERT_ID(1))
    ON DUPLICATE KEY UPDATE
        last_seq = LAST_INSERT_ID(last_seq + 1)
");
$stmt->execute([
    ':y' => $year,
    ':l' => $level
]);

// 3. Fetch generated sequence
$stmt = $pdo->query("SELECT LAST_INSERT_ID()");
$new_seq = (int) $stmt->fetchColumn();

if ($new_seq <= 0) {
    throw new Exception("Failed to generate roll sequence.");
}



    // 4. Generate roll number
    $new_roll = $year . "-" . $level . "-" . str_pad($new_seq, 5, '0', STR_PAD_LEFT);

    // 5. Create new registration
    $new_reg_id = bin2hex(random_bytes(16));

    $stmt = $pdo->prepare("
        INSERT INTO registrations (
            id,
            candidate_id,
            level,
            registration_year,
            roll_number,
            status,
            previous_registration_id,
            created_at
        ) VALUES (
            :id,
            :cid,
            :lvl,
            :yr,
            :roll,
            'pending',
            :prev,
            NOW()
        )
    ");
    $stmt->execute([
        ':id'   => $new_reg_id,
        ':cid'  => $candidate_id,
        ':lvl'  => $level,
        ':yr'   => $year,
        ':roll' => $new_roll,
        ':prev' => $registration_id
    ]);

    $pdo->commit();

    // ================= UI SUCCESS =================
    echo "
<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
</head>
<body style='
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f4f7fb;
    font-family:Segoe UI, Tahoma, Arial, sans-serif;
'>
    <div style='
        background:#ffffff;
        padding:40px 45px;
        max-width:520px;
        width:100%;
        border-radius:14px;
        box-shadow:0 10px 30px rgba(0,0,0,0.1);
        text-align:center;
    '>
        <div style='
            width:70px;
            height:70px;
            margin:0 auto 20px;
            border-radius:50%;
            background:#e9f9ef;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:34px;
            color:#28a745;
        '>✓</div>

        <h1 style='color:#28a745;margin-bottom:12px;font-size:28px;'>
            Registration Successful
        </h1>

        <p style='color:#555;font-size:15px;line-height:1.6;'>
            Your new registration has been submitted successfully.
        </p>

        <p style='color:#666;font-size:14px;line-height:1.6;'>
            Please wait for admin approval to receive your roll number.
        </p>

        <div style='
            margin:22px 0;
            padding:14px;
            background:#f8f9fa;
            border-radius:10px;
            font-size:15px;
        '>
            <strong>Registration ID</strong><br>
            <span style='font-size:18px;color:#333;'>
                " . htmlspecialchars($new_reg_id) . "
            </span>
        </div>

        <a href='../layouts/register.php' style='
            display:inline-block;
            padding:12px 28px;
            background:#28a745;
            color:#fff;
            text-decoration:none;
            border-radius:30px;
            font-size:15px;
        '>Return to Portal</a>
    </div>
</body>
</html>";
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Database Error: " . htmlspecialchars($e->getMessage()));
}