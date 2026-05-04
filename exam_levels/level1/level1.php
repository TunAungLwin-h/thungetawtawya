<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Level 1 Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../../public/assets/css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    :root {
        --bg-soft: #f5f1e9;
        --card-bg: #ffffff;
        --accent: #7c5e3b;
        --accent-soft: #e3d4bd;
        --border: #d5c7b3;
        --text-main: #3f3427;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        min-height: 100vh;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:
            linear-gradient(145deg, rgba(124, 94, 59, 0.25), rgba(245, 241, 233, 0.85)),
            url('../../public/assets/images/14.jpg') center/cover no-repeat fixed;
        color: var(--text-main);
    }

    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.7), transparent 35%);
        pointer-events: none;
        z-index: -1;
    }

    /* Top language + help bar */
    .top-bar {
        max-width: 920px;
        margin: 14px auto 0;
        padding: 0 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .lang-switch {
        display: inline-flex;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 999px;
        padding: 4px;
        border: 1px solid var(--border);
    }

    .lang-btn {
        border: none;
        background: transparent;
        padding: 8px 16px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
        transition: background 0.18s, color 0.18s;
    }

    .lang-btn.active {
        background: var(--accent);
        color: #fff;
    }

    .top-actions {
        display: inline-flex;
        gap: 8px;
    }

    .help-btn,
    .faq-btn {
        border-radius: 999px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, 0.95);
        color: var(--text-main);
        padding: 7px 14px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .help-btn:hover,
    .faq-btn:hover {
        background: #fff;
    }

    /* Main card */
    .form-container {
        max-width: 920px;
        margin: 14px auto 24px;
        padding: 0 16px 24px;
    }

    .registration-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px 20px 22px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        border: 1px solid var(--border);
    }

    .card-header {
        margin-bottom: 14px;
    }

    .card-header-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--accent);
    }

    .card-header-title i {
        color: var(--accent);
    }

    .card-header p {
        margin: 4px 0 0;
        font-size: 0.9rem;
        color: #6d5c45;
    }

    /* Forms */
    .registration-form {
        display: none;
    }

    .registration-form.active {
        display: block;
    }

    .registration-form h2 {
        margin: 4px 0 12px;
        font-size: 1.02rem;
        color: var(--text-main);
    }

    .form-row {
        display: flex;
        flex-direction: column;
        margin-bottom: 12px;
    }

    label {
        font-weight: 600;
        margin-bottom: 4px;
        font-size: 0.9rem;
    }

    input,
    select {
        padding: 9px 10px;
        border-radius: 10px;
        border: 1px solid var(--border);
        font-size: 0.92rem;
        background: #fcfbf8;
        outline: none;
        transition: border-color 0.15s, background 0.15s;
    }

    input:focus,
    select:focus {
        border-color: var(--accent);
        background: #fff;
    }

    small {
        font-size: 0.78rem;
        color: #8a7a65;
    }

    .form-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 16px;
    }

    button {
        padding: 8px 18px;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .submit-btn {
        background: var(--accent);
        color: #fff;
    }

    .reset-btn {
        background: #f0ebe1;
        color: #4b3f2f;
    }

    .back-btn {
        background: transparent;
        border: 1px solid var(--border);
        color: #4b3f2f;
    }

    .submit-btn:hover {
        background: #694c2f;
    }

    .reset-btn:hover {
        background: #e3d7c4;
    }

    .back-btn:hover {
        background: #faf6ee;
    }

    /* Help overlay */
    .help-box {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .help-box[aria-hidden="false"] {
        display: flex;
    }

    .help-content {
        background: #fffaf2;
        padding: 20px 22px;
        border-radius: 14px;
        max-width: 480px;
        width: 90%;
        box-shadow: 0 16px 40px rgba(0, 0, 0, .25);
    }

    .help-content h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: var(--accent);
    }

    .help-content ul {
        padding-left: 18px;
        margin: 0;
        font-size: 0.9rem;
    }

    .help-content li {
        margin-bottom: 6px;
    }

    .close-help {
        margin-top: 12px;
        background: var(--accent);
        color: #fff;
        padding: 7px 16px;
        border-radius: 999px;
        border: none;
        font-size: 0.86rem;
        cursor: pointer;
    }

    .close-help:hover {
        background: #694c2f;
    }

    .form-review ul {
        max-width: 500px;
        margin: 20px auto;
    }

    .form-review li {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    @media (max-width: 640px) {
        .top-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .top-actions {
            justify-content: flex-end;
        }

        .registration-card {
            padding-inline: 14px;
        }
    }

    .contactus-link {
        display: inline-block;
        margin-top: 16px;
        background: transparent;
        border: 1px solid var(--border);
        color: #4b3f2f;
        padding: 8px 18px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
    }
    </style>
</head>

<body>

    <div class="top-bar">
        <div class="lang-switch">
            <button class="lang-btn active" data-lang="mm">မြန်မာ</button>
            <button class="lang-btn" data-lang="en">English</button>
        </div>

        <div class="top-actions">
            <button type="button" class="help-btn" onclick="toggleHelp()">Help</button>
            <button type="button" class="faq-btn" onclick="direct_to_faq()">FAQ</button>
        </div>
    </div>

    <section class="form-container">
        <div class="registration-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="fa-solid fa-scroll"></i>
                    <span>Level 1 Registration</span>
                </div>
                <p>Fill in your personal and monastery information. Fields are the same in both languages.</p>
            </div>

            <!-- MYANMAR FORM -->
            <form class="registration-form lang-form lang-mm active" action="submit_level1.php" method="post"
                autocomplete="off">

                <h2>ပထမဆင့် မှတ်ပုံတင်ခြင်း</h2>

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-row">
                    <label>ဘွဲ့အမည်</label>
                    <input type="text" name="name" required maxlength="80">
                    <small>စာလုံး ၈၀ ထက် မကျော်ရ</small>
                </div>

                <div class="form-row">
                    <label>အီးမေးလ် (Gmail)</label>
                    <input type="email" name="email" required maxlength="120">
                    <small>Roll Number ကြည့်ရန် အသုံးပြုပါမည်</small>
                </div>

                <div class="form-row">
                    <label>မွေးသက္ကရာဇ်</label>
                    <input type="date" name="dob" required>
                </div>

                <div class="form-row">
                    <label>တိုင်းဒေသကြီး</label>
                    <select name="address_region" id="region-mm" required>
                        <option value="">ရွေးချယ်ပါ</option>
                        <option value="mandalay">မန္တလေး</option>
                        <option value="yangon">ရန်ကုန်</option>
                        <option value="sagaing">စစ်ကိုင်း</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>မြို့နယ်</label>
                    <select name="address_township" id="township-mm" required>
                        <option value="">အရင်တိုင်းရွေးပါ</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>ရပ်ရွာ</label>
                    <input type="text" name="address_village" required maxlength="100">
                </div>

                <div class="form-row">
                    <label>ခမည်းတော်</label>
                    <input type="text" name="father_name" required maxlength="80">
                </div>

                <div class="form-row">
                    <label>မယ်တော်အမည်</label>
                    <input type="text" name="mother_name" required maxlength="80">
                </div>

                <div class="form-row">
                    <label>မိဘနေရပ် (တိုင်း)</label>
                    <select name="parent_address_region" id="parent-region-mm" required>
                        <option value="">ရွေးချယ်ပါ</option>
                        <option value="mandalay">မန္တလေး</option>
                        <option value="yangon">ရန်ကုန်</option>
                        <option value="sagaing">စစ်ကိုင်း</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>မိဘနေရပ် (မြို့နယ်)</label>
                    <select name="parent_address_township" id="parent-township-mm" required>
                        <option value="">အရင်တိုင်းရွေးပါ</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>မိဘနေရပ် (ရပ်ရွာ)</label>
                    <input type="text" name="parent_address_village" required maxlength="120">
                </div>

                <div class="form-row">
                    <label>ကျောင်းတိုက်အမည် </label>
                    <input type="text" name="monastery_name" required maxlength="100">
                </div>

                <div class="form-row">
                    <label> ကျောင်းတိုက်ဆရာတော်ဧ။်ဘွဲ့တော်</label>
                    <input type="text" name="abbot_name" required maxlength="120">
                </div>

                <div class="form-row">
                    <label>နိုင်ငံ (နိုင်ငံခြားသား)</label>
                    <input type="text" name="country" maxlength="60">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">တင်သွင်းပါ</button>
                    <button type="button" class="reset-btn" onclick="location.reload()">ပြန်လည်ရေးထည့်ရန်</button>
                    <a href="../../layouts/register.php">
                        <button type="button" class="back-btn">နောက်သို့</button>
                    </a>
                </div>
            </form>

            <!-- ENGLISH FORM -->
            <form class="registration-form lang-form lang-en" action="submit_level1.php" method="post"
                autocomplete="off">

                <h2>Level 1 Registration</h2>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

                <div class="form-row">
                    <label>Full Name</label>
                    <input type="text" name="name" required maxlength="80">
                    <small>Maximum 80 characters</small>
                </div>

                <div class="form-row">
                    <label>Email (Gmail)</label>
                    <input type="email" name="email" required maxlength="120">
                    <small>Used to check your Roll Number later</small>
                </div>

                <div class="form-row">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>

                <div class="form-row">
                    <label>Region</label>
                    <select name="address_region" id="region-en" required>
                        <option value="">Select Region</option>
                        <option value="mandalay">Mandalay</option>
                        <option value="yangon">Yangon</option>
                        <option value="sagaing">Sagaing</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Township</label>
                    <select name="address_township" id="township-en" required>
                        <option value="">Select Region First</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Village</label>
                    <input type="text" name="address_village" required maxlength="100">
                </div>

                <div class="form-row">
                    <label>Father's Name</label>
                    <input type="text" name="father_name" required maxlength="80">
                </div>

                <div class="form-row">
                    <label>Mother's Name</label>
                    <input type="text" name="mother_name" required maxlength="80">
                </div>

                <div class="form-row">
                    <label>Parent's Address (Region)</label>
                    <select name="parent_address_region" id="parent-region-en" required>
                        <option value="">Select Region</option>
                        <option value="mandalay">Mandalay</option>
                        <option value="yangon">Yangon</option>
                        <option value="sagaing">Sagaing</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Parent's Address (Township)</label>
                    <select name="parent_address_township" id="parent-township-en" required>
                        <option value="">Select Region First</option>
                    </select>
                </div>

                <div class="form-row">
                    <label>Parent's Address (Village)</label>
                    <input type="text" name="parent_address_village" required maxlength="120">
                </div>

                <div class="form-row">
                    <label>Monastery Name</label>
                    <input type="text" name="monastery_name" required maxlength="100">
                </div>

                <div class="form-row">
                    <label>Abbot's Name with Title</label>
                    <input type="text" name="abbot_name" required maxlength="120">
                </div>

                <div class="form-row">
                    <label>Country (For Foreigners Only)</label>
                    <input type="text" name="country" maxlength="60">
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Submit</button>
                    <button type="button" class="reset-btn" onclick="location.reload()">Reset Form</button>
                    <a href="../../layouts/register.php">
                        <button type="button" class="back-btn">Back</button>
                    </a>
                </div>
            </form>

            <section id="helpBox" class="help-box" aria-hidden="true">
                <div class="help-content">
                    <h3>မှတ်ပုံတင်ရန် အကူအညီ</h3>

                    <ul>
                        <li>အချက်အလက်များကို မြန်မာစာ သို့မဟုတ် အင်္ဂလိပ်စာဖြင့် မှန်ကန်စွာ ဖြည့်ပါ</li>
                        <li>ဘွဲ့အမည်၊ မိဘအမည်များကို တူညီစွာ အသုံးပြုပါ</li>
                        <li>တိုင်းဒေသကြီး ရွေးပြီးမှ မြို့နယ် ရွေးနိုင်ပါသည်</li>
                        <li>အမှားရှိပါက “ပြန်လည်ရေးထည့်ရန်” ကိုနှိပ်ပါ</li>
                        <li>မေးခွန်းရှိပါက ကျောင်းတိုက်သို့ ဆက်သွယ်ပါ</li>
                    </ul>

                    <button type="button" class="close-help" onclick="toggleHelp()">ပိတ်ရန်</button>
                </div>
            </section>
        </div>
    </section>

    <script>
    function toggleHelp() {
        const box = document.getElementById('helpBox');
        const isHidden = box.getAttribute('aria-hidden') === 'true';
        box.setAttribute('aria-hidden', !isHidden);
    }

    function direct_to_faq() {
        window.location.href = "../../layouts/faq.php";
    }
    </script>
    <script src="../script/level1-form.js"></script>
</body>

</html>