<link rel="stylesheet" href="../layouts/assets/css/navbar.css">

<nav class="navbar-content">
    <div class="nav-brand">
        <img src="../public/assets/images/3.jpg" alt="Monastery Logo" class="logo-img">
        <div class="brand-text">
            <span class="main-title">ThuNgeDaw Tawya</span>
            <span class="sub-title">BUDDHIST EDUCATION CENTER</span>
        </div>
    </div>

    <div class="nav-menu" id="navLinks">
        <a href="../public/index.php" class="nav-item"><i class="fas fa-home"></i> Home</a>
        <a href="../layouts/dhama_content.php" class="nav-item"> <i class="fa-solid fa-play-circle"></i> Dhama Contents</a>
        <a href="../layouts/check_roll_number.php" class="nav-item"><i class="fas fa-search"></i> Roll Inquiry</a>
        <a href="../layouts/result.php" class="nav-item"><i class="fas fa-poll-h"></i> Results</a>
        <a href="../layouts/announcements.php" class="nav-item"><i class="fas fa-newspaper"></i> News</a>
        <a href="../public/about.php" class="nav-item"><i class="fas fa-monument"></i>About Us</a>
        <a href="../layouts/contact.php" class="nav-item"><i class="fas fa-phone-alt"></i>Contacts Us</a>

        <div class="mobile-admin-wrapper">
            <a href="../admin/login.php" class="login-btn">Admin Portal</a>
        </div>
    </div>

    <button class="mobile-toggle" id="navToggle">
        <i class="fas fa-bars"></i>
    </button>
</nav>

<script>
const navToggle = document.getElementById('navToggle');
const closeMenu = document.getElementById('closeMenu');
const navLinks = document.getElementById('navLinks');

navToggle.addEventListener('click', () => navLinks.classList.add('active'));
closeMenu.addEventListener('click', () => navLinks.classList.remove('active'));
</script>