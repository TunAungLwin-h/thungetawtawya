<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Preferences | Buddha Monastery</title>
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

    .sidebar-card h3 {
        font-size: 18px;
        color: var(--dark-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-card h3 i {
        color: var(--primary-color);
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

    .save-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .save-btn:hover {
        background-color: var(--accent-color);
    }

    /* Preferences Form */
    .preferences-form {
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

    select.form-control {
        cursor: pointer;
    }

    .checkbox-group,
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .checkbox-item,
    .radio-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkbox-item input,
    .radio-item input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .checkbox-item label,
    .radio-item label {
        margin-bottom: 0;
        cursor: pointer;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    /* Theme Selector */
    .theme-selector {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .theme-option {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .theme-option:hover {
        transform: scale(1.1);
    }

    .theme-option.active {
        border-color: var(--primary-color);
    }

    .theme-option.default {
        background: linear-gradient(135deg, #8B7355 50%, #D4B996 50%);
    }

    .theme-option.serenity {
        background: linear-gradient(135deg, #6A8D73 50%, #C4D6B0 50%);
    }

    .theme-option.sand {
        background: linear-gradient(135deg, #C2B280 50%, #E8E1C9 50%);
    }

    .theme-option.lotus {
        background: linear-gradient(135deg, #9A7B9E 50%, #D8C6D8 50%);
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

        .preferences-form {
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
                <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
                <a href="#" class="active"><i class="fas fa-cog"></i> Preferences</a>
                <a href="change_password.php"><i class="fas fa-key"></i> Password</a>
                <a href="../dashboard.php"><i class="fas fa-sign-out-alt"></i> Dashboard</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-card">
                    <h3><i class="fas fa-info-circle"></i> Preference Guide</h3>
                    <p>Customize your admin experience. These settings affect only your account.</p>
                    <p style="margin-top: 10px; font-size: 14px; color: var(--text-light);"><i
                            class="fas fa-lightbulb"></i> Changes are saved automatically when you click "Save
                        Preferences".</p>
                </div>

                <div class="sidebar-card">
                    <h3><i class="fas fa-bell"></i> Notification Status</h3>
                    <p>Email: <span style="color: #4CAF50; font-weight: 500;">Enabled</span></p>
                    <p>Push: <span style="color: #4CAF50; font-weight: 500;">Enabled</span></p>
                    <p>SMS: <span style="color: #F44336; font-weight: 500;">Disabled</span></p>
                </div>

                <div class="sidebar-card">
                    <h3><i class="fas fa-palette"></i> Current Theme</h3>
                    <p>Default Monastery</p>
                    <div class="theme-selector">
                        <div class="theme-option default active"></div>
                        <div class="theme-option serenity"></div>
                        <div class="theme-option sand"></div>
                        <div class="theme-option lotus"></div>
                    </div>
                </div>
            </aside>

            <!-- Main Panel -->
            <main class="main-panel">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-cog"></i> Account Preferences</h3>
                        <button class="save-btn" id="savePreferences">
                            <i class="fas fa-save"></i> Save Preferences
                        </button>
                    </div>

                    <form class="preferences-form" id="preferencesForm">
                        <div class="form-group">
                            <label for="language">Language</label>
                            <select id="language" class="form-control">
                                <option value="en" selected>English</option>
                                <option value="hi">Hindi</option>
                                <option value="sa">Sanskrit</option>
                                <option value="zh">Chinese</option>
                                <option value="ja">Japanese</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="timezone">Timezone</label>
                            <select id="timezone" class="form-control">
                                <option value="ist" selected>IST (India Standard Time)</option>
                                <option value="pst">PST (Pacific Standard Time)</option>
                                <option value="est">EST (Eastern Standard Time)</option>
                                <option value="gmt">GMT (Greenwich Mean Time)</option>
                                <option value="cst">CST (China Standard Time)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="dateFormat">Date Format</label>
                            <select id="dateFormat" class="form-control">
                                <option value="dd-mm-yyyy" selected>DD-MM-YYYY</option>
                                <option value="mm-dd-yyyy">MM-DD-YYYY</option>
                                <option value="yyyy-mm-dd">YYYY-MM-DD</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="itemsPerPage">Items Per Page</label>
                            <select id="itemsPerPage" class="form-control">
                                <option value="10">10 items</option>
                                <option value="25" selected>25 items</option>
                                <option value="50">50 items</option>
                                <option value="100">100 items</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Dashboard Layout</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="layoutDefault" name="layout" value="default" checked>
                                    <label for="layoutDefault">Default (2 columns)</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="layoutCompact" name="layout" value="compact">
                                    <label for="layoutCompact">Compact (3 columns)</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="layoutSpacious" name="layout" value="spacious">
                                    <label for="layoutSpacious">Spacious (1 column)</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label>Email Notifications</label>
                            <div class="checkbox-group">
                                <div class="checkbox-item">
                                    <input type="checkbox" id="notifyNewUsers" name="notifications" checked>
                                    <label for="notifyNewUsers">New user registrations</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="notifyEvents" name="notifications" checked>
                                    <label for="notifyEvents">Upcoming events</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="notifyDonations" name="notifications">
                                    <label for="notifyDonations">Donation receipts</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="notifySystem" name="notifications" checked>
                                    <label for="notifySystem">System alerts</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label for="autoLogout">Auto-logout After Inactivity</label>
                            <select id="autoLogout" class="form-control">
                                <option value="15">15 minutes</option>
                                <option value="30" selected>30 minutes</option>
                                <option value="60">1 hour</option>
                                <option value="120">2 hours</option>
                                <option value="0">Never</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label for="editorMode">Text Editor Mode</label>
                            <select id="editorMode" class="form-control">
                                <option value="simple">Simple</option>
                                <option value="advanced" selected>Advanced</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-bell"></i> Notification Preferences</h3>
                    </div>

                    <div class="preferences-form">
                        <div class="form-group">
                            <label for="notificationSound">Notification Sound</label>
                            <select id="notificationSound" class="form-control">
                                <option value="bell" selected>Meditation Bell</option>
                                <option value="chime">Wind Chime</option>
                                <option value="gong">Tibetan Gong</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="emailFrequency">Email Summary Frequency</label>
                            <select id="emailFrequency" class="form-control">
                                <option value="daily">Daily</option>
                                <option value="weekly" selected>Weekly</option>
                                <option value="biweekly">Bi-weekly</option>
                                <option value="monthly">Monthly</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label>Desktop Notifications</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" id="desktopAll" name="desktop" value="all" checked>
                                    <label for="desktopAll">Allow all notifications</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="desktopImportant" name="desktop" value="important">
                                    <label for="desktopImportant">Important only</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" id="desktopNone" name="desktop" value="none">
                                    <label for="desktopNone">Disable desktop notifications</label>
                                </div>
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
    // Save preferences
    const saveBtn = document.getElementById('savePreferences');
    const form = document.getElementById('preferencesForm');
    const themeOptions = document.querySelectorAll('.theme-option');

    saveBtn.addEventListener('click', function() {
        // Show saving state
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            // Show success message
            saveBtn.innerHTML = '<i class="fas fa-check"></i> Preferences Saved!';
            saveBtn.style.backgroundColor = '#4CAF50';

            // Revert button after 2 seconds
            setTimeout(() => {
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Preferences';
                saveBtn.style.backgroundColor = '';
                saveBtn.disabled = false;
            }, 2000);
        }, 1000);
    });

    // Theme selection
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            themeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');

            // Apply theme (in a real app, this would change CSS variables)
            const theme = this.classList[1]; // default, serenity, sand, lotus
            alert(
                `Theme changed to ${theme}. In a real application, this would update the color scheme.`);
        });
    });

    // Initialize form with saved preferences (simulated)
    window.addEventListener('DOMContentLoaded', function() {
        // In a real app, you would load saved preferences from an API
        console.log('Preferences form loaded');
    });
    </script>
</body>

</html>