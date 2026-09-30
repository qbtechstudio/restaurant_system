<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";


// =====================================================
// FETCH USERS
// =====================================================

$stmt = $pdo->query("
    SELECT
        id,
        name,
        username,
        email,
        role,
        status,
        created_at
    FROM users
    ORDER BY id
");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// FLASH MESSAGES
// =====================================================

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset(
    $_SESSION["success"],
    $_SESSION["error"]
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Manage Users | Bite & Bliss
    </title>


    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Admin CSS -->

    <link
        rel="stylesheet"
        href="css/admin.css">

    <script src="js/theme.js"></script>
</head>


<body>

<div class="admin-layout">


    <!-- =====================================================
         SIDEBAR OVERLAY
    ====================================================== -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay">
    </div>


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <?php require_once "includes/sidebar.php"; ?>


    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main-content">

        <div class="container-fluid p-4">


            <!-- =================================================
                 PAGE HEADING
            ================================================== -->

            <div class="page-heading users-page-heading">

                <div>

                    <p class="gold-label">
                        BITE & BLISS
                    </p>

                    <h1>
                        Manage Users
                    </h1>

                    <p>
                        Manage registered users and their account information.
                    </p>

                </div>


                <a
                    href="user_add.php"
                    class="gold-btn">

                    <i
                        class="bi bi-person-plus"
                        aria-hidden="true">
                    </i>

                    Add User

                </a>

            </div>


            <!-- =================================================
                 SUCCESS MESSAGE
            ================================================== -->

            <?php if ($success !== ""): ?>

                <div class="alert alert-success">

                    <?= htmlspecialchars(
                        $success,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 ERROR MESSAGE
            ================================================== -->

            <?php if ($error !== ""): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <!-- =================================================
                 USERS CARD
            ================================================== -->

            <div class="dashboard-card users-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            All Users
                        </h5>

                        <p>
                            View, edit and manage user accounts.
                        </p>

                    </div>


                    <span class="message-count">

                        <?= count($users) ?>

                        <?= count($users) === 1
                            ? "User"
                            : "Users"
                        ?>

                    </span>

                </div>


                <!-- =================================================
                     USERS TABLE
                ================================================== -->

                <div class="table-responsive">

                    <table
                        class="table restaurant-table users-table">

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    USER
                                </th>

                                <th>
                                    USERNAME
                                </th>

                                <th>
                                    EMAIL
                                </th>

                                <th>
                                    ROLE
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    CREATED
                                </th>

                                <th>
                                    ACTION
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (empty($users)): ?>

                                <tr>

                                    <td
                                        colspan="8"
                                        class="text-center">

                                        <div class="empty-messages">

                                            <i
                                                class="bi bi-people">
                                            </i>

                                            <h6>
                                                No Users Found
                                            </h6>

                                            <p>
                                                There are no registered users yet.
                                            </p>

                                        </div>

                                    </td>

                                </tr>


                            <?php else: ?>


                                <?php foreach ($users as $user): ?>


                                    <tr>


                                        <!-- =================================================
                                             ID
                                        ================================================== -->

                                        <td>

                                            <strong>
                                                #<?= (int) $user["id"] ?>
                                            </strong>

                                        </td>


                                        <!-- =================================================
                                             USER
                                        ================================================== -->

                                        <td>

                                            <div class="message-user">

                                                <span class="message-avatar">

                                                    <?= htmlspecialchars(
                                                        strtoupper(
                                                            substr(
                                                                $user["name"],
                                                                0,
                                                                1
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>


                                                <span class="user-name">

                                                    <?= htmlspecialchars(
                                                        $user["name"],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                            </div>

                                        </td>


                                        <!-- =================================================
                                             USERNAME
                                        ================================================== -->

                                        <td>

                                            <span class="username-text">

                                                @<?= htmlspecialchars(
                                                    $user["username"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- =================================================
                                             EMAIL
                                        ================================================== -->

                                        <td>

                                            <a
                                                href="mailto:<?= htmlspecialchars(
                                                    $user["email"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                class="contact-link">

                                                <?= htmlspecialchars(
                                                    $user["email"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </a>

                                        </td>


                                        <!-- =================================================
                                             ROLE
                                        ================================================== -->

                                        <td>

                                            <?php if (
                                                $user["role"] === "admin"
                                            ): ?>

                                                <span class="user-role admin-role">

                                                    <i
                                                        class="bi bi-shield-check"
                                                        aria-hidden="true">
                                                    </i>

                                                    Admin

                                                </span>

                                            <?php else: ?>

                                                <span class="user-role user-role-badge">

                                                    <i
                                                        class="bi bi-person"
                                                        aria-hidden="true">
                                                    </i>

                                                    User

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- =================================================
                                             STATUS
                                        ================================================== -->

                                        <td>

                                            <?php if (
                                                (int) $user["status"] === 1
                                            ): ?>

                                                <span class="message-status message-status-replied">

                                                    <i
                                                        class="bi bi-check-circle"
                                                        aria-hidden="true">
                                                    </i>

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span class="message-status message-status-default">

                                                    <i
                                                        class="bi bi-x-circle"
                                                        aria-hidden="true">
                                                    </i>

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- =================================================
                                             CREATED
                                        ================================================== -->

                                        <td>

                                            <?= date(
                                                "d M Y, h:i A",
                                                strtotime(
                                                    $user["created_at"]
                                                )
                                            ) ?>

                                        </td>


                                        <!-- =================================================
                                             ACTION
                                        ================================================== -->

                                        <td>

                                            <div class="user-actions">


                                                <!-- EDIT -->

                                                <a
                                                    href="user_edit.php?id=<?= (int) $user["id"] ?>"
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit user">

                                                    <i
                                                        class="bi bi-pencil"
                                                        aria-hidden="true">
                                                    </i>

                                                </a>


                                                <!-- DELETE -->

                                                <form
                                                    action="user_delete.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this user?');">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $user["id"] ?>">


                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete user">

                                                        <i
                                                            class="bi bi-trash"
                                                            aria-hidden="true">
                                                        </i>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>


                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>