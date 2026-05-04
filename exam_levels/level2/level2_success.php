<?php
// public/level2_success.php
session_start();
require_once __DIR__ . '/../../configuration/db.php';

// Check if success data exists
if (!isset($_SESSION['reform_success'])) {
    header('Location: level2.php');
    exit;
}

$success = $_SESSION['reform_success'];
unset($_SESSION['reform_success']); // Clear after showing
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registration Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/level2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    body {
        background-image: image-set(url('../public/assets/images/14.jpg'));
        background-size: cover;
        background-repeat: no-repeat;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
        color: #524222;
        margin: 0;
        min-height: 100vh;
    }

    .success-container {
        max-width: 600px;
        margin: 56px auto 32px auto;
        padding: 38px 32px;
        text-align: center;
        background: rgba(255, 250, 242, 0.98);
        border-radius: 26px;
        box-shadow: 0 10px 30px rgba(124, 94, 59, 0.16), 0 3px 15px rgba(90, 75, 44, .07);
    }

    .success-icon {
        font-size: 85px;
        color: #44b86e;
        margin-bottom: 18px;
        text-shadow: 0 3px 12px #cde4c9;
    }

    h1 {
        color: #786044;
        margin-bottom: 10px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .lead {
        color: #6c5739;
        font-size: 17px;
        margin-bottom: 25px;
        font-weight: 500;
    }

    .details-box {
        background: #fef8ee;
        border-radius: 12px;
        padding: 22px 22px 14px 22px;
        margin: 30px 0 24px 0;
        text-align: left;
        box-shadow: 0 3px 12px rgba(192, 177, 143, 0.09);
    }

    .details-box p {
        margin: 10px 0;
        padding: 6px 0;
        border-bottom: 1px dashed #ecd9b0;
        color: #50401b;
        font-size: 15px;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
    }

    .details-box p:last-child {
        border-bottom: none;
    }

    .btn-group {
        margin-top: 28px;
        display: flex;
        gap: 17px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 13px 30px;
        border-radius: 22px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 17px;
        border: none;
        outline: none;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(192, 168, 135, 0.07);
        background: #7c5e3b;
        color: #fff;
        transition: background 0.12s, color 0.12s;
    }

    .btn-primary {
        background: #7c5e3b;
        color: #fff;
    }

    .btn-secondary {
        background: #e8ddce;
        color: #46351b;
        border: 1.5px solid #9e8d69;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #b7995d;
        color: #b7995d;
    }

    .btn:hover,
    .btn:active,
    .btn:focus {
        background: #543d26;
        color: #fff;
    }

    .btn-outline:hover,
    .btn-outline:focus {
        background: #b7995d;
        color: #fff !important;
    }

    .btn-secondary:hover,
    .btn-secondary:focus {
        border-color: #7c5e3b;
        color: #7c5e3b;
        background: #faeed9;
    }

    .feedback-message {
        font-size: 16px;
        color: #4c8f5a;
        margin-bottom: 32px;
        margin-top: 8px;
    }

    .info-hint {
        margin-top: 36px;
        padding-top: 20px;
        border-top: 1px solid #e1d1b1;
        color: #af955e;
        font-size: 15px;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
    }

    .info-hint i {
        margin-right: 6px;
    }

    @media (max-width:700px) {
        .success-container {
            padding: 6vw 2vw;
        }

        .details-box {
            padding: 10px 4vw;
        }
    }
    </style>
</head>

<body>

    <div class="success-container">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h1>Registration Successful!</h1>
        <p class="lead">Your Level 2 registration has been created successfully.</p>

        <div class="details-box">
            <p><strong>Registration ID:</strong> <?= htmlspecialchars($success['registration_id']) ?></p>
            <p><strong>Candidate Name:</strong> <?= htmlspecialchars($success['candidate_name']) ?></p>
            <p><strong>Level:</strong> <?= htmlspecialchars($success['level']) ?></p>
            <p><strong>Registration Year:</strong> <?= htmlspecialchars($success['registration_year']) ?></p>
            <p><strong>Previous Roll Number:</strong> <?= htmlspecialchars($success['previous_roll']) ?></p>
            <p><strong>Status:</strong> <span style="color: #e9a52c; font-weight: bold;">Pending Approval</span></p>
        </div>

        <div class="feedback-message">
            Your registration is now pending administrative approval.<br>
            You will be notified once it's approved.
        </div>

        <div class="btn-group">
            <a href="level2.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Register Another
            </a>
            <a href="../../layouts/register.php" class="btn btn-secondary">
                <i class="fa-solid fa-home"></i> Back to Home
            </a>
            <a href="#" class="btn btn-outline" onclick="window.print();return false">
                <i class="fa-solid fa-print"></i> Print Receipt
            </a>
            <a href="../../layouts/contact.php" class="btn btn-secondary"
                style="background:#f4ddb5;color:#79502b;border:1.5px solid #e6ca99;">
                <i class="fa-solid fa-message"></i> Give Feedback
            </a>
        </div>

        <div class="info-hint">
            <i class="fa-solid fa-circle-info"></i>
            Please save your Registration ID for future reference.
        </div>
    </div>
</body>

</html>