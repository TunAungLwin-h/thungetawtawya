<style>
.logo {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
}

.text {
    font-size: 18px;
    font-weight: 600;
    color: var(--accent);
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    background: var(--panel);
    border-bottom: 1px solid var(--border);
}

.topbar .brand {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.topbar .top-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    position: relative;
}

.topbar .btn.ghost {
    background: none;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 14px;
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--panel);
    border: 1px solid var(--border);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    display: none;
    min-width: 250px;
    z-index: 100;
    color: var(--text);

}

.dropdown-menu ol,
.dropdown-menu ul {
    margin: 0;
    padding-left: 1.2rem;
    list-style: inside;
    color: var(--text);

}

.dropdown:hover .dropdown-menu {
    display: block;
}

/* Action Flow Dropdown Upgrade */
.action-flow-menu {
    min-width: 320px;
    padding: 15px;
    font-size: 14px;
}

.action-flow-menu h4 {
    margin-bottom: 10px;
    color: #5D4C3C;
    display: flex;
    align-items: center;
    gap: 8px;
}

.flow-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.flow {
    display: flex;
    gap: 12px;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 6px;
    align-items: center;
}

.flow:hover {
    background: #f5f1e8;
}

.flow .step {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    font-weight: bold;
    color: #fff;
    text-align: center;
    line-height: 26px;
    font-size: 13px;
}

.flow.pending .step {
    background: #ff9800;
}

.flow.approved .step {
    background: #2196f3;
}

.flow.roll .step {
    background: #9c27b0;
}

.flow.passed .step {
    background: #4caf50;
}

.flow.failed .step {
    background: #f44336;
}

.flow.Reject .step {
    background: #607d8b;
}

.flow strong {
    display: block;
    font-size: 14px;
}

.flow small {
    color: #666;
    font-size: 12px;
}

.flow-note {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px dashed #ddd;
    font-size: 12px;
    color: #555;
    display: flex;
    align-items: center;
    gap: 6px;
}


.admin-name {
    font-weight: 500;
    font-size: 20px;
    color: lightgreen;
    margin-right: 8px;


}
</style>
<header class="topbar">
    <div class="brand">
        <div class="logo">
            <img src="../admin/assets/images/3.jpg" alt="Logo" style="width:36px; height:36px; border-radius:50%;">
        </div>
        <div class="text">THUNGEDAWTAWYA-ADMIN DASHBOARD</div>
    </div>

    <div class="top-actions">


        <div class="admin-name"><?php
                if(isset($_SESSION['admin_name'])) {
                    echo htmlspecialchars($_SESSION['admin_name']);
                }else{
                    echo "System Owner";
                }
            
            ?></div>

        <!-- Settings Dropdown -->
        <div class="dropdown">
            <button class="btn ghost" title="Settings">
                <i class="fa-solid fa-gear"></i>
                Settings
                <i class="fa-solid fa-caret-down"></i>
            </button>
            <div class="dropdown-menu settings-menu">
                <ul>
                    <li><a href="layout/profile.php">Profile</a></li>
                    <li><a href="layout/change_password.php">Change Password</a></li>
                    <li><a href="layout/preference.php">Preferences</a></li>
                </ul>
            </div>
        </div>


        <!-- Action Flow Dropdown -->
        <div class="dropdown action-flow">
            <button class="btn ghost" title="Action Flow">
                <i class="fa-solid fa-diagram-project"></i>
                Action Flow
                <i class="fa-solid fa-caret-down"></i>
            </button>

            <div class="dropdown-menu action-flow-menu">
                <h4><i class="fa-solid fa-route"></i> Registration Workflow</h4>

                <ul class="flow-list">
                    <li class="flow pending">
                        <span class="step">1</span>
                        <div>
                            <strong>Pending</strong>
                            <small>View · Approve · Reject · Delete</small>
                        </div>
                    </li>

                    <li class="flow approved">
                        <span class="step">2</span>
                        <div>
                            <strong>Approved</strong>
                            <small>View · Generate Roll · Reject · Delete</small>
                        </div>
                    </li>

                    <li class="flow roll">
                        <span class="step">3</span>
                        <div>
                            <strong>Roll Generated</strong>
                            <small>View · Mark Passed · Delete</small>
                        </div>
                    </li>

                    <li class="flow passed">
                        <span class="step">4</span>
                        <div>
                            <strong>Passed</strong>
                            <small>Next Level (if &lt; 3) · View · Delete</small>
                        </div>
                    </li>

                    <li class="flow failed">
                        <span class="step">5</span>
                        <div>
                            <strong>Failed</strong>
                            <small>View · Delete</small>
                        </div>
                    </li>
                    <li class="flow Reject">
                        <span class="step">X</span>
                        <div>
                            <strong>Rejected</strong>
                            <small>View · Delete</small>
                        </div>
                    </li>
                </ul>

                <div class="flow-note">
                    <i class="fa-solid fa-circle-info"></i>
                    Progression is automatic & level-based
                </div>
            </div>
        </div>

        <!-- Logout -->
        <a href="logout.php" class="btn ghost">Logout</a>
    </div>
</header>


<script>
(function() {
    const mobileBtn = document.getElementById('mobileSidebarOpen');
    const sidebar = document.getElementById('adminSidebar');

    mobileBtn.addEventListener('click', () => {
        sidebar.classList.toggle('show');
    });
})();
</script>