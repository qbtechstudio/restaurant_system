<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$error = "";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: users.php");
    exit;
}


// Get existing user
$stmt = $pdo->prepare("
    SELECT id, name, username, email, role, status
    FROM users
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$user = $stmt->fetch();

if (!$user) {
    header("Location: users.php");
    exit;
}


// Update user
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $role = $_POST["role"] ?? "";
    $status = $_POST["status"] ?? "";


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


    // Role validation
    elseif (!in_array($role, ["user", "admin"], true)) {

        $error = "Invalid role selected.";

    }


    // Status validation
    elseif (!in_array($status, ["0", "1"], true)) {

        $error = "Invalid status selected.";

    }


    // Password validation only if entered
    elseif ($password !== "" && strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== "" && $password !== $confirm_password) {

        $error = "Passwords do not match.";

    }


    if ($error === "") {

        // Check duplicate username/email
        $stmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE (username = :username OR email = :email)
            AND id != :id
            LIMIT 1
        ");

        $stmt->execute([
            ":username" => $username,
            ":email" => $email,
            ":id" => $id
        ]);

        $existingUser = $stmt->fetch();


        if ($existingUser) {

            $error = "Username or email already exists.";

        } else {

            if ($password !== "") {

                // New password was entered
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare("
                    UPDATE users
                    SET
                        name = :name,
                        username = :username,
                        email = :email,
                        password = :password,
                        role = :role,
                        status = :status
                    WHERE id = :id
                ");

                $stmt->execute([
                    ":name" => $name,
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $hashedPassword,
                    ":role" => $role,
                    ":status" => $status,
                    ":id" => $id
                ]);

            } else {

                // Keep existing password
                $stmt = $pdo->prepare("
                    UPDATE users
                    SET
                        name = :name,
                        username = :username,
                        email = :email,
                        role = :role,
                        status = :status
                    WHERE id = :id
                ");

                $stmt->execute([
                    ":name" => $name,
                    ":username" => $username,
                    ":email" => $email,
                    ":role" => $role,
                    ":status" => $status,
                    ":id" => $id
                ]);
            }


            $_SESSION["success"] = "User updated successfully.";

            header("Location: users.php");
            exit;
        }
    }


    // Keep entered values in the form after an error
    $user["name"] = $name;
    $user["username"] = $username;
    $user["email"] = $email;
    $user["role"] = $role;
    $user["status"] = $status;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit User</title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="css/admin.css"
    >

    <script src="js/theme.js"></script>
</head>


<body>

<div class="admin-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>


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
                        Edit User
                    </h1>

                    <p>
                        Update user account information and access.
                    </p>

                </div>


                <a
                    href="users.php"
                    class="gold-btn"
                >

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


            <!-- EDIT USER CARD -->
            <div class="dashboard-card user-form-card">


                <div class="card-header-custom">

                    <div>

                        <h5>
                            User Information
                        </h5>

                        <p>
                            Update the details for this user account.
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
                                value="<?= htmlspecialchars($user["name"]) ?>"
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
                                value="<?= htmlspecialchars($user["username"]) ?>"
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
                                value="<?= htmlspecialchars($user["email"]) ?>"
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
                                    <?= $user["role"] === "user" ? "selected" : "" ?>
                                >
                                    User
                                </option>

                                <option
                                    value="admin"
                                    <?= $user["role"] === "admin" ? "selected" : "" ?>
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
                                    <?= (int)$user["status"] === 1 ? "selected" : "" ?>
                                >
                                    Active
                                </option>

                                <option
                                    value="0"
                                    <?= (int)$user["status"] === 0 ? "selected" : "" ?>
                                >
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- PASSWORD SECTION -->
                    <div class="user-password-section">

                        <div class="user-section-heading">

                            <h5>
                                Change Password
                            </h5>

                            <p>
                                Leave these fields empty to keep the current password.
                            </p>

                        </div>


                        <div class="row g-4">


                            <!-- NEW PASSWORD -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    New Password
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
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                >

                            </div>

                        </div>

                    </div>


                    <!-- BUTTONS -->
                    <div class="d-flex gap-2 mt-4 pt-3">

                        <button
                            type="submit"
                            class="btn-submit"
                        >

                            <i class="bi bi-save"></i>

                            Update User

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