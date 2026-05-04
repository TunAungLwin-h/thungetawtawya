<?php
// level2.php
session_start();
require_once __DIR__ . '/../../configuration/db.php';


// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// Flash message
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Level 2 Exam Entry</title>
    <link rel="stylesheet" href="../assets/level2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>


    <?php if ($flash): ?>
    <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="top-bar">
        <div class="top-bar-title">
            <span>Monastic Examination • Level 2</span>
        </div>
        <a href="../../layouts/register.php" class="home-btn">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Levels</span>
        </a>
    </div>

    <!-- Language Selector -->
    <section class="language-selector">
        <div class="lang-switch-group">
            <button class="lang-btn active" data-lang="mm">မြန်မာ</button>
            <button class="lang-btn" data-lang="en">English</button>
        </div>

        <button type="button" class="contact-btn" onclick="toggleContact()">
            <i class="fa-solid fa-phone-volume"></i>
            <span>Contact Monastery</span>
        </button>
    </section>

    <section class="form-container">
        <div class="form-shell">
            <div class="side-pane"></div>
            <i class="fa-solid fa-bell side-pane-icon"></i>

            <div class="form-shell-header">
                <h1>
                    <i class="fa-solid fa-scroll"></i>
                    <span>Level 2 Exam Entry Verification</span>
                </h1>
                <p>Please confirm your previous roll number and exam year to continue.</p>
            </div>

            <!-- Myanmar Form -->
            <form class="simple-form lang-form lang-mm active" action="submit_level2.php" method="post">
                <h2>ဒုတိယဆင့် စာမေးပွဲ ဝင်ခွင့် (မြန်မာ)</h2>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-row">
                    <label>ယခင် စဥ်နံပါတ် (Level 1 Roll Number)</label>
                    <input type="text" name="roll_number" required placeholder="Level 1 Roll Number">
                </div>

                <div class="form-row">
                    <label>စာမေးပွဲနှစ်</label>
                    <input type="number" name="exam_year" required placeholder="2025">
                </div>

                <div class="form-actions">
                    <button type="submit"><i class="fa fa-arrow-right"></i> ရှာဖွေမည်</button>
                </div>
            </form>

            <!-- English Form -->
            <form class="simple-form lang-form lang-en" action="submit_level2.php" method="post">
                <h2>Level 2 Exam Entry (English)</h2>
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-row">
                    <label>Previous Roll Number (Level 1)</label>
                    <input type="text" name="roll_number" required placeholder="Enter Level 1 Roll Number">
                </div>

                <div class="form-row">
                    <label>Exam Year</label>
                    <input type="number" name="exam_year" required placeholder="2025">
                </div>

                <div class="form-actions">
                    <button type="submit"><i class="fa fa-arrow-right"></i> Search</button>
                </div>
            </form>
        </div>

        <!-- Contact overlay -->
        <section id="contactBox" class="contact-box" aria-hidden="true">
            <div class="contact-content">
                <h3><i class="fa-solid fa-church"></i> Contact Us</h3>

                <p>If you have questions or issues about Level 2 registration, please contact the monastery office:</p>

                <ul>
                    <li>Phone: 09-XXXX-XXXX</li>
                    <li>Email: monastery@example.com</li>
                    <li>Office Hours: 9:00 AM – 4:00 PM</li>
                </ul>

                <button type="button" class="close-contact" onclick="toggleContact()">Close</button>
            </div>
        </section>
    </section>

    <script>
    const buttons = document.querySelectorAll('.lang-btn');
    const forms = document.querySelectorAll('.lang-form');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const lang = button.dataset.lang;
            forms.forEach(form => {
                form.classList.toggle('active', form.classList.contains('lang-' + lang));
            });
        });
    });

    function toggleContact() {
        const box = document.getElementById('contactBox');
        const isHidden = box.getAttribute('aria-hidden') === 'true';
        box.setAttribute('aria-hidden', !isHidden);
    }
    </script>

</body>

</html>