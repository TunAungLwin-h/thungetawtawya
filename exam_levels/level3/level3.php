<?php
// level3.php 
session_start();
require_once __DIR__ . '/../../configuration/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Level 3 Exam Entry</title>
    <link rel="stylesheet" href="../assets/level3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>

    <?php if ($flash): ?>
    <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="top-bar">
        <div class="top-bar-title">
            <i class="fa-solid fa-om"></i>
            <span>Monastic Examination • Level 3</span>
        </div>
        <a href="../../layouts/register.php" class="back-btn">
            <i class="fa-solid fa-home"></i>
            <span>Back to Levels</span>
        </a>
    </div>

    <div class="lang-contact-row">
        <div class="lang-selector">
            <button class="lang-btn active" data-lang="mm">မြန်မာ</button>
            <button class="lang-btn" data-lang="en">English</button>
        </div>

        <button type="button" class="contact-btn" onclick="toggleContact()">
            <i class="fa-solid fa-phone-volume"></i>
            <span>Contact Monastery</span>
        </button>
    </div>

    <div class="outer-wrap">
        <section class="form-container">
            <div class="form-stripe"></div>
            <i class="fa-solid fa-mountain-sun form-stripe-icon"></i>

            <!-- Myanmar Form -->
            <form class="lang-form lang-mm active" action="submit_level3.php" method="post">
                <h2>
                    <i class="fa-solid fa-scroll"></i>
                    <span>တတိယဆင့် စာမေးပွဲ ဝင်ခွင့်</span>
                </h2>
                <p class="form-intro">
                    ယခင် Level 2 စာမေးပွဲအောင်မြင်ကြောင်းအချက်အလက်များကို အတည်ပြုရန်
                    စဥ်နံပါတ်နှင့် စာမေးပွဲနှစ်ကို ဖြည့်သွင်းပေးပါ။
                </p>

                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-row">
                    <label>ယခင် စဥ်နံပါတ် (Level 2 Roll Number)</label>
                    <input type="text" name="roll_number" required placeholder="Level 2 စဥ်နံပါတ်">
                </div>

                <div class="form-row">
                    <label>စာမေးပွဲနှစ်</label>
                    <input type="number" name="exam_year" required placeholder="2025" min="2000" max="2030">
                </div>

                <div class="form-actions">
                    <button type="submit">
                        <i class="fa fa-search"></i> ရှာဖွေမည်
                    </button>
                </div>
            </form>

            <!-- English Form -->
            <form class="lang-form lang-en" action="submit_level3.php" method="post">
                <h2>
                    <i class="fa-solid fa-scroll"></i>
                    <span>Level 3 Exam Entry</span>
                </h2>
                <p class="form-intro">
                    Please verify your Level 2 roll number and exam year before proceeding to the final level.
                </p>

                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <div class="form-row">
                    <label>Previous Roll Number (Level 2)</label>
                    <input type="text" name="roll_number" required placeholder="Enter Level 2 Roll Number">
                </div>

                <div class="form-row">
                    <label>Exam Year</label>
                    <input type="number" name="exam_year" required placeholder="2025" min="2000" max="2030">
                </div>

                <div class="form-actions">
                    <button type="submit">
                        <i class="fa fa-search"></i> Search
                    </button>
                </div>
            </form>

            <!-- Contact overlay (logic unchanged) -->
            <section id="contactBox" class="contact-box" aria-hidden="true">
                <div class="contact-content">
                    <h3><i class="fa-solid fa-church"></i> Contact Us</h3>

                    <p>If you have questions or issues about Level 3 registration, please contact the monastery office:
                    </p>

                    <ul>
                        <li>Phone: 09-XXXX-XXXX</li>
                        <li>Email: monastery@example.com</li>
                        <li>Office Hours: 9:00 AM – 4:00 PM</li>
                    </ul>

                    <button type="button" class="close-contact" onclick="toggleContact()">Close</button>
                </div>
            </section>

        </section>
    </div>

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
    </script>

    <script>
    function toggleContact() {
        const box = document.getElementById('contactBox');
        const isHidden = box.getAttribute('aria-hidden') === 'true';
        box.setAttribute('aria-hidden', !isHidden);
    }
    </script>

</body>

</html>