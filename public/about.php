<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | ThuNgeDaw Tawya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../public/assets/css/index.css">
    <link rel="stylesheet" href="../public/assets/css/about.css">
</head>

<body>
    <?php include("../layouts/navbar.php"); ?>

    <section class="about-hero">
        <div class="container">
            <h1 class="fade-in">Our Path & Purpose</h1>
            <p class="fade-in">Preserving the Dhamma through education, practice, and community.</p>
        </div>
    </section>
    <section class="history-section fade-in">
        <div class="container">
            <div class="history-main-grid">

                <div class="history-visual">
                    <div class="experience-badge">
                        <span class="years">Since</span>
                        <span class="date">1990</span>
                    </div>
                    <div class="main-history-img">
                        <img src="../public/assets/images/2.jpg" alt="Monastery History">
                    </div>
                    <div class="accent-shape"></div>
                </div>

                <div class="history-text">
                    <div class="heading-wrapper">
                        <span class="section-subtitle">Chronicles of ThuNgeDaw</span>
                        <h2>A Legacy of Faith & Wisdom</h2>
                    </div>

                    <p class="lead-text">
                        ThuNgeDaw Tawya began as a humble aspiration to preserve the sacred Dhamma in its purest form.
                    </p>

                    <div class="history-body">
                        <p>What started as a secluded forest retreat has evolved into a cornerstone of Buddhist
                            education. Rooted in the <strong>Tawya (Forest) tradition</strong>, our monastery provides
                            the perfect balance between solitary meditation and rigorous scriptural mastery.</p>

                        <p>Today, we serve as a beacon for students across the nation, blending the ancient silence of
                            the woods with the active pursuit of the <strong>Tharthana Lingara</strong> excellence.</p>
                    </div>

                    <div class="tradition-tags">
                        <span><i class="fas fa-leaf"></i> Forest Tradition</span>
                        <span><i class="fas fa-scroll"></i> Scriptural Purity</span>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <section class="vision-mission-section">
        <div class="container">
            <div class="mission-grid">
                <div class="mission-card fade-in">
                    <div class="card-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To be a leading center for Tipitaka excellence, fostering a global community of enlightened
                        scholars and practitioners.</p>
                </div>
                <div class="mission-card fade-in">
                    <div class="card-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>To provide high-quality scriptural education, facilitate intensive meditation, and support the
                        spiritual growth of the Sangha.</p>
                </div>
                <div class="mission-card fade-in">
                    <div class="card-icon"><i class="fas fa-hands-holding"></i></div>
                    <h3>Our Values</h3>
                    <p>Compassion (Karuna), Wisdom (Panna), and Integrity (Sila) are the pillars of every action we
                        take.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Students Annually</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">20+</span>
                    <span class="stat-label">Dhamma Teachers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">Level 3</span>
                    <span class="stat-label">Accredited Courses</span>
                </div>
            </div>
        </div>
        <?php include("../layouts/footer.php") ?>
    </section>



    <script>
    // Simple Intersection Observer for animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    });
    document.querySelectorAll('.fade-in').forEach((el) => observer.observe(el));
    </script>
</body>

</html>