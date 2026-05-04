<?php
// test.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Testing...<br>";

// Test session
if (session_start()) {
    echo "Session started<br>";
} else {
    echo "Session failed<br>";
}

// Test database include
$db_file = __DIR__ . '/../../configuration/db.php';
if (file_exists($db_file)) {
    echo "DB file exists<br>";
    require_once $db_file;
    echo "DB loaded<br>";
} else {
    echo "DB file NOT found at: " . $db_file . "<br>";
}
?>