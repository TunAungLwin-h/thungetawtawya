<?php
require_once '../configuration/db.php';

$lang = $_GET['lang'] ?? 'mm'; 
$cat_filter = $_GET['category'] ?? 'all';

$query = "SELECT * FROM announcements WHERE status = 'published'";
if($cat_filter !== 'all') { $query .= " AND category = :cat"; }
$query .= " ORDER BY importance_level DESC, publish_date DESC";

$stmt = $pdo->prepare($query);
if($cat_filter !== 'all') $stmt->bindValue(':cat', $cat_filter);
$stmt->execute();
$announcements = $stmt->fetchAll();

$t = [
    'en' => ['title' => 'Monastery Announcements', 'all' => 'All', 'results' => 'Exam Results', 'dates' => 'Exam Dates', 'gen' => 'General'],
    'mm' => ['title' => 'သာသနာရေးဆိုင်ရာ ကြေညာချက်များ', 'all' => 'အားလုံး', 'results' => 'စာမေးပွဲရလဒ်', 'dates' => 'စာမေးပွဲရက်စွဲ', 'gen' => 'အထွေထွေ']
];
$curr = $t[$lang];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= $curr['title'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pyidaungsu&family=Inter:wght@400;700&display=swap"
        rel="stylesheet">
    <link href="assets/css/announcements.css" rel="stylesheet">

</head>

<body>
    <div class="padding"></div>
    <?php include 'navbar.php'; ?>
    <div class="page-header container mt-4 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2 class="page-title mb-0"><?= $curr['title'] ?></h2>

            <div class="lang-switch">
                <a href="?lang=en" class="btn btn-sm <?= $lang=='en'?'btn-warning':'btn-outline-secondary' ?>">EN</a>
                <a href="?lang=mm"
                    class="btn btn-sm <?= $lang=='mm'?'btn-warning':'btn-outline-secondary' ?>">မြန်မာ</a>
            </div>
        </div>
    </div>


    <div class="container mt-2">
        <div class="filter-container mb-5">
            <a href="?lang=<?= $lang ?>&category=all"
                class="cat-btn <?= $cat_filter=='all'?'active':'' ?>"><?= $curr['all'] ?></a>
            <a href="?lang=<?= $lang ?>&category=exam_results"
                class="cat-btn <?= $cat_filter=='exam_results'?'active':'' ?>"><?= $curr['results'] ?></a>
            <a href="?lang=<?= $lang ?>&category=exam_dates"
                class="cat-btn <?= $cat_filter=='exam_dates'?'active':'' ?>"><?= $curr['dates'] ?></a>
            <a href="?lang=<?= $lang ?>&category=general"
                class="cat-btn <?= $cat_filter=='general'?'active':'' ?>"><?= $curr['gen'] ?></a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if(empty($announcements)): ?>
                <div class="text-center py-5 text-muted">
                    No announcements found in this category.
                </div>
                <?php endif; ?>

                <?php foreach($announcements as $a): ?>
                <div class="card announcement-card p-4 <?= $a['importance_level']=='high'?'importance-high':'' ?>">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="date-badge me-2">
                                <i class="fa fa-calendar-alt me-1"></i>
                                <?= date('d M Y', strtotime($a['publish_date'])) ?>
                            </span>
                            <?php if($a['importance_level']=='high'): ?>
                            <span class="badge bg-danger">IMPORTANT</span>
                            <?php endif; ?>
                        </div>
                        <small
                            class="text-uppercase fw-bold text-muted"><?= str_replace('_', ' ', $a['category']) ?></small>
                    </div>

                    <h3 class="fw-bold mb-3" style="color: var(--monastery-maroon);">
                        <?= htmlspecialchars($lang == 'en' ? $a['title_en'] : $a['title_mm']) ?>
                    </h3>

                    <div class="content-area" style="line-height: 1.8; font-size: 1.1rem;">
                        <?= nl2br(htmlspecialchars($lang == 'en' ? $a['content_en'] : $a['content_mm'])) ?>
                    </div>

                    <div class="mt-4 pt-3 border-top text-muted small">
                        Posted by: <?= htmlspecialchars($a['author_name']) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>


    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
</body>

</html>