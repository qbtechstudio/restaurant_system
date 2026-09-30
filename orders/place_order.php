<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../checkout.php");
    exit;
}

if (!isset($_SESSION["user_id"])) {

    $_SESSION["error"] =
        "Please login before placing an order.";

    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$cart = $_SESSION["cart"] ?? [];

if (empty($cart)) {

    $_SESSION["error"] =
        "Your cart is empty.";

    header("Location: ../cart.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Get form data
|--------------------------------------------------------------------------
*/

$full_name =
    trim($_POST["full_name"] ?? "");

$email =
    trim($_POST["email"] ?? "");

$phone =
    trim($_POST["phone"] ?? "");

$order_type =
    $_POST["order_type"] ?? "";

$table_number =
    trim($_POST["table_number"] ?? "");

$delivery_address =
    trim($_POST["delivery_address"] ?? "");

$special_request =
    trim($_POST["special_request"] ?? "");

$checkout_form_data = [
    "full_name" => $full_name,
    "email" => $email,
    "phone" => $phone,
    "order_type" => $order_type,
    "table_number" => $table_number,
    "delivery_address" => $delivery_address,
    "special_request" => $special_request
];

function returnToCheckoutWithError($message, $form_data) {
    $_SESSION["error"] = $message;
    $_SESSION["checkout_form_data"] = $form_data;

    header("Location: ../checkout.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

if (strlen($full_name) < 2 || strlen($full_name) > 100) {
    returnToCheckoutWithError(
        "Please enter a valid full name.",
        $checkout_form_data
    );
}

if (strlen($email) > 254 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    returnToCheckoutWithError(
        "Please enter a valid email address.",
        $checkout_form_data
    );
}

if (!preg_match("/^[0-9+() -]{7,20}$/", $phone)) {
    returnToCheckoutWithError(
        "Please enter a valid phone number.",
        $checkout_form_data
    );
}

$allowed_types = [
    "Dine-in",
    "Takeaway",
    "Delivery"
];

if (!in_array($order_type, $allowed_types, true)) {

    returnToCheckoutWithError(
        "Invalid order type.",
        $checkout_form_data
    );
}

if (
    $order_type === "Dine-in" &&
    $table_number === ""
) {

    returnToCheckoutWithError(
        "Please enter your table number.",
        $checkout_form_data
    );
}

if (
    $order_type === "Delivery" &&
    $delivery_address === ""
) {

    returnToCheckoutWithError(
        "Please enter a delivery address with at least 10 characters.",
        $checkout_form_data
    );
}

if ($order_type === "Dine-in" && strlen($table_number) > 30) {
    returnToCheckoutWithError(
        "Your table number is too long.",
        $checkout_form_data
    );
}

if (
    $order_type === "Delivery" &&
    (strlen($delivery_address) < 10 || strlen($delivery_address) > 500)
) {
    returnToCheckoutWithError(
        "Please enter a delivery address with at least 10 characters.",
        $checkout_form_data
    );
}

if (strlen($special_request) > 1000) {
    returnToCheckoutWithError(
        "Your special request is too long.",
        $checkout_form_data
    );
}

/*
|--------------------------------------------------------------------------
| Get current prices from database
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

$menuItems = $stmt->fetchAll();

if (count($menuItems) !== count($ids)) {

    $_SESSION["error"] =
        "One or more items are no longer available.";

    header("Location: ../cart.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Calculate total
|--------------------------------------------------------------------------
*/

$total = 0;

foreach ($menuItems as $item) {

    $quantity =
        (int)($cart[$item["id"]] ?? 0);

    if ($quantity < 1) {
        continue;
    }

    $total +=
        $item["price"] * $quantity;
}

/*
|--------------------------------------------------------------------------
| Create order
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (
            user_id,
            full_name,
            email,
            phone,
            order_type,
            table_number,
            delivery_address,
            special_request,
            total_amount,
            status
        )
        VALUES (
            :user_id,
            :full_name,
            :email,
            :phone,
            :order_type,
            :table_number,
            :delivery_address,
            :special_request,
            :total_amount,
            'Pending'
        )
    ");

    $stmt->execute([
        ":user_id" => $user_id,
        ":full_name" => $full_name,
        ":email" => $email,
        ":phone" => $phone,
        ":order_type" => $order_type,
        ":table_number" =>
            $order_type === "Dine-in"
                ? $table_number
                : null,
        ":delivery_address" =>
            $order_type === "Delivery"
                ? $delivery_address
                : null,
        ":special_request" =>
            $special_request !== ""
                ? $special_request
                : null,
        ":total_amount" => $total
    ]);

    $order_id =
        $pdo->lastInsertId();

    /*
    |--------------------------------------------------------------------------
    | Insert order items
    |--------------------------------------------------------------------------
    */

    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (
            order_id,
            menu_item_id,
            item_name,
            item_price,
            quantity,
            subtotal
        )
        VALUES (
            :order_id,
            :menu_item_id,
            :item_name,
            :item_price,
            :quantity,
            :subtotal
        )
    ");

    foreach ($menuItems as $item) {

        $quantity =
            (int)($cart[$item["id"]] ?? 0);

        if ($quantity < 1) {
            continue;
        }

        $stmtItem->execute([
            ":order_id" =>
                $order_id,

            ":menu_item_id" =>
                $item["id"],

            ":item_name" =>
                $item["name"],

            ":item_price" =>
                $item["price"],

            ":quantity" =>
                $quantity,

            ":subtotal" =>
                $item["price"] * $quantity
        ]);
    }

    $pdo->commit();

    /*
    |--------------------------------------------------------------------------
    | Clear cart
    |--------------------------------------------------------------------------
    */

    $_SESSION["cart"] = [];

    $_SESSION["success"] =
        "Your order has been placed successfully.";

    header(
        "Location: order_success.php?id=" .
        $order_id
    );

    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION["error"] =
        "Something went wrong while placing your order.";

    header("Location: ../checkout.php");
    exit;
}
