<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {

    $_SESSION["error"] =
        "Please login to view your orders.";

    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        id,
        order_type,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE user_id = :user_id
    ORDER BY id DESC
");

$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);

$orders = $stmt->fetchAll();

require_once "../includes/header.php";
?>

<main class="container py-5 orders-page">

    <div class="text-center mb-5">

        <h1>My Orders</h1>

        <p>
            View your previous orders.
        </p>

    </div>

    <?php if (empty($orders)): ?>

        <div class="text-center">

            <h4>
                You haven't placed any orders yet.
            </h4>

            <a
                href="../menu.php"
                class="btn btn-primary mt-3"
            >
                Browse Menu
            </a>

        </div>

    <?php else: ?>

        <div class="table-responsive orders-table-wrap">

            <table class="table table-bordered align-middle orders-table">

                <thead>

                    <tr>
                        <th>Order #</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($orders as $order): ?>

                    <?php
                    $orderStatusClass = match ($order["status"]) {
                        "Pending" => "status-pending",
                        "Preparing" => "status-preparing",
                        "Ready" => "status-ready",
                        "Completed" => "status-completed",
                        "Cancelled" => "status-cancelled",
                        default => "status-pending"
                    };
                    ?>

                    <tr>

                        <td>
                            #<?= $order["id"] ?>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                ucfirst(
                                    str_replace(
                                        "_",
                                        " ",
                                        $order["order_type"]
                                    )
                                )
                            ) ?>
                        </td>

                        <td class="orders-total">
                            Rs.
                            <?= number_format(
                                $order["total_amount"],
                                2
                            ) ?>
                        </td>

                        <td>
                            <span class="order-status-badge <?= $orderStatusClass ?>">
                                <?= htmlspecialchars($order["status"]) ?>
                            </span>
                        </td>

                        <td>
                            <?= htmlspecialchars(
                                $order["created_at"]
                            ) ?>
                        </td>

                        <td>

                            <a
                                href="order_details.php?id=<?= $order["id"] ?>"
                                class="btn btn-sm btn-primary"
                            >
                                View
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
