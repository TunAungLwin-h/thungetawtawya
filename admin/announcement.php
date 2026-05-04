<?php

require_once __DIR__ . '/../configuration/db.php';

require_once __DIR__ . '/auth/admin_guard.php';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
}
$csrf = $_SESSION['csrf_token'];

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $t_en = trim($_POST['title_en'] ?? '');
    $t_mm = trim($_POST['title_mm'] ?? '');
    $c_en = trim($_POST['content_en'] ?? '');
    $c_mm = trim($_POST['content_mm'] ?? '');
    $cat  = $_POST['category'] ?? 'general';
    $status = $_POST['status'] ?? 'draft';
    $importance = $_POST['importance_level'] ?? 'normal';
    $publish_date = $_POST['publish_date'] ?: date('Y-m-d');

    if ($t_en === '' || $c_en === '') {
        $error = 'English Title and Content are required.';
    } else {
        if (!empty($_POST['id'])) {
            $stmt = $pdo->prepare("UPDATE announcements SET 
                title_en=?, title_mm=?, content_en=?, content_mm=?, 
                status=?, importance_level=?, category=?, publish_date=? 
                WHERE id=?");
            $stmt->execute([$t_en, $t_mm, $c_en, $c_mm, $status, $importance, $cat, $publish_date, $_POST['id']]);
            $success = 'Updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO announcements 
                (title_en, title_mm, content_en, content_mm, author_id, author_name, status, importance_level, category, publish_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$t_en, $t_mm, $c_en, $c_mm, $_SESSION['admin_id'], $_SESSION['admin_name'], $status, $importance, $cat, $publish_date]);
            $success = 'Added successfully!';
        }
    }
    if (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM announcements WHERE id=?");
        $stmt->execute([(int)$_POST['delete_id']]);
        $success = 'Deleted successfully!';
    }
}

$announcements = $pdo->query("SELECT * FROM announcements ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Bilingual Announcements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f4f1ee;
        font-family: 'Inter', sans-serif;
    }

    .admin-card {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .lang-label {
        font-size: 0.8rem;
        font-weight: bold;
        color: #7c5e3b;
        text-transform: uppercase;
    }

    .cat-badge {
        font-size: 0.7rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h2>📢 Announcement Manager</h2>
            <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
        </div>

        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="admin-card p-4 mb-5">
            <?php
            $editData = null;
            if(!empty($_GET['edit'])) {
                foreach($announcements as $a) if($a['id'] == $_GET['edit']) $editData = $a;
            }
            ?>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                <?php if($editData) echo '<input type="hidden" name="id" value="'.$editData['id'].'">' ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="lang-label">Title (English)</label>
                        <input type="text" name="title_en" class="form-control" required
                            value="<?= $editData['title_en'] ?? '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="lang-label">ခေါင်းစဉ် (မြန်မာ)</label>
                        <input type="text" name="title_mm" class="form-control"
                            value="<?= $editData['title_mm'] ?? '' ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="lang-label">Content (English)</label>
                        <textarea name="content_en" class="form-control" rows="4"
                            required><?= $editData['content_en'] ?? '' ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="lang-label">အကြောင်းအရာ (မြန်မာ)</label>
                        <textarea name="content_mm" class="form-control"
                            rows="4"><?= $editData['content_mm'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Category</label>
                        <select name="category" class="form-select">
                            <option value="general" <?= ($editData['category']??'')=='general'?'selected':'' ?>>General
                            </option>
                            <option value="exam_results"
                                <?= ($editData['category']??'')=='exam_results'?'selected':'' ?>>Exam Results</option>
                            <option value="exam_dates" <?= ($editData['category']??'')=='exam_dates'?'selected':'' ?>>
                                Exam Dates</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Publish Date</label>
                        <input type="date" name="publish_date" class="form-control"
                            value="<?= $editData['publish_date'] ?? date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit"
                            class="btn btn-primary w-100"><?= $editData ? 'Update' : 'Post Now' ?></button>
                    </div>
                </div>
            </form>
        </div>

        <table class="table admin-card overflow-hidden">
            <thead class="table-light">
                <tr>
                    <th>Category</th>
                    <th>Title (EN/MM)</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($announcements as $a): ?>
                <tr>
                    <td><span class="badge bg-secondary cat-badge"><?= strtoupper($a['category']) ?></span></td>
                    <td>
                        <strong><?= htmlspecialchars($a['title_en']) ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($a['title_mm']) ?></small>
                    </td>
                    <td><?= $a['status'] ?></td>
                    <td><?= $a['publish_date'] ?></td>
                    <td>
                        <a href="?edit=<?= $a['id'] ?>" class="btn btn-sm btn-info text-white">Edit</a>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                            <button type="submit" name="delete_id" value="<?= (int)$a['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Delete?')">Del</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
