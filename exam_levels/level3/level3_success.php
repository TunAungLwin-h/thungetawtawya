<?php
session_start();
require_once __DIR__ . '/../../configuration/db.php';

if (!isset($_SESSION['level3_success'])) {
    header('Location: level3.php');
    exit;
}

$success = $_SESSION['level3_success'];
unset($_SESSION['level3_success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Level 3 Registration Successful</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/css/level2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
    body {
        background-image: image-set(url('../public/assets/images/14.jpg'));
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 100vh;
        font-family: 'Segoe UI', 'Pyidaungsu', 'Zawgyi-One', 'Times New Roman', Times, serif;
        color: #58431c;
        margin: 0;
    }

    .success-container {
        background: rgba(255, 250, 242, 0.97);
        border-radius: 26px;
        box-shadow: 0 10px 42px rgba(124, 94, 59, 0.18);
        padding: 44px 30px 28px 30px;
        max-width: 650px;
        width: 99%;
        margin: 60px auto 28px auto;
        text-align: center;
        animation: fadeIn .4s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .success-icon {
        font-size: 104px;
        color: #44b86e;
        margin-bottom: 12px;
        text-shadow: 0 3px 14px #dbecd9;
        animation: trophyWave 1.6s infinite alternate;
    }

    @keyframes trophyWave {
        0% {
            transform: rotate(-4deg);
        }

        100% {
            transform: rotate(7deg);
        }
    }

    h1 {
        color: #826844;
        margin-bottom: 12px;
        font-weight: 800;
        letter-spacing: .02em;
        font-size: 2.1em;
    }

    .lead {
        color: #6c5739;
        font-size: 17px;
        margin-bottom: 28px;
        font-weight: 500;
    }

    .details-box {
        background: #fff8ee;
        border-radius: 14px;
        padding: 26px 25px 12px 25px;
        margin: 32px 0 18px 0;
        text-align: left;
        box-shadow: 0 2px 7px rgba(192, 177, 143, 0.10);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 13px 0;
        border-bottom: 1px dashed #ecd9b0;
        font-size: 15px;
        color: #402f10;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #795a28;
    }

    .detail-value {
        color: #43a726;
        font-weight: 500;
    }

    .registration-id {
        background: #f5e3b7;
        padding: 7px 16px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 18px;
        color: #79502b;
        border: 1px solid #e9d49e;
    }

    .btn-group {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 38px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 13px 32px;
        border-radius: 22px;
        text-decoration: none;
        font-weight: 600;
        font-size: 16.5px;
        border: none;
        outline: none;
        background: #7c5e3b;
        color: #fff;
        box-shadow: 0 2px 8px rgba(192, 168, 135, 0.09);
        transition: background 0.12s, color 0.12s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
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
        background: #faeed9;
        color: #826844;
    }

    .feedback-btn {
        background: #faf0de;
        color: #8f681e;
        border: 1.5px solid #ecd891;
    }

    .feedback-btn:hover,
    .feedback-btn:active,
    .feedback-btn:focus {
        background: #ffe8bb;
        color: #826844;
    }

    .info-hint {
        margin-top: 38px;
        padding-top: 18px;
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
            padding: 7vw 2vw;
        }

        .details-box {
            padding: 8px 4vw;
        }
    }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-trophy"></i>
        </div>

        <h1>Level 3 Registration Successful</h1>
        <p class="lead">You have successfully registered for the Level 3 examination.</p>

        <div class="details-box">
            <div class="detail-row">
                <span class="detail-label">Registration ID:</span>
                <span class="registration-id"><?= htmlspecialchars($success['registration_id']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Candidate Name:</span>
                <span class="detail-value"><?= htmlspecialchars($success['candidate_name']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Level:</span>
                <span class="detail-value"><?= htmlspecialchars($success['level']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Registration Year:</span>
                <span class="detail-value"><?= htmlspecialchars($success['registration_year']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Exam Year:</span>
                <span class="detail-value"><?= htmlspecialchars($success['exam_year']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Previous Level 2 Roll:</span>
                <span class="detail-value"><?= htmlspecialchars($success['previous_roll']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value" style="color: #d59307; font-weight: bold;">
                    <i class="fas fa-clock"></i> Pending Approval
                </span>
            </div>
        </div>
        <div class="btn-group">
            <a href="level3.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Register Another
            </a>
            <a href="../../public/index.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <button onclick="printReceipt()" class="btn btn-outline" type="button">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="../../layouts/contact.php" class="btn feedback-btn">
                <i class="fa-solid fa-message"></i> Give Feedback
            </a>
        </div>
        <div class="info-hint">
            <i class="fa-solid fa-circle-info"></i>
            Please save your Registration ID for future reference.
        </div>
    </div>

    <script>
    function printReceipt() {
        const printContent = `
            <html>
            <head>
                <title>Level 3 Registration Receipt</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; background: #fffdf7;}
                    .receipt { max-width: 500px; margin: 0 auto; border: 2px solid #7c5e3b; padding: 22px 18px; border-radius: 14px;}
                    .header { text-align: center; border-bottom: 2px solid #b7995d; padding-bottom: 15px; margin-bottom: 20px; }
                    .header h2 { color: #b7995d; margin: 0; }
                    .details { margin: 20px 0; }
                    .row { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px dashed #ccc; }
                    .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ccc; padding-top: 15px; }
                </style>
            </head>
            <body>
                <div class="receipt">
                    <div class="header">
                        <h2>Level 3 Registration Receipt</h2>
                        <p><strong>Date:</strong> <?= date('Y-m-d H:i') ?></p>
                    </div>
                    <div class="details">
                        <div class="row"><strong>Registration ID:</strong> <?= htmlspecialchars($success['registration_id']) ?></div>
                        <div class="row"><strong>Candidate:</strong> <?= htmlspecialchars($success['candidate_name']) ?></div>
                        <div class="row"><strong>Level:</strong> <?= htmlspecialchars($success['level']) ?></div>
                        <div class="row"><strong>Registration Year:</strong> <?= htmlspecialchars($success['registration_year']) ?></div>
                        <div class="row"><strong>Exam Year:</strong> <?= htmlspecialchars($success['exam_year']) ?></div>
                        <div class="row"><strong>Previous Roll:</strong> <?= htmlspecialchars($success['previous_roll']) ?></div>
                        <div class="row"><strong>Status:</strong> Pending Approval</div>
                    </div>
                    <div class="footer">
                        This is an official registration receipt.<br>
                        Please keep this for your records.<br>
                        Generated on: <?= date('Y-m-d') ?>
                    </div>
                </div>
            </body>
            </html>
        `;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }
    </script>
</body>

</html>