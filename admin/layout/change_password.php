<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Buddha Monastery</title>
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
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .container {
        max-width: 900px;
        width: 100%;
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
        width: 300px;
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

    .password-strength {
        margin-top: 20px;
    }

    .strength-meter {
        height: 8px;
        background-color: #eee;
        border-radius: 4px;
        margin: 10px 0;
        overflow: hidden;
    }

    .strength-level {
        height: 100%;
        width: 0%;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .strength-label {
        font-size: 14px;
        display: flex;
        justify-content: space-between;
    }

    .password-rules {
        margin-top: 20px;
    }

    .password-rules ul {
        list-style: none;
        padding-left: 0;
    }

    .password-rules li {
        padding: 5px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }

    .password-rules li i {
        color: #ccc;
    }

    .password-rules li.valid i {
        color: #4CAF50;
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

    .card-header p {
        color: var(--text-light);
        margin-top: 5px;
        font-size: 15px;
    }

    /* Password Form */
    .password-form {
        max-width: 500px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--dark-color);
        font-weight: 500;
    }

    .input-with-icon {
        position: relative;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px 12px 45px;
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

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-light);
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-light);
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }

    .btn {
        padding: 12px 25px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--accent-color);
    }

    .btn-secondary {
        background-color: #f0f0f0;
        color: var(--text-color);
    }

    .btn-secondary:hover {
        background-color: #e0e0e0;
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

        .form-actions {
            flex-direction: column;
        }

        .btn {
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
                <a href="preference.php"><i class="fas fa-cog"></i> Preferences</a>
                <a href="#" class="active"><i class="fas fa-key"></i> Password</a>
                <a href="../dashboard.php"><i class="fas fa-sign-out-alt"></i> Dashboard</a>
            </nav>
        </header>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sidebar -->
            <aside class="sidebar">
                <div class="sidebar-card">
                    <h3><i class="fas fa-lock"></i> Password Security</h3>
                    <p>For your security, please follow these guidelines when creating a new password:</p>

                    <div class="password-rules">
                        <ul>
                            <li id="rule-length">
                                <i class="fas fa-circle"></i>
                                <span>At least 12 characters</span>
                            </li>
                            <li id="rule-uppercase">
                                <i class="fas fa-circle"></i>
                                <span>At least one uppercase letter</span>
                            </li>
                            <li id="rule-lowercase">
                                <i class="fas fa-circle"></i>
                                <span>At least one lowercase letter</span>
                            </li>
                            <li id="rule-number">
                                <i class="fas fa-circle"></i>
                                <span>At least one number</span>
                            </li>
                            <li id="rule-special">
                                <i class="fas fa-circle"></i>
                                <span>At least one special character</span>
                            </li>
                        </ul>
                    </div>

                    <div class="password-strength">
                        <div class="strength-label">
                            <span>Password Strength:</span>
                            <span id="strength-text">None</span>
                        </div>
                        <div class="strength-meter">
                            <div class="strength-level" id="strength-level"></div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-card">
                    <h3><i class="fas fa-shield-alt"></i> Security Tips</h3>
                    <p>1. Never share your password with anyone.</p>
                    <p>2. Avoid using personal information in passwords.</p>
                    <p>3. Change your password regularly.</p>
                    <p>4. Use different passwords for different accounts.</p>
                    <p>5. Consider using a password manager.</p>
                </div>
            </aside>

            <!-- Main Panel -->
            <main class="main-panel">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-key"></i> Change Password</h3>
                        <p>Update your password to maintain account security. You will be logged out of all other
                            sessions after changing your password.</p>
                    </div>

                    <form class="password-form" id="passwordForm">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="currentPassword" class="form-control" required>
                                <button type="button" class="toggle-password" data-target="currentPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password" id="newPassword" class="form-control" required>
                                <button type="button" class="toggle-password" data-target="newPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirm New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password" id="confirmPassword" class="form-control" required>
                                <button type="button" class="toggle-password" data-target="confirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="password-match" style="margin-top: 5px; font-size: 14px; display: none;">
                                <i class="fas fa-check" style="color: #4CAF50;"></i>
                                <span>Passwords match</span>
                            </div>
                            <div id="password-mismatch" style="margin-top: 5px; font-size: 14px; display: none;">
                                <i class="fas fa-times" style="color: #F44336;"></i>
                                <span>Passwords do not match</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelBtn">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-user-shield"></i> Security Status</h3>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="margin-bottom: 10px; color: var(--dark-color);">Password History</h4>
                            <p>Last changed: <strong>April 15, 2023</strong></p>
                            <p>Expires in: <strong>45 days</strong></p>
                            <p>Strength: <strong>Strong</strong></p>
                        </div>

                        <div style="flex: 1; min-width: 200px;">
                            <h4 style="margin-bottom: 10px; color: var(--dark-color);">Active Sessions</h4>
                            <p>Current device: <strong style="color: #4CAF50;">This browser</strong></p>
                            <p>Other sessions: <strong>3 devices</strong></p>
                            <p>Last unusual activity: <strong>None</strong></p>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        <button class="btn btn-secondary" style="padding: 8px 15px; font-size: 14px;">
                            <i class="fas fa-desktop"></i> Manage Active Sessions
                        </button>
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
    // Password visibility toggle
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Password validation
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordMatch = document.getElementById('password-match');
    const passwordMismatch = document.getElementById('password-mismatch');
    const strengthLevel = document.getElementById('strength-level');
    const strengthText = document.getElementById('strength-text');

    // Password validation rules
    const rules = {
        length: document.getElementById('rule-length'),
        uppercase: document.getElementById('rule-uppercase'),
        lowercase: document.getElementById('rule-lowercase'),
        number: document.getElementById('rule-number'),
        special: document.getElementById('rule-special')
    };

    function validatePassword(password) {
        // Reset all rules
        Object.values(rules).forEach(rule => {
            rule.classList.remove('valid');
            rule.querySelector('i').className = 'fas fa-circle';
        });

        // Check length
        if (password.length >= 12) {
            rules.length.classList.add('valid');
            rules.length.querySelector('i').className = 'fas fa-check-circle';
        }

        // Check uppercase
        if (/[A-Z]/.test(password)) {
            rules.uppercase.classList.add('valid');
            rules.uppercase.querySelector('i').className = 'fas fa-check-circle';
        }

        // Check lowercase
        if (/[a-z]/.test(password)) {
            rules.lowercase.classList.add('valid');
            rules.lowercase.querySelector('i').className = 'fas fa-check-circle';
        }

        // Check number
        if (/[0-9]/.test(password)) {
            rules.number.classList.add('valid');
            rules.number.querySelector('i').className = 'fas fa-check-circle';
        }

        // Check special character
        if (/[^A-Za-z0-9]/.test(password)) {
            rules.special.classList.add('valid');
            rules.special.querySelector('i').className = 'fas fa-check-circle';
        }

        // Calculate strength
        let strength = 0;
        if (password.length >= 12) strength += 20;
        if (/[A-Z]/.test(password)) strength += 20;
        if (/[a-z]/.test(password)) strength += 20;
        if (/[0-9]/.test(password)) strength += 20;
        if (/[^A-Za-z0-9]/.test(password)) strength += 20;

        // Update strength meter
        strengthLevel.style.width = strength + '%';

        // Update strength text and color
        if (strength <= 20) {
            strengthLevel.style.backgroundColor = '#F44336';
            strengthText.textContent = 'Weak';
            strengthText.style.color = '#F44336';
        } else if (strength <= 60) {
            strengthLevel.style.backgroundColor = '#FF9800';
            strengthText.textContent = 'Fair';
            strengthText.style.color = '#FF9800';
        } else if (strength <= 80) {
            strengthLevel.style.backgroundColor = '#2196F3';
            strengthText.textContent = 'Good';
            strengthText.style.color = '#2196F3';
        } else {
            strengthLevel.style.backgroundColor = '#4CAF50';
            strengthText.textContent = 'Strong';
            strengthText.style.color = '#4CAF50';
        }
    }

    // Check password match
    function checkPasswordMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (newPassword && confirmPassword) {
            if (newPassword === confirmPassword) {
                passwordMatch.style.display = 'block';
                passwordMismatch.style.display = 'none';
            } else {
                passwordMatch.style.display = 'none';
                passwordMismatch.style.display = 'block';
            }
        } else {
            passwordMatch.style.display = 'none';
            passwordMismatch.style.display = 'none';
        }
    }

    // Event listeners
    newPasswordInput.addEventListener('input', function() {
        validatePassword(this.value);
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Form submission
    const form = document.getElementById('passwordForm');
    const cancelBtn = document.getElementById('cancelBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Basic validation
        if (!currentPassword) {
            alert('Please enter your current password');
            return;
        }

        if (newPassword !== confirmPassword) {
            alert('New passwords do not match');
            return;
        }

        if (newPassword.length < 12) {
            alert('New password must be at least 12 characters long');
            return;
        }

        // Check password strength
        const validRules = document.querySelectorAll('.password-rules li.valid');
        if (validRules.length < 5) {
            alert('Please ensure your password meets all the security requirements');
            return;
        }

        // Simulate password change
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
        submitBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            alert('Password updated successfully! You will be redirected to the login page.');

            // Reset form
            form.reset();
            strengthLevel.style.width = '0%';
            strengthText.textContent = 'None';
            strengthText.style.color = '';
            passwordMatch.style.display = 'none';
            passwordMismatch.style.display = 'none';

            // Reset validation rules
            Object.values(rules).forEach(rule => {
                rule.classList.remove('valid');
                rule.querySelector('i').className = 'fas fa-circle';
            });

            // Reset button
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Password';
            submitBtn.disabled = false;
        }, 1500);
    });

    // Cancel button
    cancelBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
            form.reset();
            strengthLevel.style.width = '0%';
            strengthText.textContent = 'None';
            strengthText.style.color = '';
            passwordMatch.style.display = 'none';
            passwordMismatch.style.display = 'none';

            // Reset validation rules
            Object.values(rules).forEach(rule => {
                rule.classList.remove('valid');
                rule.querySelector('i').className = 'fas fa-circle';
            });
        }
    });
    </script>
</body>

</html>