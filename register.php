<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Hash the password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if username already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);

    if ($check->rowCount() > 0) {
        $error = "Username already taken.";
    } else {
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $hashed])) {
            header("Location: index.php?msg=registered");
            exit();
        } else {
            $error = "Registration failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form method="post" class="card p-4 shadow-sm">
                <h2 class="text-center">Register</h2>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Choose a username" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Choose a password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <a href="index.php" class="btn btn-link w-100 mt-2">← Back to login</a>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
