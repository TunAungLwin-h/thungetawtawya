<?php
// submit_level1.php - secure POST handler for Level 1 registration
session_start();
require_once '../../configuration/db.php'; // provides $pdo

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// CSRF check
$token = $_POST['csrf_token'] ?? '';
if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    http_response_code(400);
    exit('Invalid CSRF token');
}

// Collect and sanitize input
$input = [];
$input['name'] = trim($_POST['name'] ?? '');
$input['email'] = trim($_POST['email'] ?? '');
$input['dob'] = trim($_POST['dob'] ?? '');
$input['address_region'] = trim($_POST['address_region'] ?? '');
$input['address_township'] = trim($_POST['address_township'] ?? '');
$input['address_village'] = trim($_POST['address_village'] ?? '');
$input['father_name'] = trim($_POST['father_name'] ?? '');
$input['mother_name'] = trim($_POST['mother_name'] ?? '');
$input['parent_address_region'] = trim($_POST['parent_address_region'] ?? '');
$input['parent_address_township'] = trim($_POST['parent_address_township'] ?? ''); // NEW
$input['parent_address_village'] = trim($_POST['parent_address_village'] ?? '');
$input['monastery_name'] = trim($_POST['monastery_name'] ?? '');
$input['abbot_name'] = trim($_POST['abbot_name'] ?? '');
$input['country'] = trim($_POST['country'] ?? '');

// Basic server-side validation
$errors = [];

if ($input['name'] === '') $errors[] = 'Name is required';
if ($input['dob'] === '') $errors[] = 'Date of birth is required';
if ($input['email'] === '') $errors[] = 'Email is required';
if ($input['address_region'] === '') $errors[] = 'Region is required';
if ($input['address_township'] === '') $errors[] = 'Township is required';
if ($input['address_village'] === '') $errors[] = 'Village is required';
if ($input['father_name'] === '') $errors[] = 'Father name is required';
if ($input['mother_name'] === '') $errors[] = 'Mother name is required';
if ($input['monastery_name'] === '') $errors[] = 'Monastery name is required';
if ($input['abbot_name'] === '') $errors[] = 'Abbot name is required';
if ($input['parent_address_region'] === '') $errors[] = 'Parent region is required';
if ($input['parent_address_township'] === '') $errors[] = 'Parent township is required';
if ($input['parent_address_village'] === '') $errors[] = 'Parent village is required';

// Validate date format (YYYY-MM-DD)
if ($input['dob'] !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $input['dob']);
    if (!$d || $d->format('Y-m-d') !== $input['dob']) {
        $errors[] = 'Invalid date of birth';
    }
}

// Validate email format
if ($input['email'] !== '' && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// If there are errors, stop
if (count($errors) > 0) {
    http_response_code(422);
    echo "Validation errors:\n";
    foreach ($errors as $e) {
        echo "- " . htmlspecialchars($e) . "\n";
    }
    exit;
}

// Insert into DB
try {
    $sql = "INSERT INTO candidates
        (name, dob, email, address_region, address_township, address_village, 
         father_name, mother_name, parent_address_region,parent_address_township, parent_address_village,
         monastery_name, abbot_name, country, created_at)
        VALUES
        (:name, :dob, :email, :address_region, :address_township, :address_village,
         :father_name, :mother_name, :parent_address_region, :parent_address_township, :parent_address_village,
         :monastery_name, :abbot_name, :country, NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $input['name'],
        ':dob'  => $input['dob'],
        ':email' => $input['email'],
        ':address_region' => $input['address_region'],
        ':address_township' => $input['address_township'],
        ':address_village' => $input['address_village'],
        ':father_name' => $input['father_name'],
        ':mother_name' => $input['mother_name'],
        ':parent_address_region' => $input['parent_address_region'],
        ':parent_address_township' => $input['parent_address_township'],
        ':parent_address_village' => $input['parent_address_village'],
        ':monastery_name' => $input['monastery_name'],
        ':abbot_name' => $input['abbot_name'],
        ':country' => $input['country'] !== '' ? $input['country'] : null,
    ]);

    // Clear CSRF to prevent resubmission
    unset($_SESSION['csrf_token']);

    echo "Registration successful! Reference ID: " . $pdo->lastInsertId();
} catch (Exception $e) {
    http_response_code(500);
    echo "Database error: " . htmlspecialchars($e->getMessage());
    exit;
}