<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../menu.php");
    exit;
}

$menu_item_id = filter_input(INPUT_POST, "menu_item_id", FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT);

if (!$menu_item_id || !$quantity || $quantity < 1) {
    $_SESSION["error"] = "Invalid item or quantity.";
    header("Location: ../menu.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Check that menu item exists and is available
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id, name, price, status
    FROM menu_items
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $menu_item_id
]);

$item = $stmt->fetch();

if (!$item) {
    $_SESSION["error"] = "Menu item not found.";
    header("Location: ../menu.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Check item availability
|--------------------------------------------------------------------------
*/

if (isset($item["status"]) && !$item["status"]) {
    $_SESSION["error"] = "This item is currently unavailable.";
    header("Location: ../menu.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Create cart
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["cart"]) || !is_array($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

/*
|--------------------------------------------------------------------------
| Add quantity
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["cart"][$menu_item_id])) {
    $_SESSION["cart"][$menu_item_id] += $quantity;
} else {
    $_SESSION["cart"][$menu_item_id] = $quantity;
}

$_SESSION["success"] = $item["name"] . " added to cart.";

header("Location: ../cart.php");
exit;