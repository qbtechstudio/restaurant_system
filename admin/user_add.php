<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "user";
    $status = $_POST["status"] ?? "1";

    // Name validation
    if ($name === "") {

        $error = "Please enter the user's name.";

    } elseif (!preg_match('/^[a-zA-Z ]{2,50}$/', $name)) {

        $error = "Name must be 2-50 characters and contain only letters and spaces.";

    }

    // Username validation
    elseif ($username === "") {

        $error = "Please enter a username.";

    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {

        $error = "Username must be 3-30 characters and contain only letters, numbers, and underscores.";

    }

    // Email validation
    elseif ($email === "") {

        $error = "Please enter an email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    }

    // Password validation
    elseif ($password === "") {

        $error = "Please enter a password.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    }

    // Confirm password
    elseif ($confirm_password === "") {

        $error = "Please confirm the password.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    }

    // Role validation
    elseif (!in_array($role, ["user", "admin"], true)) {

        $error = "Invalid role selected.";

    }

    // Status validation
    elseif (!in_array($status, ["0", "1"], true)) {

        $error = "Invalid status selected.";

    }

    // Check username/email
    if ($error === "") {

        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE username = :username OR email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":username" => $username,
            ":email" => $email
        ]);

        $existingUser = $stmt->fetch();

        if ($existingUser) {

            $error = "Username or email already exists.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                (name, username, email, password, role, status)
                VALUES
                (:name, :username, :email, :password, :role, :status)
            ");

            $stmt->execute([
                ":name" => $name,
                ":username" => $username,
                ":email" => $email,
                ":password" => $hashedPassword,
                ":role" => $role,
                ":status" => $status
            ]);

            $_SESSION["success"] = "User added successfully.";

            header("Location: users.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add User</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/admin.css">

    <script src="js/theme.js"></script>
</head>

<body>

<div class="admin-layout">

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">

            <!-- PAGE HEADING -->
            <div class="page-heading users-page-heading">

                <div>

                    <p class="gold-label">
                        BITE & BLISS
                    </p>

                    <h1>
                        Add User
                    </h1>

                    <p>
                        Create a new user account and manage their access.
                    </p>

                </div>

                <a href="users.php" class="gold-btn">

                    <i class="bi bi-arrow-left"></i>

                    Back to Users

                </a>

            </div>


            <!-- ERROR MESSAGE -->
            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- USER FORM CARD -->
            <div class="dashboard-card user-form-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            User Information
                        </h5>

                        <p>
                            Enter the details for the new account.
                        </p>

                    </div>

                </div>


                <form method="POST">

                    <div class="row g-4">

                        <!-- NAME -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["name"] ?? "") ?>"
                            >

                        </div>


                        <!-- USERNAME -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["username"] ?? "") ?>"
                            >

                        </div>


                        <!-- EMAIL -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="text"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                            >

                        </div>


                        <!-- PASSWORD -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                            >

                            <div class="form-text">
                                Password must be at least 6 characters.
                            </div>

                        </div>


                        <!-- CONFIRM PASSWORD -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                            >

                        </div>


                        <!-- ROLE -->
                        <div class="col-md-3">

                            <label class="form-label">
                                Role
                            </label>

                            <select
                                name="role"
                                class="form-select"
                            >

                                <option
                                    value="user"
                                    <?= (($_POST["role"] ?? "user") === "user") ? "selected" : "" ?>
                                >
                                    User
                                </option>

                                <option
                                    value="admin"
                                    <?= (($_POST["role"] ?? "") === "admin") ? "selected" : "" ?>
                                >
                                    Admin
                                </option>

                            </select>

                        </div>


                        <!-- STATUS -->
                        <div class="col-md-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-select"
                            >

                                <option
                                    value="1"
                                    <?= (($_POST["status"] ?? "1") === "1") ? "selected" : "" ?>
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    <?= (($_POST["status"] ?? "") === "0") ? "selected" : "" ?>
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- FORM BUTTONS -->
                    <div class="d-flex gap-2 mt-4 pt-3">

                        <button
                            type="submit"
                            class="btn-submit"
                        >

                            <i class="bi bi-person-plus"></i>

                            Add User

                        </button>


                        <a
                            href="users.php"
                            class="btn-cancel"
                        >

                            Cancel

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </main>

</div>


<script src="js/admin.js"></script>

</body>

</html>