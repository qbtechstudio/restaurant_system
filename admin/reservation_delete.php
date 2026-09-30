<?php

require_once "admin_auth_check.php";
require_once "../config/database.php";

// ONLY ALLOW POST REQUEST

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: reservations.php");
    exit;
}

// GET RESERVATION ID

$id = (int) ($_POST["id"] ?? 0);

if ($id <= 0) {
    $_SESSION["error"] = "Invalid reservation.";
    header("Location: reservations.php");
    exit;
}

// CHECK RESERVATION EXISTS

$stmt = $pdo->prepare(
    "SELECT id FROM reservations WHERE id = :id LIMIT 1"
);

$stmt->execute([
    ":id" => $id
]);

$reservation = $stmt->fetch();

if (!$reservation) {
    $_SESSION["error"] = "Reservation not found.";
    header("Location: reservations.php");
    exit;
}

// DELETE RESERVATION

$stmt = $pdo->prepare(
    "DELETE FROM reservations WHERE id = :id"
);

$stmt->execute([
    ":id" => $id
]);

// SUCCESS

$_SESSION["success"] = "Reservation deleted successfully.";
header("Location: reservations.php");
exit;