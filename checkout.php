<?php

session_start();

require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {

    $_SESSION["error"] =
        "Please login before placing an order.";

    header("Location: login.php");
    exit;
}

$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {

    $_SESSION["error"] =
        "Your cart is empty.";

    header("Location: cart.php");
    exit;
}

$user_id = $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| Get logged-in user
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, name, email
    FROM users
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $user_id
]);

$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Calculate cart total
|--------------------------------------------------------------------------
*/

$ids = array_keys($cart);

$placeholders =
    implode(",", array_fill(0, count($ids), "?"));

$stmt = $pdo->prepare("
    SELECT id, name, price
    FROM menu_items
    WHERE id IN ($placeholders)
");

$stmt->execute($ids);

$items = $stmt->fetchAll();

$total = 0;

foreach ($items as $item) {

    $quantity = $cart[$item["id"]] ?? 0;

    $total += $item["price"] * $quantity;
}

$success = $_SESSION["success"] ?? "";
$error = $_SESSION["error"] ?? "";
$checkout_form_data = $_SESSION["checkout_form_data"] ?? [];

unset(
    $_SESSION["success"],
    $_SESSION["error"],
    $_SESSION["checkout_form_data"]
);

if (!is_array($checkout_form_data)) {
    $checkout_form_data = [];
}

$selected_order_type = $checkout_form_data["order_type"] ?? "Dine-in";

if (!in_array($selected_order_type, ["Dine-in", "Takeaway", "Delivery"], true)) {
    $selected_order_type = "Dine-in";
}

require_once "includes/header.php";
?>

<main class="container py-5 checkout-page">

    <div class="text-center mb-5">
        <h1>Checkout</h1>
        <p>Complete your order details.</p>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-7">

            <div class="card shadow-sm checkout-card">

                <div class="card-body p-4">

                    <form
                        action="orders/place_order.php"
                        method="POST"
                        id="checkoutForm"
                        novalidate>

                        <div class="mb-3">

                            <label class="form-label" for="full_name">
                                Full Name
                            </label>

                            <input
                                type="text"
                                id="full_name"
                                name="full_name"
                                class="form-control"
                                value="<?= htmlspecialchars($checkout_form_data["full_name"] ?? $user["name"], ENT_QUOTES, "UTF-8") ?>"
                                autocomplete="name"
                                minlength="2"
                                maxlength="100"
                                required
                                aria-describedby="full_name_feedback">

                            <div class="invalid-feedback" id="full_name_feedback">
                                Enter your name using at least 2 characters.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="email">
                                Email
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?= htmlspecialchars($checkout_form_data["email"] ?? $user["email"], ENT_QUOTES, "UTF-8") ?>"
                                autocomplete="email"
                                maxlength="254"
                                required
                                aria-describedby="email_feedback">

                            <div class="invalid-feedback" id="email_feedback">
                                Enter a valid email address.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="phone">
                                Phone
                            </label>

                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form-control"
                                value="<?= htmlspecialchars($checkout_form_data["phone"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                autocomplete="tel"
                                inputmode="tel"
                                pattern="[0-9+() -]{7,20}"
                                maxlength="20"
                                required
                                aria-describedby="phone_feedback">

                            <div class="invalid-feedback" id="phone_feedback">
                                Enter a valid phone number with 7 to 20 digits or symbols.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="order_type">
                                Order Type
                            </label>

                            <select
                                name="order_type"
                                id="order_type"
                                class="form-select"
                                required>
                                <option value="Dine-in" <?= $selected_order_type === "Dine-in" ? "selected" : "" ?>>
                                    Dine In
                                </option>

                                <option value="Takeaway" <?= $selected_order_type === "Takeaway" ? "selected" : "" ?>>
                                    Takeaway
                                </option>

                                <option value="Delivery" <?= $selected_order_type === "Delivery" ? "selected" : "" ?>>
                                    Delivery
                                </option>
                            </select>

                        </div>

                        <div
                            class="mb-3"
                            id="table_field">

                            <label class="form-label" for="table_number_input">
                                Table Number
                            </label>

                            <input
                                type="text"
                                name="table_number"
                                id="table_number_input"
                                class="form-control"
                                value="<?= htmlspecialchars($checkout_form_data["table_number"] ?? "", ENT_QUOTES, "UTF-8") ?>"
                                maxlength="30"
                                aria-describedby="table_number_feedback">

                            <div class="invalid-feedback" id="table_number_feedback">
                                Enter your table number.
                            </div>

                        </div>

                        <div
                            class="mb-3"
                            id="address_field"
                            style="display:none;">

                            <label class="form-label" for="delivery_address_input">
                                Delivery Address
                            </label>

                            <textarea
                                name="delivery_address"
                                id="delivery_address_input"
                                class="form-control"
                                rows="3"
                                minlength="10"
                                maxlength="500"
                                aria-describedby="delivery_address_feedback"><?= htmlspecialchars($checkout_form_data["delivery_address"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>

                            <div class="invalid-feedback" id="delivery_address_feedback">
                                Enter a delivery address with at least 10 characters.
                            </div>

                        </div>

                        <div class="mb-3">

                            <label class="form-label" for="special_request">
                                Special Request
                            </label>

                            <textarea
                                name="special_request"
                                id="special_request"
                                class="form-control"
                                rows="3"
                                maxlength="1000"><?= htmlspecialchars($checkout_form_data["special_request"] ?? "", ENT_QUOTES, "UTF-8") ?></textarea>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary w-100">
                            Place Order
                        </button>

                    </form>

                </div>

            </div>

        </div>

        <div class="col-lg-5">

            <div class="card shadow-sm checkout-card checkout-summary-card">

                <div class="card-body p-4">

                    <h4>Order Summary</h4>

                    <hr>

                    <?php foreach ($items as $item): ?>

                        <?php
                        $quantity =
                            $cart[$item["id"]] ?? 0;
                        ?>

                        <div
                            class="d-flex justify-content-between mb-2 checkout-summary-item">

                            <span class="checkout-summary-item-name">
                                <?= htmlspecialchars($item["name"]) ?>
                                &times; <?= $quantity ?>
                            </span>

                            <span class="checkout-summary-item-price">
                                Rs.
                                <?= number_format(
                                    $item["price"] * $quantity,
                                    2
                                ) ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                    <hr>

                    <div
                        class="d-flex justify-content-between">

                        <strong>Total</strong>

                        <strong>
                            Rs.
                            <?= number_format($total, 2) ?>
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<script>
    const orderType =
        document.getElementById("order_type");

    const tableField =
        document.getElementById("table_field");

    const addressField =
        document.getElementById("address_field");

    const tableInput =
        document.getElementById("table_number_input");

    const addressInput =
        document.getElementById("delivery_address_input");

    const checkoutForm =
        document.getElementById("checkoutForm");

    function updateOrderFields() {

        if (orderType.value === "Dine-in") {

            tableField.style.display = "block";
            addressField.style.display = "none";

            tableInput.required = true;
            tableInput.disabled = false;
            addressInput.required = false;
            addressInput.disabled = true;

        } else if (orderType.value === "Delivery") {

            tableField.style.display = "none";
            addressField.style.display = "block";

            tableInput.required = false;
            tableInput.disabled = true;
            addressInput.required = true;
            addressInput.disabled = false;

        } else {

            tableField.style.display = "none";
            addressField.style.display = "none";

            tableInput.required = false;
            tableInput.disabled = true;
            addressInput.required = false;
            addressInput.disabled = true;

        }
    }

    orderType.addEventListener(
        "change",
        updateOrderFields
    );

    checkoutForm.addEventListener("submit", function (event) {
        updateOrderFields();

        if (!checkoutForm.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        checkoutForm.classList.add("was-validated");
    });

    updateOrderFields();
</script>

<?php require_once "includes/footer.php"; ?>
