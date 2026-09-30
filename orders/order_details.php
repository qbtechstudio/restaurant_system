<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {

    $_SESSION["error"] =
        "Please login to view your orders.";

    header("Location: ../login.php");
    exit;
}

$order_id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$order_id) {
    header("Location: my_orders.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get the order — scoped to the logged-in user, so nobody can view
| someone else's order just by guessing an id in the URL.
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        full_name,
        email,
        phone,
        order_type,
        table_number,
        delivery_address,
        special_request,
        total_amount,
        status,
        created_at
    FROM orders
    WHERE id = :id
    AND user_id = :user_id
    LIMIT 1
");

$stmt->execute([
    ":id" => $order_id,
    ":user_id" => $_SESSION["user_id"]
]);

$order = $stmt->fetch();

if (!$order) {

    $_SESSION["error"] =
        "Order not found.";

    header("Location: my_orders.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get the order's line items
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT item_name, item_price, quantity, subtotal
    FROM order_items
    WHERE order_id = :order_id
");

$stmt->execute([
    ":order_id" => $order_id
]);

$items = $stmt->fetchAll();

$statusClass = match ($order["status"]) {
    "Pending" => "status-pending",
    "Preparing" => "status-preparing",
    "Ready" => "status-ready",
    "Completed" => "status-completed",
    "Cancelled" => "status-cancelled",
    default => "status-pending"
};

require_once "../includes/header.php";
?>

<main class="container py-5 order-details-page">

    <div class="text-center mb-5">
        <h1>Order #<?= (int) $order["id"] ?></h1>
        <p>Placed on <?= date("d M Y, h:i A", strtotime($order["created_at"])) ?></p>
    </div>

    <div class="row g-4">

        <div class="col-lg-7">

            <div class="card shadow-sm order-details-card">

                <div class="card-body p-4">

                    <h4>Items</h4>

                    <hr>

                    <?php foreach ($items as $item): ?>

                        <div class="d-flex justify-content-between mb-2 order-detail-item">

                            <span class="order-detail-item-name">
                                <?= htmlspecialchars($item["item_name"]) ?>
                                &times; <?= (int) $item["quantity"] ?>
                            </span>

                            <span class="order-detail-item-price">
                                Rs. <?= number_format($item["subtotal"], 2) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                    <hr>

                    <div class="d-flex justify-content-between order-detail-total">
                        <strong>Total</strong>
                        <strong>Rs. <?= number_format($order["total_amount"], 2) ?></strong>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="card shadow-sm order-details-card">

                <div class="card-body p-4">

                    <h4>Order Details</h4>

                    <hr>

                    <div class="d-flex justify-content-between mb-2 order-meta-row">
                        <span class="order-meta-label">Status</span>
                        <span class="order-status-badge <?= $statusClass ?>">
                            <?= htmlspecialchars($order["status"]) ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 order-meta-row">
                        <span class="order-meta-label">Order Type</span>
                        <span class="order-meta-value"><?= htmlspecialchars($order["order_type"]) ?></span>
                    </div>

                    <?php if ($order["order_type"] === "Dine-in" && $order["table_number"]): ?>

                        <div class="d-flex justify-content-between mb-2 order-meta-row">
                            <span class="order-meta-label">Table Number</span>
                            <span class="order-meta-value"><?= htmlspecialchars($order["table_number"]) ?></span>
                        </div>

                    <?php endif; ?>

                    <?php if ($order["order_type"] === "Delivery" && $order["delivery_address"]): ?>

                        <div class="d-flex justify-content-between mb-2 order-meta-row">
                            <span class="order-meta-label">Delivery Address</span>
                            <span class="order-meta-value"><?= htmlspecialchars($order["delivery_address"]) ?></span>
                        </div>

                    <?php endif; ?>

                    <div class="d-flex justify-content-between mb-2 order-meta-row">
                        <span class="order-meta-label">Phone</span>
                        <span class="order-meta-value"><?= htmlspecialchars($order["phone"]) ?></span>
                    </div>

                    <?php if (!empty($order["special_request"])): ?>

                        <div class="mt-3 order-special-request">
                            <span class="order-meta-label">Special Request</span>
                            <p class="mb-0 order-meta-value"><?= nl2br(htmlspecialchars($order["special_request"])) ?></p>
                        </div>

                    <?php endif; ?>

                    <a href="my_orders.php" class="btn btn-outline-secondary w-100 mt-4">
                        Back to My Orders
                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>
