<?php
session_start();
require_once __DIR__ . '/../configuration/db.php';
require_once __DIR__ . '/auth/superadmin_guard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $format = trim($_POST['format_string']);
    $year_format = $_POST['year_format'];
    $seq_length = (int)$_POST['seq_length'];

    $pdo->exec("UPDATE roll_formats SET is_active = 0");

    $stmt = $pdo->prepare("
        INSERT INTO roll_formats (format_string, year_format, seq_length, is_active)
        VALUES (?, ?, ?, 1)
    ");
    $stmt->execute([$format, $year_format, $seq_length]);

    $_SESSION['flash'] = "Roll number format updated successfully.";
    header('Location: roll_format_settings.php');
    exit;
}

$current = $pdo->query("
    SELECT * FROM roll_formats WHERE is_active = 1 ORDER BY id DESC LIMIT 1
")->fetch(PDO::FETCH_ASSOC);

$preview = str_replace(
    ['{YEAR}','{LEVEL}','{SEQ}'],
    ['2026','1',str_pad('1', $current['seq_length'] ?? 5, '0', STR_PAD_LEFT)],
    $current['format_string'] ?? '{YEAR}-{LEVEL}-{SEQ}'
);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Roll Number Format</title>
    <link rel="stylesheet" href="assets/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    /* ===== Monastery Theme Variables ===== */
    :root {
        --monastery-bg: #f7f4ec;
        --monastery-card: #fbf9f3;
        --monastery-border: #e4dcc4;
        --monastery-gold: #b08a2e;
        --monastery-text: #3f3a2b;
        --monastery-muted: #7a6a45;
    }

    /* Page wrapper */
    .format-wrapper {
        max-width: 900px;
        margin: auto;
    }

    /* Main card */
    .format-card {
        background: var(--monastery-card);
        border: 1px solid var(--monastery-border);
        border-radius: 18px;
        padding: 32px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
        position: relative;
    }

    /* Headings */
    .format-card h3 {
        font-size: 24px;
        color: var(--monastery-text);
        margin-bottom: 4px;
    }

    .subtitle {
        color: var(--monastery-muted);
        font-size: 14px;
        margin-bottom: 26px;
    }

    /* Form groups */
    .form-group {
        margin-bottom: 22px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
        color: var(--monastery-text);
    }

    .form-group small {
        font-size: 13px;
        color: var(--monastery-muted);
    }

    /* Inputs */
    input[type="text"],
    input[type="number"],
    select {
        width: 100%;
        padding: 12px 14px;
        border-radius: 10px;
        border: 1px solid var(--monastery-border);
        background: #fff;
        font-size: 15px;
    }

    /* Preview */
    .preview-box {
        background: #fff;
        border: 1px dashed var(--monastery-gold);
        padding: 16px;
        border-radius: 12px;
        font-family: monospace;
        font-size: 17px;
        color: var(--monastery-text);
    }

    /* Help button */
    .help-btn {
        background: none;
        border: none;
        color: var(--monastery-gold);
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }

    /* Help panel */
    .help-panel {
        display: none;
        margin-top: 24px;
        background: #fffdf6;
        border-left: 4px solid var(--monastery-gold);
        padding: 20px;
        border-radius: 12px;
        color: var(--monastery-text);
        animation: fadeIn .3s ease;
    }

    .help-panel h4 {
        margin-top: 0;
        color: #5a4a20;
    }

    /* Floating action button */
    .fab {
        position: fixed;
        bottom: 26px;
        right: 30px;
        background: linear-gradient(135deg, #c8a64b, #9b7520);
        color: #fff;
        border: none;
        padding: 14px 22px;
        border-radius: 999px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .25);
        cursor: pointer;
        z-index: 999;
    }

    .fab:hover {
        transform: translateY(-2px);
    }

    /* Alert */
    .alert.success {
        background: #eef6e9;
        border: 1px solid #cfe2c5;
        color: #355f2e;
        padding: 14px;
        border-radius: 10px;
        margin-bottom: 22px;
    }

    /* Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</head>

<body>

    <?php include 'header.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="format-wrapper">
            <?php if (!empty($_SESSION['flash'])): ?>
            <div class="alert success"><?= $_SESSION['flash']; unset($_SESSION['flash']); ?></div>
            <?php endif; ?>

            <form method="post" class="format-card">

                <h3>Format Configuration</h3>

                <p class="subtitle">You can use Burmese, English, symbols, and placeholders.</p>

                <div class="form-group">
                    <label>Format String</label>
                    <input type="text" name="format_string" required
                        value="<?= htmlspecialchars($current['format_string'] ?? '{YEAR}-{LEVEL}-{SEQ}') ?>">
                    <small>
                        Placeholders:
                        <b>{YEAR}</b>, <b>{LEVEL}</b>, <b>{SEQ}</b>
                        <br>Example: <i>သ-အဆင့်{LEVEL}-{SEQ}</i>
                    </small>
                </div>

                <div class="form-group">
                    <label>Year Display</label>
                    <select name="year_format">
                        <option value="YYYY" <?= ($current['year_format'] ?? '') === 'YYYY' ? 'selected' : '' ?>>Full
                            Year (2026)</option>
                        <option value="YY" <?= ($current['year_format'] ?? '') === 'YY' ? 'selected' : '' ?>>Short Year
                            (26)</option>
                        <option value="NONE" <?= ($current['year_format'] ?? '') === 'NONE' ? 'selected' : '' ?>>No Year
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sequence Length</label>
                    <input type="number" name="seq_length" min="1" max="10"
                        value="<?= (int)($current['seq_length'] ?? 5) ?>">
                    <small>Controls zero-padding (e.g. 00001)</small>
                </div>

                <div class="form-group">
                    <label>Live Preview</label>
                    <div class="preview-box"><?= htmlspecialchars($preview) ?></div>
                </div>

                <div class="actions">
                    <button class="fab" onclick="document.querySelector('form').submit()">
                        💾 Save Format
                    </button>
                    <button type="button" class="help-btn" onclick="toggleHelp()">❓ How this works</button>

                </div>

                <div class="help-panel" id="helpPanel">
                    <h4>Roll Number Workflow</h4>
                    <ol>
                        <li>Admin defines the roll number format here.</li>
                        <li>System auto-increments sequence per year & level.</li>
                        <li>Roll number is generated only for <b>approved</b> candidates.</li>
                        <li>The same format applies across Level 1, 2, and 3.</li>
                    </ol>
                    <p>
                        This ensures consistency, avoids duplicates, and supports monastery-style naming.
                    </p>
                </div>

            </form>
        </div>
    </main>

    <script>
    function toggleHelp() {
        const panel = document.getElementById('helpPanel');
        panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
    }
    </script>

</body>

</html>