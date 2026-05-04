<?php
require_once __DIR__ . '/admin_guard.php';

if ($_SESSION['admin_role'] !== 'superadmin') {
    http_response_code(403);
    die('Access denied — Super Admin only');
}