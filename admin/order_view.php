<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

$id = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$id) {

    $_SESSION["error"] = "Invalid order.";
    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Order
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $id
]);

$order = $stmt->fetch();

if (!$order) {

    $_SESSION["error"] = "Order not found.";
    header("Location: orders.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get Order Items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM order_items
    WHERE order_id = :order_id
    ORDER BY id ASC
");

$stmt->execute([
    ":order_id" => $id
]);

$items = $stmt->fetchAll();

$statusClass = strtolower(
    str_replace(" ", "-", $order["status"])
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Order #<?= (int)$order["id"] ?>
    </title>

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

<body class="admin-order-view-page">

<div class="admin-layout">

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>

    <?php require_once "includes/sidebar.php"; ?>

    <main class="main-content">

        <div class="container-fluid p-4">

            <!-- HEADING -->

            <div class="page-heading">

                <div>

                    <p class="gold-label">
                        BITE &amp; BLISS
                    </p>

                    <h1>
                        Order #<?= (int)$order["id"] ?>
                    </h1>

                    <p>
                        Complete order information.
                    </p>

                </div>

                <div class="d-flex gap-2">

                    <a
                        href="order_edit.php?id=<?= (int)$order["id"] ?>"
                        class="gold-btn"
                    >
                        <i class="bi bi-pencil"></i>
                        Edit Order
                    </a>

                    <a
                        href="orders.php"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-arrow-left"></i>
                        Back
                    </a>

                </div>

            </div>


            <div class="row g-4">

                <!-- CUSTOMER INFORMATION -->

                <div class="col-lg-5">

                    <div class="dashboard-card p-4 admin-order-info-card">

                        <h5>
                            Customer Information
                        </h5>

                        <hr>

                        <p>
                            <strong>Name:</strong><br>
                            <?= htmlspecialchars(
                                $order["full_name"]
                            ) ?>
                        </p>

                        <p>
                            <strong>Email:</strong><br>
                            <?= htmlspecialchars(
                                $order["email"]
                            ) ?>
                        </p>

                        <p>
                            <strong>Phone:</strong><br>
                            <?= htmlspecialchars(
                                $order["phone"]
                            ) ?>
                        </p>

                        <p>
                            <strong>Order Type:</strong><br>
                            <?= htmlspecialchars(
                                $order["order_type"]
                            ) ?>
                        </p>

                        <?php if (
                            $order["table_number"] !== null &&
                            $order["table_number"] !== ""
                        ): ?>

                            <p>
                                <strong>Table:</strong><br>
                                <?= htmlspecialchars(
                                    $order["table_number"]
                                ) ?>
                            </p>

                        <?php endif; ?>


                        <?php if (
                            $order["delivery_address"] !== null &&
                            $order["delivery_address"] !== ""
                        ): ?>

                            <p>
                                <strong>
                                    Delivery Address:
                                </strong><br>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $order["delivery_address"]
                                    )
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <?php if (
                            $order["special_request"] !== null &&
                            $order["special_request"] !== ""
                        ): ?>

                            <p>
                                <strong>
                                    Special Request:
                                </strong><br>

                                <?= nl2br(
                                    htmlspecialchars(
                                        $order["special_request"]
                                    )
                                ) ?>

                            </p>

                        <?php endif; ?>


                        <p>
                            <strong>Status:</strong><br>

                            <span class="admin-order-status status-<?= htmlspecialchars($statusClass) ?>">
                                <?= htmlspecialchars(
                                    $order["status"]
                                ) ?>
                            </span>

                        </p>


                        <p class="mb-0">

                            <strong>
                                Order Date:
                            </strong><br>

                            <?= htmlspecialchars(
                                $order["created_at"]
                            ) ?>

                        </p>

                    </div>

                </div>


                <!-- ORDER ITEMS -->

                <div class="col-lg-7">

                    <div class="dashboard-card admin-order-items-card">

                        <div class="p-4">

                            <h5>
                                Order Items
                            </h5>

                        </div>

                        <div class="table-responsive">

                            <table class="table admin-order-items-table">

                                <thead>

                                    <tr>

                                        <th>
                                            ITEM
                                        </th>

                                        <th>
                                            PRICE
                                        </th>

                                        <th>
                                            QTY
                                        </th>

                                        <th>
                                            SUBTOTAL
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                <?php foreach ($items as $item): ?>

                                    <tr>

                                        <td>

                                            <?= htmlspecialchars(
                                                $item["item_name"]
                                            ) ?>

                                        </td>

                                        <td>

                                            Rs.
                                            <?= number_format(
                                                (float)$item["item_price"],
                                                2
                                            ) ?>

                                        </td>

                                        <td>

                                            <?= (int)$item["quantity"] ?>

                                        </td>

                                        <td>

                                            Rs.
                                            <?= number_format(
                                                (float)$item["subtotal"],
                                                2
                                            ) ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                                <?php if (!$items): ?>

                                    <tr>

                                        <td
                                            colspan="4"
                                            class="text-center py-4"
                                        >
                                            No order items found.
                                        </td>

                                    </tr>

                                <?php endif; ?>

                                </tbody>

                                <tfoot>

                                    <tr>

                                        <th colspan="3">
                                            TOTAL
                                        </th>

                                        <th>
                                            Rs.
                                            <?= number_format(
                                                (float)$order["total_amount"],
                                                2
                                            ) ?>
                                        </th>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<script src="js/admin.js"></script>

</body>
</html>
