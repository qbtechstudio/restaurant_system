<?php

session_start();

require_once "../includes/header.php";

$order_id =
    filter_input(
        INPUT_GET,
        "id",
        FILTER_VALIDATE_INT
    );
?>

<div class="container py-5">

    <div class="text-center mb-5">
        <h1>Order Placed Successfully!</h1>
        <p>Thank you for ordering from Bite & Bliss.</p>
    </div>

    <div class="text-center py-5">

        <i class="bi bi-check-circle-fill display-1 text-success"></i>

        <?php if ($order_id): ?>

            <p class="mt-4">
                Your Order ID is:
                <strong>
                    #<?= $order_id ?>
                </strong>
            </p>

        <?php endif; ?>

        <div class="d-flex justify-content-center flex-wrap gap-2 mt-3">

            <?php if ($order_id): ?>

                <a
                    href="order_details.php?id=<?= $order_id ?>"
                    class="btn btn-primary"
                >
                    View Order
                </a>

            <?php endif; ?>

            <a
                href="../menu.php"
                class="btn btn-outline-secondary"
            >
                Continue Shopping
            </a>

        </div>

    </div>

</div>

<?php require_once "../includes/footer.php"; ?>
