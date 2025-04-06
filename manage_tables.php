<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle table creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $stmt = $pdo->prepare("INSERT INTO tables (user_id, title) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title]);
    header("Location: manage_tables.php");
    exit();
}

// Handle table deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_table_id'])) {
    $delete_table_id = (int)$_POST['delete_table_id'];

    $stmt = $pdo->prepare("DELETE FROM tables WHERE id = ? AND user_id = ?");
    $stmt->execute([$delete_table_id, $_SESSION['user_id']]);

    header("Location: manage_tables.php");
    exit();
}

// Fetch user tables
$stmt = $pdo->prepare("SELECT * FROM tables WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$tables = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tables</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="post" class="card p-4 shadow-sm mb-4">
                <h2 class="text-center">Create New Table</h2>
                <div class="mb-3">
                    <input type="text" name="title" class="form-control" placeholder="Table Title" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Table</button>
            </form>
            <h3 class="text-center">Your Tables</h3>
            <ul class="list-group">
                <?php foreach ($tables as $table): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?= htmlspecialchars($table['title']) ?>
                        <div>
                            <a href="edit_table.php?id=<?= $table['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="delete_table_id" value="<?= $table['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this table?');">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <a href="dashboard.php" class="btn btn-link w-100 mt-3">← Back to Dashboard</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
