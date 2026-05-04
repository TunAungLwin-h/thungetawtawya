<?php


$current = basename($_SERVER['PHP_SELF']);
function isActive($target) {
    global $current;
    return $current === $target ? 'active' : '';
}
?>
<?php
$role = $_SESSION['admin_role'] ?? 'admin';
?>

<aside class="sidebar" id="adminSidebar" aria-label="Admin Navigation">
    <div class="menu">
        <li><a href="dashboard.php" class="<?= isActive('dashboard.php') ?>"><span class="icon"><i
                        class="fa-solid fa-gauge-high"></i></span><span class="label">Dashboard</span></a></li>
        <li><a href="registrations.php" class="<?= isActive('registrations.php') ?>"><span class="icon"><i
                        class="fa-solid fa-file-signature"></i></span><span class="label">Registrations</span></a></li>
        <li><a href="candidates.php" class="<?= isActive('candidates.php') ?>"><span class="icon"><i
                        class="fa-solid fa-users"></i></span><span class="label">Candidates</span></a></li>

        <li><a href="reports.php" class="<?= isActive('reports.php') ?>"><span class="icon"><i
                        class="fa-solid fa-chart-column"></i></span><span class="label">Reports</span></a>
        </li>
        <li> <a href="admin_feedback.php" class="<? isActive('admin_feedback.php') ?>"><span class="icon"></span><i
                    class="fa-regular fa-comment"></i><span class="label">Feedback&Complain</span></a></li>


        <li>

            <a href="roll_publish.php" class="<?= isActive('roll_publish.php') ?>">
                <span class="icon"><i class="fa-solid fa-upload"></i></span>
                <span class="label">Publish Rolls</span>
            </a>
        </li>

        <li><a href="announcement.php" class="<?= isActive('announcement.php') ?>"><span class="icon"><i
                        class="fa-solid fa-bullhorn"></i></span><span class="label">Announcements</span></a></li>

        </ul>
        <?php if ($role === 'superadmin'): ?>
        <li><a href="roll_format_settings.php" class="<?= isActive('roll_format_settings.php') ?>"><span class="icon"><i
                        class="fa-solid fa-cog"></i></span><span class="label">Roll Format</span></a></li>
        <?php endif; ?>
        <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
        <li>
            <a href="registration_publish.php">
                <span class="icon"><i class="fa-solid fa-file-upload"></i></span>
                <span class="label">Publish Registration</span>
            </a>
        </li>
        <?php endif; ?>


        <?php if ($role === 'superadmin'): ?>
        <li><a href="manage_users.php" class="<?= isActive('manage_users.php') ?>"><span class="icon"><i
                        class="fa-solid fa-user-shield"></i></span><span class="label">Manage Users</span></a></li>
        <?php endif; ?>

        <div class="sidebar-footer">
            <div class="sidebar-brand">Thungedawtawya</div>
        </div>

        <button id="sidebarToggle" class="sidebar-toggle" title="Toggle sidebar">
            <i class="fa-solid fa-angle-left"></i>
        </button>

    </div>
</aside>
<script>
(function() {
    const sidebar = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const icon = toggleBtn.querySelector('i');
    const mainContent = document.querySelector('.main-content');

    function applyState(collapsed) {
        sidebar.classList.toggle('collapsed', collapsed);

        if (collapsed) {
            icon.classList.remove('fa-angle-left');
            icon.classList.add('fa-angle-right');
        } else {
            icon.classList.remove('fa-angle-right');
            icon.classList.add('fa-angle-left');
        }

        if (mainContent) {
            mainContent.classList.toggle('sidebar-collapsed', collapsed);
        }
    }

    // Default state (always open on page load)
    applyState(false);

    toggleBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isCollapsed = sidebar.classList.contains('collapsed');
        applyState(!isCollapsed);
    });

})();
</script>