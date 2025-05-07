<?php
session_start();
require 'db.php';

try {
    // Ensure $pdo is properly initialized
    if (!$pdo) {
        throw new Exception("Database connection not established.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usertag = trim($_POST['usertag']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE usertag = ?");
    $stmt->execute([$usertag]);
    $user = $stmt->fetch();

    // Debugging: Log fetched user data
    error_log("Fetched user: " . print_r($user, true));

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        // Debugging: Log password verification failure
        if ($user) {
            error_log("Password verification failed for usertag: $usertag");
        } else {
            error_log("No user found with usertag: $usertag");
        }
        $error = "Invalid login.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Customer Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="invoice.php">
                        <i class="bi bi-receipt"></i> Invoice
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form method="post" class="card p-4 shadow-sm">
                <h2 class="text-center">Login</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <div class="mb-3">
                    <input type="text" name="usertag" class="form-control" placeholder="@usertag" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3">Don't have an account? <a href="register.php">Register here</a></p>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
