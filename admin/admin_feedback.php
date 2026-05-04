<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';

require_once __DIR__ . '/auth/admin_guard.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$csrf = $_SESSION['csrf_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        http_response_code(400);
        exit('Invalid CSRF token.');
    }

    $id = (int)$_POST['delete'];
    $stmt = $pdo->prepare("DELETE FROM feedback_submissions WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Message deleted successfully.";
    header("Location: admin_feedback.php");
    exit;
}


$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['category']) ? $_GET['category'] : 'All';

$query = "SELECT * FROM feedback_submissions WHERE 1=1";
$params = [];

if ($search != '') {
    $query .= " AND (sender_name LIKE ? OR sender_email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter != 'All') {
    $query .= " AND category = ?";
    $params[] = $filter;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Feedback Manager | Thungetaw Tawya</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    :root {
        --monastery-brown: #a67c52;
        --bg-light: #fdfaf7;
        --text-main: #4a3f35;
        --border-color: #e0d7ce;
        --danger: #e74c3c;
    }

    /* Using your main-content class to ensure it respects the sidebar */
    .main-content {
        margin-left: 260px;
        /* Adjust based on your actual sidebar width */
        padding: 30px;
        background: var(--bg-light);
        min-height: 100vh;
    }

    .admin-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 25px;
        border-top: 6px solid var(--monastery-brown);
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f4f0;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
    }

    .toolbar input,
    .toolbar select {
        padding: 10px 15px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        outline: none;
    }

    .btn-apply {
        background: var(--monastery-brown);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    /* Table Styling */
    .feedback-table {
        width: 100%;
        border-collapse: collapse;
    }

    .feedback-table th {
        text-align: left;
        padding: 15px;
        background: #fcfaf8;
        color: var(--monastery-brown);
        font-size: 13px;
        text-transform: uppercase;
        border-bottom: 2px solid var(--border-color);
    }

    .feedback-table td {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-main);
    }

    /* Badges */
    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
    }

    .Inquiry {
        background: #d1ecf1;
        color: #0c5460;
    }

    .Complaint {
        background: #f8d7da;
        color: #721c24;
    }

    .Donation {
        background: #d4edda;
        color: #155724;
    }

    /* Modal styling */
    .modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(74, 63, 53, 0.7);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: white;
        margin: 10% auto;
        padding: 30px;
        width: 500px;
        border-radius: 15px;
        border-left: 10px solid var(--monastery-brown);
        position: relative;
    }

    .close-modal {
        position: absolute;
        right: 20px;
        top: 20px;
        cursor: pointer;
        font-size: 20px;
        color: #999;
    }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="admin-card">
            <h2 style="color: var(--monastery-brown); margin-bottom: 10px;">
                <i class="fa-solid fa-comments"></i> Feedback & Inquiries
            </h2>
            <p style="color: #8a7a64; margin-bottom: 25px;">Manage and respond to monastery visitors and practitioners.
            </p>

            <?php if (!empty($_SESSION['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
            <?php endif; ?>

            <form method="GET" class="toolbar">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" placeholder="Search name/email..."
                        value="<?= htmlspecialchars($search) ?>">
                    <select name="category">
                        <option value="All">All Categories</option>
                        <option value="Inquiry" <?= $filter == 'Inquiry' ? 'selected' : '' ?>>Inquiry</option>
                        <option value="Complaint" <?= $filter == 'Complaint' ? 'selected' : '' ?>>Complaint</option>
                        <option value="Donation" <?= $filter == 'Donation' ? 'selected' : '' ?>>Donation</option>
                    </select>
                    <button type="submit" class="btn-apply">Filter</button>
                </div>
                <a href="admin_feedback.php"
                    style="color: var(--monastery-brown); text-decoration: none; font-size: 14px;">Reset</a>
            </form>

            <table class="feedback-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sender</th>
                        <th>Category</th>
                        <th>Message Preview</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($results) > 0): ?>
                    <?php foreach($results as $row): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td>
                            <strong><?= htmlspecialchars($row['sender_name']) ?></strong><br>
                            <small style="color: #9c8a79;"><?= htmlspecialchars($row['sender_email']) ?></small>
                        </td>
                        <td><span class="badge <?= $row['category'] ?>"><?= $row['category'] ?></span></td>
                        <td><?= htmlspecialchars(substr($row['message_body'], 0, 45)) ?>...</td>
                        <td>
                            <button class="btn-apply" style="padding: 5px 12px; font-size: 12px;"
                                onclick="openMsg('<?= addslashes($row['sender_name']) ?>', '<?= addslashes($row['message_body']) ?>')">Read</button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                <button type="submit" name="delete" value="<?= (int)$row['id'] ?>"
                                    style="color: var(--danger); margin-left: 10px; font-size: 14px; background:none; border:none; cursor:pointer;"
                                    onclick="return confirm('Permanently delete this feedback?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 50px;">No messages found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="msgModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeMsg()">&times;</span>
            <h3 id="modalName" style="color: var(--monastery-brown); margin-top: 0;"></h3>
            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 15px 0;">
            <p id="modalBody" style="line-height: 1.6; color: var(--text-main); font-size: 15px;"></p>
            <div style="margin-top: 25px; text-align: right;">
                <button class="btn-apply" onclick="closeMsg()">Close</button>
            </div>
        </div>
    </div>

    <script>
    function openMsg(name, body) {
        document.getElementById('modalName').innerText = "Message from: " + name;
        document.getElementById('modalBody').innerText = body;
        document.getElementById('msgModal').style.display = "block";
    }

    function closeMsg() {
        document.getElementById('msgModal').style.display = "none";
    }
    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        let modal = document.getElementById('msgModal');
        if (event.target == modal) {
            closeMsg();
        }
    }
    </script>
</body>

</html>
