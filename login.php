<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = $_SESSION["error"] ?? "";
$success = $_SESSION["success"] ?? "";

unset($_SESSION["error"], $_SESSION["success"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Restaurant System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="auth-card">

        <div class="logo">
            <h2>Restaurant System</h2>
            <p>Login to your account</p>
        </div>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <?php if (!empty($success)): ?>

            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>

        <form action="auth/login_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Username or Email</label>
                <input type="text" name="login" class="form-control" placeholder="Enter username or email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
            </div>

            <button type="submit" class="btn-auth">Login</button>

        </form>

        <?php if (!isset($_SESSION["user_id"])): ?>

            <p class="text-center mt-4 mb-0">
                Don't have an account?
                <a href="register.php" class="auth-link">Register</a>
            </p>

        <?php endif; ?>

    </div>

</body>

</html>