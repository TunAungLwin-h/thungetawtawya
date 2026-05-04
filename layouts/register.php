<?php
require_once '../configuration/db.php';
$status = $pdo->query("SELECT * FROM registration_publish WHERE id = 1")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>သူငယ်တော်တောရ သာသနလင်္ကာရကျော်စာမေးပွဲ - Choose Your Exam Level</title>
    <link rel="stylesheet" href="assets/css/register_page.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<style>
/* --- Card Layout --- */
.level-cards {
    max-width: 1200px;
    margin: 120px auto 40px auto;
    /* leave space for fixed nav */
    padding: 0 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    grid-gap: 24px;
}

.level-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.level-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.16);
}

/* --- Foldable Section --- */
.card-description {
    max-height: 0;
    overflow: hidden;
    opacity: 0;
    transition: max-height 0.4s ease-in-out, opacity 0.4s ease-in-out, padding 0.4s ease-in-out;
    background: #fdfdfd;
    padding: 0 18px;
}

/* Expanded State */
.level-card.active .card-description {
    max-height: 500px;
    opacity: 1;
    padding-top: 16px;
    padding-bottom: 18px;
    border-bottom: 1px solid #eee;
}

/* --- Fixed Footer --- */
.card-footer {
    padding: 15px;
    background: #fff;
    text-align: center;
    border-top: 1px solid #f0f0f0;
}

.btn-level {
    display: block;
    width: 100%;
    padding: 12px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    border: none;
    background: #b87333;
    color: #fff;
    cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
}

.btn-level:hover:not(.disabled) {
    background: #a16227;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18);
}

.btn-level.disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* --- Header & Icon --- */
.card-header {
    cursor: pointer;
    position: relative;
    display: block;
}

.card-header img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
}

.level-badge {
    position: absolute;
    top: 14px;
    left: 14px;
    background: rgba(183, 115, 51, 0.9);
    color: #fff;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 700;
}

/* Toggle icon */
.toggle-icon {
    position: absolute;
    right: 20px;
    bottom: 12px;
    color: white;
    font-size: 1.2rem;
    transition: transform 0.4s ease;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.level-card.active .toggle-icon {
    transform: rotate(180deg);
}

/* --- CONSOLIDATED REGISTER NAV --- */
.register-nav {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    z-index: 1000;
    padding: 15px 0;
}

.nav-container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Back Link Styling */
.nav-back-link {
    text-decoration: none;
    color: var(--dark-brown);
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: 0.3s;
}

.nav-back-link i {
    font-size: 0.8rem;
    color: var(--primary-gold);
}

.nav-back-link:hover {
    color: var(--primary-gold);
    transform: translateX(-5px);
}

/* Contact link */
.nav-contactus-link {
    text-decoration: none;
    color: var(--dark-brown);
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: 0.3s;
}

.nav-contactus-link i {
    font-size: 0.8rem;
    color: var(--primary-gold);
}

.nav-contactus-link:hover {
    color: var(--primary-gold);
    transform: translateX(3px);
}

/* Language Switcher Styling */
.nav-lang-switcher {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--cream);
    padding: 6px 18px;
    border-radius: 50px;
    border: 1px solid rgba(212, 175, 55, 0.3);
}

.nav-lang-switcher i {
    color: var(--text-light);
    font-size: 0.9rem;
}

.nav-lang-switcher .lang-btn {
    background: none;
    border: none;
    padding: 0;
    margin: 0;
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-light);
    cursor: pointer;
    transition: 0.3s;
}

.nav-lang-switcher .lang-btn.active {
    color: var(--dark-brown);
    text-decoration: underline;
    text-underline-offset: 4px;
    text-decoration-color: var(--primary-gold);
}

.nav-lang-switcher .divider {
    color: #ddd;
    font-size: 0.8rem;
}

/* Quote + header */
.level-select .header {
    max-width: 1200px;
    margin: 110px auto 10px auto;
    padding: 0 20px 0 50px;
}

.level-select .quote {
    max-width: 900px;
    margin: 0 auto 20px auto;
    padding: 12px 20px;
    background: #fff7eb;
    border-radius: 30px;
    border-left: 6px solid #d4af37;
    font-style: italic;
    text-align: center;
}

/* Quick stats */
.quick-stats {
    max-width: 900px;
    margin: 10px auto 25px auto;
    padding: 16px 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
}

