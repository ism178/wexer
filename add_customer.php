<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("INSERT INTO customers (user_id, name, phone, address, notes) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([
    $_SESSION['user_id'],
    $_POST['name'],
    $_POST['phone'],
    $_POST['address'],
    $_POST['notes']
]);

header("Location: dashboard.php");
exit();
