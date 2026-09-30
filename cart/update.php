<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../cart.php");
    exit;
}

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

$quantities = $_POST["quantity"] ?? [];

foreach ($quantities as $item_id => $quantity) {

    $item_id = (int) $item_id;
    $quantity = (int) $quantity;

    if ($quantity <= 0) {
        unset($_SESSION["cart"][$item_id]);
    } else {
        $_SESSION["cart"][$item_id] = $quantity;
    }
}

$_SESSION["success"] = "Cart updated successfully.";

header("Location: ../cart.php");
exit;