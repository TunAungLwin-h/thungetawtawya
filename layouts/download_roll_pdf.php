<?php
require_once '../configuration/db.php';

$email = trim((string)($_GET['email'] ?? ''));
$level = (int)($_GET['level'] ?? 1);
$year  = (int)($_GET['year'] ?? date('Y'));

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid request.');
}

$stmt = $pdo->prepare("
    SELECT
        c.name,
        c.dob,
        c.monastery_name,
        c.email,
        r.roll_number,
        r.level,
        r.registration_year
    FROM candidates c
    INNER JOIN registrations r ON r.candidate_id = c.id
    WHERE c.email = ?
      AND r.level = ?
      AND r.registration_year = ?
      AND r.status IN ('approved', 'passed')
      AND r.roll_number IS NOT NULL
    LIMIT 1
");
$stmt->execute([$email, $level, $year]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    exit('No candidate found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roll Number Slip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5efe8;
            color: #3b2b1d;
            margin: 0;
            padding: 32px 16px;
        }

        .slip {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        }

        .heading {
            text-align: center;
            margin-bottom: 24px;
        }

        .roll {
            font-size: 2rem;
            font-weight: 700;
            color: #8a5a2d;
            margin: 10px 0 0;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .field {
            padding: 14px 16px;
            border-radius: 12px;
            background: #faf6f1;
        }

        .label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #8d7a68;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 28px;
        }

        .btn {
            border: none;
            border-radius: 999px;
            padding: 12px 20px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-primary {
            background: #8a5a2d;
            color: #fff;
        }

        .btn-secondary {
            background: #ece3d7;
            color: #3b2b1d;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .slip {
                box-shadow: none;
                padding: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="slip">
        <div class="heading">
            <h1>Thungwetaw Tawya Monastery Exam</h1>
            <p>Official Roll Number Slip</p>
            <div class="roll"><?= htmlspecialchars($candidate['roll_number']) ?></div>
        </div>

        <div class="grid">
            <div class="field">
                <div class="label">Candidate Name</div>
                <div><?= htmlspecialchars($candidate['name']) ?></div>
            </div>
            <div class="field">
                <div class="label">Email</div>
                <div><?= htmlspecialchars($candidate['email']) ?></div>
            </div>
            <div class="field">
                <div class="label">Date of Birth</div>
                <div><?= htmlspecialchars(date('d M Y', strtotime($candidate['dob']))) ?></div>
            </div>
            <div class="field">
                <div class="label">Monastery</div>
                <div><?= htmlspecialchars($candidate['monastery_name']) ?></div>
            </div>
            <div class="field">
                <div class="label">Exam Level</div>
                <div>Level <?= htmlspecialchars((string)$candidate['level']) ?></div>
            </div>
            <div class="field">
                <div class="label">Registration Year</div>
                <div><?= htmlspecialchars((string)$candidate['registration_year']) ?></div>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">Print Slip</button>
            <a class="btn btn-secondary" href="check_roll_number.php">Back to Roll Inquiry</a>
        </div>
    </div>
</body>
</html>
