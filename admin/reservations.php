<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";


// =====================================================
// FETCH RESERVATIONS
// =====================================================

$stmt = $pdo->query("
    SELECT
        id,
        full_name,
        email,
        phone,
        reservation_date,
        reservation_time,
        guests,
        special_request,
        status,
        created_at
    FROM reservations
    ORDER BY reservation_date ASC, reservation_time ASC
");

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// FLASH MESSAGES
// =====================================================

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";

unset($_SESSION["success"], $_SESSION["error"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Reservations | Bite & Bliss
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
                        Reservations
                    </h1>

                    <p>
                        View and manage table reservations booked by customers.
                    </p>

                </div>


                <a
                    href="reservation_add.php"
                    class="gold-btn">

                    <i
                        class="bi bi-calendar-plus"
                        aria-hidden="true">
                    </i>

                    Add Reservation

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
                 RESERVATIONS CARD
            ================================================== -->

            <div class="dashboard-card contact-messages-card">

                <div class="card-header-custom">

                    <div>

                        <h5>
                            All Reservations
                        </h5>

                        <p>
                            Bookings submitted through the Reservations form, ordered by date.
                        </p>

                    </div>


                    <span class="message-count">

                        <?= count($reservations) ?>

                        <?= count($reservations) === 1
                            ? "Reservation"
                            : "Reservations"
                        ?>

                    </span>

                </div>


                <!-- =================================================
                     RESERVATIONS TABLE
                ================================================== -->

                <div class="table-responsive">

                    <table
                        class="table restaurant-table contact-messages-table">

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    GUEST
                                </th>

                                <th>
                                    EMAIL
                                </th>

                                <th>
                                    PHONE
                                </th>

                                <th>
                                    DATE &amp; TIME
                                </th>

                                <th>
                                    GUESTS
                                </th>

                                <th>
                                    SPECIAL REQUEST
                                </th>

                                <th>
                                    STATUS
                                </th>

                                <th>
                                    ACTION
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php if (empty($reservations)): ?>

                                <tr>

                                    <td
                                        colspan="9"
                                        class="text-center">

                                        <div class="empty-messages">

                                            <i
                                                class="bi bi-calendar-x">
                                            </i>

                                            <h6>
                                                No Reservations Found
                                            </h6>

                                            <p>
                                                There are no table reservations yet.
                                            </p>

                                        </div>

                                    </td>

                                </tr>


                            <?php else: ?>


                                <?php foreach ($reservations as $reservation): ?>


                                    <tr>


                                        <!-- ID -->

                                        <td>

                                            <strong>
                                                #<?= (int) $reservation["id"] ?>
                                            </strong>

                                        </td>


                                        <!-- GUEST -->

                                        <td>

                                            <div class="message-user">

                                                <span class="message-avatar">

                                                    <?= htmlspecialchars(
                                                        strtoupper(
                                                            substr(
                                                                $reservation["full_name"],
                                                                0,
                                                                1
                                                            )
                                                        ),
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                                <span>

                                                    <?= htmlspecialchars(
                                                        $reservation["full_name"],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </span>

                                            </div>

                                        </td>


                                        <!-- EMAIL -->

                                        <td>

                                            <a
                                                href="mailto:<?= htmlspecialchars(
                                                    $reservation["email"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                class="contact-link">

                                                <?= htmlspecialchars(
                                                    $reservation["email"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </a>

                                        </td>


                                        <!-- PHONE -->

                                        <td>

                                            <a
                                                href="tel:<?= htmlspecialchars(
                                                    $reservation["phone"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>"
                                                class="contact-link">

                                                <?= htmlspecialchars(
                                                    $reservation["phone"],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </a>

                                        </td>


                                        <!-- DATE & TIME -->

                                        <td>

                                            <?= date(
                                                "d M Y",
                                                strtotime(
                                                    $reservation["reservation_date"]
                                                )
                                            ) ?>

                                            <br>

                                            <span class="username-text">

                                                <?= date(
                                                    "h:i A",
                                                    strtotime(
                                                        $reservation["reservation_time"]
                                                    )
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- GUESTS -->

                                        <td>

                                            <?= (int) $reservation["guests"] ?>

                                            <?= (int) $reservation["guests"] === 1
                                                ? "Guest"
                                                : "Guests"
                                            ?>

                                        </td>


                                        <!-- SPECIAL REQUEST -->

                                        <td>

                                            <?php if (
                                                !empty($reservation["special_request"])
                                            ): ?>

                                                <div class="message-content">

                                                    <?= nl2br(
                                                        htmlspecialchars(
                                                            $reservation["special_request"],
                                                            ENT_QUOTES,
                                                            'UTF-8'
                                                        )
                                                    ) ?>

                                                </div>

                                            <?php else: ?>

                                                <span class="empty-value">
                                                    —
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <!-- STATUS -->

                                        <td>

                                            <?php

                                            $status = $reservation["status"];

                                            $statusClass = match ($status) {

                                                "Pending" =>
                                                    "message-status-new",

                                                "Confirmed" =>
                                                    "message-status-replied",

                                                "Completed" =>
                                                    "message-status-read",

                                                "Cancelled" =>
                                                    "message-status-cancelled",

                                                default =>
                                                    "message-status-default"

                                            };

                                            ?>

                                            <span
                                                class="message-status <?= $statusClass ?>">

                                                <?= htmlspecialchars(
                                                    $status,
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                                ) ?>

                                            </span>

                                        </td>


                                        <!-- ACTION -->

                                        <td>

                                            <div class="user-actions">


                                                <!-- EDIT -->

                                                <a
                                                    href="reservation_edit.php?id=<?= (int) $reservation["id"] ?>"
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit reservation">

                                                    <i
                                                        class="bi bi-pencil"
                                                        aria-hidden="true">
                                                    </i>

                                                </a>


                                                <!-- DELETE -->

                                                <form
                                                    action="reservation_delete.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this reservation?');">

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $reservation["id"] ?>">


                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Delete reservation">

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


<script src="js/admin.js"></script>

</body>
</html>