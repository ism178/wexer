<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch table details
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM tables WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $table = $stmt->fetch();

    if (!$table) {
        header("Location: manage_tables.php");
        exit();
    }
} else {
    header("Location: manage_tables.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['title'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);

    $stmt = $pdo->prepare("UPDATE tables SET title = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $id, $_SESSION['user_id']]);

    header("Location: manage_tables.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Table</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" class="card p-4 shadow-sm">
                <h2 class="text-center">Edit Table</h2>
                <div class="mb-3">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($table['id']) ?>">
                    <label for="title" class="form-label">Table Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($table['title']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                <a href="manage_tables.php" class="btn btn-link w-100 mt-2">← Back to Manage Tables</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