.stat-item {
    min-width: 160px;
    padding: 12px 16px;
    border-radius: 12px;
    background: #fffdf8;
    border: 1px solid rgba(212, 175, 55, 0.3);
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.3rem;
    font-weight: 700;
    color: #b87333;
}

.stat-label {
    font-size: 0.85rem;
}

/* Footer links */
.footer-links {
    max-width: 900px;
    margin: 0 auto 40px auto;
    padding: 0 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    justify-content: center;
}

.footer-links a {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #fff;
    border: 1px solid rgba(212, 175, 55, 0.25);
    text-decoration: none;
    color: var(--dark-brown);
    font-size: 0.85rem;
    transition: 0.25s ease;
}

.footer-links a:hover {
    background: #fff6e5;
    transform: translateY(-1px);
}

/* Responsive adjustment */
@media (max-width: 768px) {
    .level-cards {
        margin-top: 130px;
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .nav-back-link span {
        display: none;
    }

    .nav-back-link i {
        font-size: 1.2rem;
    }
}
</style>

<body>
    <!-- Top nav -->
    <nav class="register-nav">
        <div class="nav-container">
            <a href="../public/index.php" class="nav-back-link">
                <i class="fas fa-arrow-left"></i>
                <span data-en="Back to Home" data-mm="မူလစာမျက်နှာသို့">Back to Home</span>
            </a>

            <a href="contact.php" class="nav-contactus-link">
                <i class="fas fa-phone-alt"></i>
                <span data-en="Contacts Us & Feedback" data-mm="ဆက်သွယ်ရန်နှင့် Feedback ပေးရန်">
                    Contact Us & Feedback
                </span>
            </a>

            <div class="nav-lang-switcher">
                <i class="fas fa-globe"></i>
                <button class="lang-btn active" data-lang="en">EN</button>
                <span class="divider">|</span>
                <button class="lang-btn" data-lang="mm">MM</button>
            </div>
        </div>
    </nav>

    <section class="level-select">
        <div class="header">
            <h2>
                <i class="fas fa-graduation-cap"></i>
                <span data-en="ThuNgeDawTawya TharthanarLingarakyaw Examination"
                    data-mm="သူငယ်တော်တောရ သာသနလင်္ကာရကျော်စာမေးပွဲ">
                    ThuNgeDawTawya TharthanarLingarakyaw Examination
                </span>
            </h2>
        </div>

        <p class="quote" data-en="Choose the path of learning, one step at a time..."
            data-mm="ပညာရပ်အတွက် လမ်းစကို တစ်ဆင့်စီ ရွေးချယ်ပါ...">
            "Choose the path of learning, one step at a time..."
        </p>

        <!-- LEVEL CARDS WRAPPER -->
        <div class="level-cards">

            <!-- Level 1 Card -->
            <div class="level-card level-1" data-order="1">
                <div class="card-header" onclick="toggleLevel(this)">
                    <span class="level-badge" data-en="Level 1" data-mm="အဆင့် ၁">Level 1</span>
                    <img src="../public/assets/images/4.jpg" alt="Level 1 Exam" loading="lazy">
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>

                <div class="card-description">
                    <div class="inner-content">
                        <h3>
                            <i class="fas fa-seedling"></i>
                            <span data-en="Level 1 Details" data-mm="ပထမအဆင့် အသေးစိတ်">Level 1 Details</span>
                        </h3>
                        <p data-en="For new candidates..." data-mm="အသစ်စတင်သည့် စာဖြေသူများအတွက်...">
                            Complete registration with personal information, address, and parent details.
                        </p>
                        <div class="requirements">
                            <ul>
                                <li data-en="Personal Info" data-mm="ကိုယ်ရေးအချက်အလက်">✓ Personal Info</li>
                                <li data-en="Address" data-mm="နေရပ်လိပ်စာ">✓ Address</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if ($status['level1']): ?>
                    <a href="../exam_levels/level1/level1.php" class="btn-level">Register Now</a>
                    <?php else: ?>
                    <button class="btn-level disabled" disabled>Registration Closed</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Level 2 Card -->
            <div class="level-card level-2" data-order="2">
                <div class="card-header" onclick="toggleLevel(this)">
                    <span class="level-badge" data-en="Level 2" data-mm="အဆင့် ၂">Level 2</span>
                    <img src="../public/assets/images/5.jpg" alt="Level 2 Exam" loading="lazy">
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>

                <div class="card-description">
                    <div class="inner-content">
                        <h3>
                            <i class="fas fa-tree"></i>
                            <span data-en="Level 2 Registration Details" data-mm="ဒုတိယအဆင့် စာရင်းသွင်း အသေးစိတ်">
                                Level 2 Registration Details
                            </span>
                        </h3>

                        <p data-en="For candidates who successfully passed Level 1. Registration is simple and faster because your previous information is already stored in the system."
                            data-mm="ပထမအဆင့် အောင်မြင်ပြီးသူများအတွက် ဖြစ်ပါသည်။ ယခင်အချက်အလက်များ စနစ်တွင်ရှိပြီးဖြစ်သောကြောင့် မြန်ဆန်စွာ စာရင်းသွင်းနိုင်ပါသည်။">
                            For candidates who successfully passed Level 1. Registration is simple and faster because
                            your previous information is already stored in the system.
                        </p>

                        <p data-en="You only need to verify your roll number and exam year to continue to the next level."
                            data-mm="နောက်တစ်ဆင့်သို့ တက်ရောက်ရန် စဥ်နံပါတ်နှင့် စာမေးပွဲနှစ်ကို အတည်ပြုရန်သာ လိုအပ်ပါသည်။">
                            You only need to verify your roll number and exam year to continue to the next level.
                        </p>

                        <div class="requirements">
                            <ul>
                                <li data-en="Passed Level 1 Exam" data-mm="ပထမအဆင့် အောင်မြင်ပြီးရမည်">
                                    ✓ Passed Level 1 Exam
                                </li>
                                <li data-en="Level 1 Roll Number" data-mm="ပထမအဆင့် စဥ်နံပါတ်">
                                    ✓ Level 1 Roll Number
                                </li>
                                <li data-en="Previous Exam Year" data-mm="ယခင်စာမေးပွဲနှစ်">
                                    ✓ Previous Exam Year
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if ($status['level2']): ?>
                    <a href="../exam_levels/level2/level2.php" class="btn-level">Register Now</a>
                    <?php else: ?>
                    <button class="btn-level disabled" disabled>Registration Closed</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Level 3 Card -->
            <div class="level-card level-3" data-order="3">
                <div class="card-header" onclick="toggleLevel(this)">
                    <span class="level-badge" data-en="Level 3" data-mm="အဆင့် ၃">Level 3</span>
                    <img src="../public/assets/images/867.jpg" alt="Level 3 Exam" loading="lazy">
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>

                <div class="card-description">
                    <div class="inner-content">
                        <h3>
                            <i class="fas fa-mountain"></i>
                            <span data-en="Level 3 Registration Details" data-mm="တတိယအဆင့် စာရင်းသွင်း အသေးစိတ်">
                                Level 3 Registration Details
                            </span>
                        </h3>

                        <p data-en="This is the final and most advanced level of the examination process. Only candidates who passed Level 2 are eligible to apply."
                            data-mm="ဤအဆင့်သည် စာမေးပွဲလုပ်ငန်းစဉ်၏ နောက်ဆုံးနှင့် အမြင့်ဆုံးအဆင့် ဖြစ်ပါသည်။ ဒုတိယအဆင့် အောင်မြင်သူများသာ လျှောက်ထားနိုင်ပါသည်။">
                            This is the final and most advanced level of the examination process. Only candidates who
                            passed Level 2 are eligible to apply.
                        </p>

                        <p data-en="All previous records will be verified automatically. Please ensure your Level 2 roll number and exam year are correct before submission."
                            data-mm="ယခင်မှတ်တမ်းများကို အလိုအလျောက် စစ်ဆေးမည်ဖြစ်ပါသည်။ ဒုတိယအဆင့် စဥ်နံပါတ်နှင့် စာမေးပွဲနှစ်ကို မှန်ကန်စွာ ထည့်သွင်းပါ။">
                            All previous records will be verified automatically. Please ensure your Level 2 roll number
                            and exam year are correct before submission.
                        </p>

                        <div class="requirements">
                            <ul>
                                <li data-en="Passed Level 2 Exam" data-mm="ဒုတိယအဆင့် အောင်မြင်ပြီးရမည်">
                                    ✓ Passed Level 2 Exam
                                </li>
                                <li data-en="Level 2 Roll Number" data-mm="ဒုတိယအဆင့် စဥ်နံပါတ်">
                                    ✓ Level 2 Roll Number
                                </li>
                                <li data-en="Previous Exam Year" data-mm="ယခင်စာမေးပွဲနှစ်">
                                    ✓ Previous Exam Year
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <?php if ($status['level3']): ?>
                    <a href="../exam_levels/level3/level3.php" class="btn-level">Register Now</a>
                    <?php else: ?>
                    <button class="btn-level disabled" disabled>Registration Closed</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Statistics -->
        <div class="quick-stats">
            <div class="stat-item">
                <span class="stat-number" id="level1-count">1,247</span>
                <span class="stat-label" data-en="Level 1 Passed" data-mm="ပထမအဆင့် အောင်မြင်သူ">Level 1 Passed</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="level2-count">843</span>
                <span class="stat-label" data-en="Level 2 Passed" data-mm="ဒုတိယအဆင့် အောင်မြင်သူ">Level 2 Passed</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="level3-count">443</span>
                <span class="stat-label" data-en="Level 3 Passed" data-mm="တတိယအဆင့် အောင်မြင်သူ">Level 3 Passed</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" id="total-candidates">2,156</span>
                <span class="stat-label" data-en="Total Candidates" data-mm="စုစုပေါင်း စာဖြေသူ">Total Candidates</span>
            </div>
        </div>

        <!-- Additional links -->
        <div class="footer-links">
            <a href="check_roll_number.php">
                <i class="fas fa-search"></i>
                <span data-en="RollNumber Inquiry" data-mm="ခုံနံပါတ်စုံစမ်းရန်">RollNumber Inquiry</span>
            </a>
            <a href="../layouts/announcements.php">
                <i class="fas fa-calendar-alt"></i>
                <span data-en="Exam Schedule" data-mm="စာမေးပွဲအချိန်ဇယား">Exam Schedule</span>
            </a>
            <a href="contact.php">
                <i class="fas fa-phone-alt"></i>
                <span data-en="Contact Us" data-mm="ဆက်သွယ်ရန်">Contact Us</span>
            </a>
            <a href="../layouts/faq.php">
                <i class="fas fa-question-circle"></i>
                <span data-en="FAQ" data-mm="မေးလေ့ရှိသောမေးခွန်းများ">FAQ</span>
            </a>
        </div>
    </section>

    <script>
    function toggleLevel(headerElement) {
        const card = headerElement.closest('.level-card');
        const isActive = card.classList.contains('active');

        // Close all cards
        document.querySelectorAll('.level-card').forEach(c => c.classList.remove('active'));

        // Open clicked card if it was not active
        if (!isActive) {
            card.classList.add('active');
        }
    }

    // Language switching
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.lang-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const lang = this.dataset.lang;

            document.querySelectorAll('[data-en], [data-mm]').forEach(el => {
                const text = lang === 'mm' ? el.dataset.mm : el.dataset.en;
                if (!text) return;

                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.placeholder = text;
                } else {
                    el.textContent = text;
                }
            });

            document.documentElement.dir = 'ltr';
            document.documentElement.lang = lang === 'mm' ? 'my' : 'en';
        });
    });

    // Quick stats
    function updateStats() {
        const stats = {
            level1: 1247,
            level2: 843,
            level3: 443,
            total: 2156
        };

        document.getElementById('level1-count').textContent = stats.level1.toLocaleString();
        document.getElementById('level2-count').textContent = stats.level2.toLocaleString();
        document.getElementById('level3-count').textContent = stats.level3.toLocaleString();
        document.getElementById('total-candidates').textContent = stats.total.toLocaleString();
    }

    document.addEventListener('DOMContentLoaded', updateStats);

    // Intersection animation
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.level-card').forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        card.style.transitionDelay = `${index * 0.15}s`;
        observer.observe(card);
    });

    // Hover lift
    document.querySelectorAll('.level-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Fallback for images
    document.querySelectorAll('img').forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const parent = this.parentElement;
            const fallback = document.createElement('div');
            fallback.className = 'image-fallback';
            fallback.innerHTML = '<i class="fas fa-image"></i>';
            fallback.style.cssText =
                'width:100%;height:200px;background:#f9f5eb;display:flex;align-items:center;justify-content:center;color:#8b4513;font-size:48px;border-radius:8px;';
            parent.insertBefore(fallback, this);
        });
    });
    </script>
</body>

</html>
