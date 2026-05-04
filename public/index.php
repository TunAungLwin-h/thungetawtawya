<?php
// index.php - main website
require_once __DIR__ . '/../configuration/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thu Nge Taw Taw Ya Monastery | Buddhist Education Center</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;600&family=Poppins:wght@400;600;700&family=Roboto+Slab:wght@400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/css/index.css" />
</head>

<body>
    <header class="navbar">
        <?php include("../layouts/navbar.php"); ?>
    </header>

    <section class="hero-banner">
        <img src="../public/assets/images/16.jpg" alt="Monastery View">
        <div class="hero-text fade-in">
            <h1>Welcome To</h1>
            <p>A Center for Buddhist Education, Meditation, and Dhamma Practice</p>
            <a href="../layouts/register.php" class="magic-button">
                <i class="fas fa-pen-alt"></i> Exam Registration
            </a>
        </div>
    </section>

    <section class="founder-section fade-in">
        <div class="container">
            <div class="founder-grid">
                <div class="founder-visual">
                    <div class="image-frame">
                        <img src="../public/assets/images/12.jpg" alt="Venerable Sayadaw">
                    </div>
                </div>

                <div class="founder-details">
                    <span class="section-subtitle">The Founder</span>
                    <h2>Venerable ThuNgeDaw TawYa Sayadaw</h2>

                    <div class="founder-bio">
                        <p><strong>Thu Ngwe Daw Taw Ya Sayadaw</strong> is the revered founder and spiritual guide of
                            our monastery. With profound wisdom, Sayadaw has dedicated his life to nurturing generations
                            of monks in the path of Dhamma.</p>

                        <p>His teachings emphasize the integration of scriptural study with dedicated practice, creating
                            a balanced approach to spiritual development under the monastery's guidance.</p>
                    </div>
                    <div class="founder-actions" style="margin-top: 30px;">
                        <a href="about.php" class="magic-button outline">
                            Learn More About Us <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>

                    <div class="quote-box">
                        <i class="fas fa-quote-left"></i>
                        <blockquote>
                            "To walk the path of Dhamma is to cultivate clarity in mind, purity in heart, and courage in
                            action."
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="exam-info fade-in">
        <div class="container">
            <div class="exam-grid-layout">
                <div class="exam-description">
                    <span class="section-subtitle">Academic Excellence</span>
                    <h2>Sasana Alankara (Tharthana Lingara) Examination</h2>
                    <p>The Tharthana Lingara exam is a rigorous scriptural challenge designed for dedicated monks
                        and scholars. It aims to preserve the authentic teachings of the Tipitaka through systematic
                        study and evaluation.</p>

                    <div class="level-cards-container">
                        <div class="level-card">
                            <div class="level-num">01</div>
                            <div class="level-info">
                                <h4>Foundation (Mula)</h4>
                                <p>Focuses on basic Pali grammar and foundational Buddhist concepts (Suttas).</p>
                            </div>
                        </div>
                        <div class="level-card">
                            <div class="level-num">02</div>
                            <div class="level-info">
                                <h4>Intermediate (Nge/Latt)</h4>
                                <p>Deeper scriptural analysis, Vinaya (monastic discipline), and meditation theory.</p>
                            </div>
                        </div>
                        <div class="level-card">
                            <div class="level-num">03</div>
                            <div class="level-info">
                                <h4>Advanced (Gyi)</h4>
                                <p>Mastery of Abhidhamma and complex Pali linguistics.</p>
                            </div>
                        </div>
                    </div>

                    <div class="success-path">
                        <i class="fas fa-award"></i>
                        <p><strong>After Level 3:</strong> Graduates are honored with the title of Sasana Alankara,
                            recognized as qualified Dhamma teachers capable of guiding the next generation of the
                            Sangha.</p>
                    </div>

                    <a href="../layouts/register.php" class="magic-button">
                        <i class="fas fa-file-signature"></i> Register for Exam
                    </a>

                    <a href="about.php" class="magic-button">Learn More About Us <i class="fas fa-arrow-right"></i></a>
                </div>

                <div class="exam-visual">
                    <div class="exam-image-wrapper">
                        <img src="../public/assets/images/100.jpg" alt="Dhamma Examination Hall">
                        <div class="image-overlay-badge">National Standard</div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <section class="events-section fade-in">
        <div class="container">
            <div class="section-title">
                <h2>Upcoming Dhamma Events</h2>
                <p>Stay updated with our monastery's schedules and festivals</p>
            </div>

            <div class="events-grid">
                <div class="event-card">
                    <div class="event-date">MARCH 15, 2026</div>
                    <div class="event-info">
                        <h3>Abhidhamma Exam</h3>
                        <p>Annual examination for Level 1 and Level 2 students will be held in the main hall.</p>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">APRIL 12, 2026</div>
                    <div class="event-info">
                        <h3>Thingyan Retreat</h3>
                        <p>Join us for a 7-day silent meditation retreat during the Myanmar New Year water festival.</p>
                    </div>
                </div>

                <div class="event-card">
                    <div class="event-date">MAY 20, 2026</div>
                    <div class="event-info">
                        <h3>Kason Full Moon</h3>
                        <p>Banyan tree watering ceremony and evening Dhamma talk by the Head Sayadaw.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include("../layouts/footer.php") ?>



    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
        document.getElementById('year').textContent = new Date().getFullYear();
    });
    </script>
</body>

</html>
