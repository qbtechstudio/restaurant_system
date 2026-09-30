<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

//  Only POST allowed

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}

$id = (int)($_POST["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["error"] = "Invalid order.";
    header("Location: orders.php");
    exit;
}

// Check Order

$stmt = $pdo->prepare(
    "SELECT id FROM orders WHERE id = :id LIMIT 1"
);

$stmt->execute([
    ":id" => $id
]);

$order = $stmt->fetch();

if (!$order) {
    $_SESSION["error"] = "Order not found.";
    header("Location: orders.php");
    exit;
}

// Delete Order


try {
    $pdo->beginTransaction();

    //  Delete order items first

    $stmt = $pdo->prepare(
        "DELETE FROM order_items WHERE order_id = :order_id"
    );

    $stmt->execute([
        ":order_id" => $id
    ]);


    //  Delete order

    $stmt = $pdo->prepare(
        "DELETE FROM orders WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);


    $pdo->commit();

    $_SESSION["success"] =
        "Order deleted successfully.";
} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION["error"] =
        "Unable to delete the order.";
}

header("Location: orders.php");
exit;