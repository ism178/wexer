<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Check if customer ID and table ID are provided
if (isset($_GET['id'], $_GET['table_id'])) {
    $id = (int)$_GET['id'];
    $table_id = (int)$_GET['table_id'];

    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ? AND table_id = ? AND user_id = ?");
    if ($stmt->execute([$id, $table_id, $_SESSION['user_id']])) {
        header("Location: dashboard.php?id=$table_id");
        exit();
    } else {
        $error = "Failed to delete customer.";
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>
