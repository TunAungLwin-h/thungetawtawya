<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile | Buddha Monastery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
        --primary-color: #8B7355;
        --secondary-color: #D4B996;
        --accent-color: #7A6A50;
        --light-color: #F5F1E8;
        --dark-color: #5D4C3C;
        --text-color: #333;
        --text-light: #666;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --border-radius: 8px;
    }

    body {
        background-color: #f9f7f2;
        color: var(--text-color);
        min-height: 100vh;
        background-image: url('data:image/svg+xml;utf8,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path d="M20,50 Q50,20 80,50 T140,50" stroke="%238B7355" fill="none" stroke-width="0.5" opacity="0.1"/></svg>');
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    /* Header Styles */
    .header {
        background-color: var(--primary-color);
        color: white;
        padding: 15px 20px;
        border-radius: var(--border-radius);
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: var(--shadow);
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .logo i {
        font-size: 28px;
        color: var(--secondary-color);
    }

    .logo h1 {
        font-size: 24px;
        font-weight: 400;
        letter-spacing: 1px;
    }

    .admin-nav {
        display: flex;
        gap: 20px;
    }

    .admin-nav a {
        color: white;
        text-decoration: none;
        padding: 8px 15px;
        border-radius: 4px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .admin-nav a:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .admin-nav a.active {
        background-color: var(--secondary-color);
        color: var(--dark-color);
    }

    /* Main Content */
    .main-content {
        display: flex;
        gap: 30px;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        flex-shrink: 0;
    }

    .sidebar-card {
        background-color: white;
        border-radius: var(--border-radius);
        padding: 25px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    .admin-info {
        text-align: center;
    }

    .admin-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 0 auto 15px;
        background-color: var(--light-color);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 5px solid var(--secondary-color);
        overflow: hidden;
    }

    .admin-avatar i {
        font-size: 60px;
        color: var(--primary-color);
    }

    .admin-info h2 {
        font-size: 22px;
        margin-bottom: 5px;
        color: var(--dark-color);
    }

    .admin-info p {
        color: var(--text-light);
        margin-bottom: 15px;
        font-size: 14px;
    }

    .admin-stats {
        display: flex;
        justify-content: space-around;
        margin-top: 20px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 600;
        color: var(--primary-color);
    }

    .stat-label {
        font-size: 12px;
        color: var(--text-light);
    }

    /* Main Panel */
    .main-panel {
        flex: 1;
    }

    .card {
        background-color: white;
        border-radius: var(--border-radius);
        padding: 30px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .card-header h3 {
        font-size: 20px;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-header h3 i {
        color: var(--primary-color);
    }

    .edit-btn {
        background-color: var(--secondary-color);
        color: var(--dark-color);
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .edit-btn:hover {
        background-color: var(--primary-color);
        color: white;
    }

    /* Profile Form */
    .profile-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark-color);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
    }

    .full-width {
        grid-column: 1 / -1;
    }

    /* Footer */
    .footer {
        text-align: center;
        margin-top: 40px;
        padding: 20px;
        color: var(--text-light);
        font-size: 14px;
        border-top: 1px solid #eee;
    }

    .footer i {
        color: var(--primary-color);
        margin: 0 5px;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .main-content {
            flex-direction: column;
        }

        .sidebar {
            width: 100%;
        }

        .profile-form {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .header {
            flex-direction: column;
            gap: 15px;
        }

        .admin-nav {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <i class="fas fa-spa"></i>
                <h1>Buddha Monastery Admin</h1>
            </div>
            <nav class="admin-nav">
                <a href="#" class="active"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="preference.php"><i class="fas fa-cog"></i> Preferences</a>
                <a href="change_password.php"><i class="fas fa-key"></i> Password</a>
                <a href="../dashboard.php"><i class="fas fa-sign-out-alt"></i> Dashboard</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-card admin-info">
                    <div class="admin-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2>Admin User</h2>
                    <p>Head Administrator</p>
                    <p><i class="fas fa-map-marker-alt"></i> Bodh Gaya Monastery</p>

                    <div class="admin-stats">
                        <div class="stat-item">
                            <div class="stat-value">42</div>
                            <div class="stat-label">Monks</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">12</div>
                            <div class="stat-label">Events</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">156</div>
                            <div class="stat-label">Meditations</div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3><i class="fas fa-info-circle"></i> Account Status</h3>
                    <p>Active since: Jan 15, 2020</p>
                    <p>Last login: Today, 09:42 AM</p>
                    <p>Role: Super Administrator</p>
                </div>
            </aside>

            <!-- Main Panel -->
            <main class="main-panel">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                        <button class="edit-btn">Edit Profile</button>
                    </div>

                    <form class="profile-form">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" id="fullName" class="form-control" value="Admin User" readonly>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" value="admin@monastery.com" readonly>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" class="form-control" value="+1 (555) 123-4567" readonly>
                        </div>

                        <div class="form-group">
                            <label for="role">Administrative Role</label>
                            <input type="text" id="role" class="form-control" value="Head Administrator" readonly>
                        </div>

                        <div class="form-group full-width">
                            <label for="address">Monastery Address</label>
                            <input type="text" id="address" class="form-control" value="Bodh Gaya, Bihar 824231, India"
                                readonly>
                        </div>

                        <div class="form-group full-width">
                            <label for="bio">Biography</label>
                            <textarea id="bio" class="form-control" rows="4"
                                readonly>Head administrator of the Buddha Monastery with over 10 years of experience in spiritual leadership and community management. Dedicated to preserving Buddhist teachings and guiding the monastic community towards enlightenment.</textarea>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> Recent Activity</h3>
                    </div>

                    <div class="activity-list">
                        <div class="activity-item"
                            style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 15px;">
                            <div style="background-color: #E8F5E9; padding: 8px; border-radius: 50%;">
                                <i class="fas fa-user-check" style="color: #4CAF50;"></i>
                            </div>
                            <div>
                                <p style="font-weight: 500;">Approved new monk application</p>
                                <p style="color: var(--text-light); font-size: 14px;">2 hours ago</p>
                            </div>
                        </div>

                        <div class="activity-item"
                            style="padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 15px;">
                            <div style="background-color: #E3F2FD; padding: 8px; border-radius: 50%;">
                                <i class="fas fa-calendar-alt" style="color: #2196F3;"></i>
                            </div>
                            <div>
                                <p style="font-weight: 500;">Scheduled meditation session</p>
                                <p style="color: var(--text-light); font-size: 14px;">Yesterday, 3:15 PM</p>
                            </div>
                        </div>

                        <div class="activity-item"
                            style="padding: 10px 0; display: flex; align-items: center; gap: 15px;">
                            <div style="background-color: #FFF3E0; padding: 8px; border-radius: 50%;">
                                <i class="fas fa-book" style="color: #FF9800;"></i>
                            </div>
                            <div>
                                <p style="font-weight: 500;">Updated sacred texts database</p>
                                <p style="color: var(--text-light); font-size: 14px;">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>Buddha Monastery Admin Panel <i class="fas fa-om"></i> 2023 | Cultivating Mindfulness & Compassion</p>
        </footer>
    </div>

    <script>
    // Toggle edit mode for profile
    const editBtn = document.querySelector('.edit-btn');
    const formControls = document.querySelectorAll('.form-control');

    editBtn.addEventListener('click', function() {
        const isReadOnly = formControls[0].hasAttribute('readonly');

        if (isReadOnly) {
            // Switch to edit mode
            formControls.forEach(control => {
                control.removeAttribute('readonly');
                control.style.backgroundColor = '#fff';
            });
            editBtn.textContent = 'Save Changes';
            editBtn.style.backgroundColor = '#4CAF50';
            editBtn.style.color = 'white';
        } else {
            // Simulate saving changes and switch back to read-only
            formControls.forEach(control => {
                control.setAttribute('readonly', 'true');
                control.style.backgroundColor = '#f9f9f9';
            });
            editBtn.textContent = 'Edit Profile';
            editBtn.style.backgroundColor = 'var(--secondary-color)';
            editBtn.style.color = 'var(--dark-color)';


            alert('Profile updated successfully!');
        }
    });
    </script>
</body>

</html>