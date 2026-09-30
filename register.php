<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = $_SESSION["error"] ?? "";

unset($_SESSION["error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - Restaurant System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <div class="auth-card">

        <div class="logo">
            <h2>Restaurant System</h2>
            <p>Create New Account</p>
        </div>

        <?php if (!empty($error)): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form action="auth/register_process.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name">
            </div>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter username">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter email">
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password">
            </div>

            <button type="submit" class="btn-auth">Register</button>

        </form>

        <p class="text-center mt-4 mb-0">
            Already have an account?
            <a href="login.php" class="auth-link">Login</a>
        </p>

    </div>

</body>

</html>