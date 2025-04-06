<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_id'], $_POST['name'], $_POST['phone'])) {
    $table_id = (int)$_POST['table_id'];
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = isset($_POST['address']) ? trim($_POST['address']) : null;
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;

    $stmt = $pdo->prepare("INSERT INTO customers (user_id, table_id, name, phone, address, notes) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $table_id, $name, $phone, $address, $notes]);

    header("Location: dashboard.php?id=$table_id");
    exit();
}
