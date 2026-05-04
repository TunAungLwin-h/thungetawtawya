<?php
require_once "../configuration/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['sender_name'] ?? '';
    $email = $_POST['sender_email'] ?? '';
    $category = $_POST['category'] ?? 'Inquiry';
    $message = $_POST['message_body'] ?? '';

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO feedback_submissions (sender_name, sender_email, category, message_body) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $email, $category, $message])) {
            header("Location: contact.php?status=success");
        } else {
            header("Location: contact.php?status=error");
        }
    } else {
        header("Location: contact.php?status=missing");
    }
    exit;
}