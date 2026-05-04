<?php
// faq.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Exam System</title>
    <style>
    /* BODY & THEME */
    body {
        background: #f9f5ee;
        font-family: 'Segoe UI', sans-serif;
        color: #4b3f2f;
        margin: 0;
        padding: 0;
    }

    /* PAGE HEADER */
    header {
        background: #efe6d8;
        padding: 20px;
        text-align: center;
        color: #7c5e3b;
        font-size: 28px;
        font-weight: 600;
    }

    /* FAQ SECTION */
    .faq-container {
        max-width: 800px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .faq-item {
        border: 1px solid #c9b79c;
        border-radius: 12px;
        margin-bottom: 12px;
        background: #fffaf2;
        overflow: hidden;
    }

    .faq-question {
        padding: 16px 20px;
        cursor: pointer;
        font-weight: 600;
        position: relative;
    }

    .faq-question::after {
        content: '+';
        position: absolute;
        right: 20px;
        font-size: 20px;
        transition: transform 0.3s ease;
    }

    .faq-item.active .faq-question::after {
        transform: rotate(45deg);
    }

    .faq-answer {
        padding: 0 20px 16px 20px;
        display: none;
        font-size: 14px;
        line-height: 1.5;
        color: #5b4a36;
    }

    .faq-item.active .faq-answer {
        display: block;
    }

    /* BACK BUTTON */
    .back-btn {
        display: inline-block;
        margin: 20px;
        padding: 10px 22px;
        border-radius: 22px;
        border: 1px solid #7c5e3b;
        color: #7c5e3b;
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        .faq-question {
            font-size: 16px;
        }
    }
    </style>
</head>

<body>

    <header>FAQ - Exam System</header>

    <div class="faq-container">

        <div class="faq-item">
            <div class="faq-question">Q1: How do I register for Level 1?</div>
            <div class="faq-answer">
                You can register by filling the Level 1 registration form completely and submitting it.
                Make sure your information matches official documents.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Q2: Can I edit my submission after submitting?</div>
            <div class="faq-answer">
                Editing is only possible before the submission deadline. Contact the administration if you need to
                update information.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Q3: What should I do if I forget my roll number?</div>
            <div class="faq-answer">
                Please contact the monastery office with your full name and date of birth to retrieve your roll number.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">Q4: Are foreign students allowed?</div>
            <div class="faq-answer">
                Yes, foreign students can register. Fill the country field in the registration form.
            </div>
        </div>

    </div>

    <a href="register.php" class="back-btn">Back to Registration</a>

    <script>
    // Simple accordion for FAQ
    document.querySelectorAll('.faq-question').forEach(item => {
        item.addEventListener('click', () => {
            const parent = item.parentElement;
            parent.classList.toggle('active');
        });
    });
    </script>

</body>

</html>